<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
class AdminInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query()
            ->select('menus.*')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->orderBy('categories.name')
            ->orderBy('menus.name');
        if ($request->category && $request->category !== 'all') {
            $query->where('categories.name', $request->category);
        }
        if ($request->status === 'available') {
            $query->where('is_available', true)->where('daily_stock', '>', 0);
        } elseif ($request->status === 'low') {
            $query->where('daily_stock', '<=', 10)->where('daily_stock', '>', 0);
        } elseif ($request->status === 'out') {
            $query->where('daily_stock', '<=', 0);
        }
        $menus = $query->get();
        $categories = \App\Models\Category::pluck('name');
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->where('daily_stock', '>', 0)->count();
        $lowStockMenus = Menu::where('daily_stock', '<=', 10)->where('daily_stock', '>', 0)->count();
        $outOfStockMenus = Menu::where('daily_stock', '<=', 0)->count();
        return view('admin.inventory.index', compact(
            'menus',
            'categories',
            'totalMenus',
            'availableMenus',
            'lowStockMenus',
            'outOfStockMenus'
        ));
    }
    public function updateStock(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $request->validate([
            'daily_stock' => 'required|integer|min:0',
            'max_daily_stock' => 'required|integer|min:1',
        ]);
        $menu->update([
            'daily_stock' => $request->daily_stock,
            'max_daily_stock' => $request->max_daily_stock,
            'stock_updated_at' => now()->toDateString(),
        ]);
        if ($menu->daily_stock > 0 && !$menu->is_available) {
            $menu->update(['is_available' => true]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui',
            'menu' => $menu->fresh()
        ]);
    }
    public function adjustStock(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $action = $request->action;
        if ($action === 'increase') {
            $menu->increaseStock($request->quantity ?? 1);
        } elseif ($action === 'decrease') {
            $menu->decreaseStock($request->quantity ?? 1);
        } elseif ($action === 'reset') {
            $menu->resetDailyStock();
        }
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui',
            'menu' => $menu->fresh()
        ]);
    }
    public function toggleAvailability($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = !$menu->is_available;
        $menu->save();
        return response()->json([
            'success' => true,
            'message' => $menu->is_available ? 'Menu diaktifkan' : 'Menu dinonaktifkan',
            'is_available' => $menu->is_available
        ]);
    }
    public function resetAllStock()
    {
        Menu::query()->update([
            'daily_stock' => \DB::raw('max_daily_stock'),
            'is_available' => true,
            'stock_updated_at' => now()->toDateString(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Semua stok berhasil di-reset'
        ]);
    }
    public function getMenusApi()
    {
        $menus = Menu::with('category')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->orderBy('categories.name')
            ->orderBy('menus.name')
            ->select('menus.*')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $menus->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'category' => $menu->category ? $menu->category->name : '-',
                    'daily_stock' => $menu->daily_stock,
                    'max_daily_stock' => $menu->max_daily_stock,
                    'is_available' => $menu->is_available,
                    'stock_status' => $menu->stock_status,
                    'stock_color' => $menu->stock_color,
                ];
            })
        ]);
    }
}