<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class ApiMenuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = \DB::table('menus')->where('is_available', true);
            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }
            $sortBy = $request->get('sort', 'name');
            $sortOrder = $request->get('order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            $menus = $query->get();
            $favorites = [];
            if ($request->user()) {
                $favorites = \App\Models\Favorite::where('user_id', $request->user()->id)
                    ->pluck('menu_id')
                    ->toArray();
            }
            $categories = \DB::table('menus')
                ->where('is_available', true)
                ->select('category')
                ->distinct()
                ->pluck('category');
            return response()->json([
                'success' => true,
                'menus' => $menus->map(function ($menu) use ($favorites) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'description' => $menu->description,
                        'price' => $menu->price,
                        'category' => $menu->category,
                        'image_url' => $menu->image_url,
                        'is_available' => $menu->is_available,
                        'is_favorite' => in_array($menu->id, $favorites),
                    ];
                }),
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    public function show($slug)
    {
        $menu = \DB::table('menus')->where('slug', $slug)->first();
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'menu' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'description' => $menu->description,
                'price' => $menu->price,
                'category' => $menu->category,
                'image_url' => $menu->image_url,
                'is_available' => $menu->is_available,
            ],
        ]);
    }
}