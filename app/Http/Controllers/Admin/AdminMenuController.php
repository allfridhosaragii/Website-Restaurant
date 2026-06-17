<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;
class AdminMenuController extends Controller
{
    const DEFAULT_IMAGE = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';
    public function __construct()
    {
        try {
            Configuration::instance('cloudinary://474775265674185:pI64ZhoDmEy2fhevZp-kqzzVuCE@dh9ysyfit');
        } catch (\Exception $e) {
        }
    }
    public function index()
    {
        $menus = DB::table('menus')
            ->leftJoin('categories', 'menus.category_id', '=', 'categories.id')
            ->select('menus.*', 'categories.name as category_name')
            ->orderBy('menus.created_at', 'desc')
            ->get();
        $lowStockMenus = DB::table('menus')->whereRaw('stock <= min_stock')->get();
        return view('admin.menus.index', compact('menus', 'lowStockMenus'));
    }
    public function create()
    {
        return view('admin.menus.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_online' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ], [
            'name.unique' => 'Menu dengan nama tersebut sudah ada. Silakan gunakan nama lain.',
        ]);
        $imageUrl = $request->image_url;
        if (empty($imageUrl)) {
            $imageUrl = self::DEFAULT_IMAGE;
        }
        $categorySlug = Str::slug($request->category);
        $category = DB::table('categories')->where('slug', $categorySlug)->first();
        if (!$category) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $request->category,
                'slug' => $categorySlug,
                'color' => '#10B981',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $categoryId = $category->id;
        }

        $menuId = DB::table('menus')->insertGetId([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'price_online' => $request->price_online,
            'category_id' => $categoryId,
            'image_url' => $imageUrl,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
            'is_available' => $request->boolean('is_available', true),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Handle Modifiers
        $submittedModifiers = $request->input('modifiers', []);
        foreach ($submittedModifiers as $mIndex => $modData) {
            $modifier = \App\Models\MenuModifier::create([
                'menu_id' => $menuId,
                'name' => $modData['name'],
                'type' => $modData['type'],
                'is_required' => !empty($modData['is_required']),
                'max_select' => $modData['max_select'] ?? 1,
                'sort_order' => $mIndex
            ]);

            $submittedOptions = $modData['options'] ?? [];
            foreach ($submittedOptions as $oIndex => $optData) {
                \App\Models\MenuModifierOption::create([
                    'menu_modifier_id' => $modifier->id,
                    'name' => $optData['name'],
                    'price' => $optData['price'] ?? 0,
                    'sort_order' => $oIndex
                ]);
            }
        }

        return redirect('/admin/menus')->with('success', 'Menu berhasil ditambahkan!');
    }
    public function edit($slug)
    {
        $menu = \App\Models\Menu::with(['modifiers.options'])->where('slug', $slug)->first();
        if (!$menu) {
            abort(404);
        }
        return view('admin.menus.edit', compact('menu'));
    }
    public function update(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_online' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);
        $menu = DB::table('menus')->where('slug', $slug)->first();
        if (!$menu) {
            abort(404);
        }
        $imageUrl = $request->image_url ?: $menu->image_url;
        $categorySlug = Str::slug($request->category);
        $category = DB::table('categories')->where('slug', $categorySlug)->first();
        if (!$category) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $request->category,
                'slug' => $categorySlug,
                'color' => '#10B981',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $categoryId = $category->id;
        }

        DB::table('menus')->where('id', $menu->id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'price_online' => $request->price_online,
            'category_id' => $categoryId,
            'image_url' => $imageUrl,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
            'is_available' => $request->boolean('is_available', true),
            'updated_at' => now(),
        ]);

        // Handle Modifiers
        $submittedModifiers = $request->input('modifiers', []);
        $submittedModifierIds = array_filter(array_column($submittedModifiers, 'id'));

        // Delete modifiers that were removed
        \App\Models\MenuModifier::where('menu_id', $menu->id)
            ->when(!empty($submittedModifierIds), function($q) use ($submittedModifierIds) {
                return $q->whereNotIn('id', $submittedModifierIds);
            })->delete();

        foreach ($submittedModifiers as $mIndex => $modData) {
            $modifier = \App\Models\MenuModifier::updateOrCreate(
                ['id' => $modData['id'] ?? null, 'menu_id' => $menu->id],
                [
                    'name' => $modData['name'],
                    'type' => $modData['type'],
                    'is_required' => !empty($modData['is_required']),
                    'max_select' => $modData['max_select'] ?? 1,
                    'sort_order' => $mIndex
                ]
            );

            $submittedOptions = $modData['options'] ?? [];
            $submittedOptionIds = array_filter(array_column($submittedOptions, 'id'));

            // Delete options that were removed
            \App\Models\MenuModifierOption::where('menu_modifier_id', $modifier->id)
                ->when(!empty($submittedOptionIds), function($q) use ($submittedOptionIds) {
                    return $q->whereNotIn('id', $submittedOptionIds);
                })->delete();

            foreach ($submittedOptions as $oIndex => $optData) {
                \App\Models\MenuModifierOption::updateOrCreate(
                    ['id' => $optData['id'] ?? null, 'menu_modifier_id' => $modifier->id],
                    [
                        'name' => $optData['name'],
                        'price' => $optData['price'] ?? 0,
                        'sort_order' => $oIndex
                    ]
                );
            }
        }

        return redirect('/admin/menus')->with('success', 'Menu berhasil diperbarui!');
    }
    public function destroy($slug)
    {
        DB::table('menus')->where('slug', $slug)->delete();
        return redirect('/admin/menus')->with('success', 'Menu berhasil dihapus!');
    }
}