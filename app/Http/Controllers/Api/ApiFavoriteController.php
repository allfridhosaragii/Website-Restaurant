<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
class ApiFavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with('menu')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'favorites' => $favorites->map(function ($favorite) {
                return [
                    'id' => $favorite->id,
                    'menu_id' => $favorite->menu_id,
                    'menu' => $favorite->menu ? [
                        'id' => $favorite->menu->id,
                        'name' => $favorite->menu->name,
                        'slug' => $favorite->menu->slug,
                        'description' => $favorite->menu->description,
                        'price' => $favorite->menu->price,
                        'category' => $favorite->menu->category,
                        'image_url' => $favorite->menu->image_url,
                    ] : null,
                    'created_at' => $favorite->created_at,
                ];
            }),
            'count' => $favorites->count(),
        ]);
    }
    public function toggle(Request $request, $menuId)
    {
        $user = $request->user();
        $menu = \DB::table('menus')->where('id', $menuId)->first();
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
            ], 404);
        }
        $favorite = Favorite::where('user_id', $user->id)
            ->where('menu_id', $menuId)
            ->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'status' => 'removed',
                'message' => 'Removed from favorites',
                'is_favorite' => false,
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'menu_id' => $menuId,
            ]);
            return response()->json([
                'success' => true,
                'status' => 'added',
                'message' => 'Added to favorites',
                'is_favorite' => true,
            ]);
        }
    }
}