<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
class ApiCartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = CartItem::where('user_id', $request->user()->id)
            ->with('menu')
            ->get();
        $total = $cartItems->sum(function ($item) {
            $price = $item->menu->price_online ?? $item->menu->price;
            return $item->menu ? $item->quantity * $price : 0;
        });
        return response()->json([
            'success' => true,
            'items' => $cartItems->map(function ($item) {
                $price = $item->menu->price_online ?? $item->menu->price;
                return [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'menu_name' => $item->menu?->name,
                    'menu_image' => $item->menu?->image_url,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->menu ? $item->quantity * $price : 0,
                ];
            }),
            'total' => $total,
            'item_count' => $cartItems->sum('quantity'),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);
        $existingItem = CartItem::where('user_id', $request->user()->id)
            ->where('menu_id', $request->menu_id)
            ->first();
        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
            $cartItem = $existingItem;
        } else {
            $cartItem = CartItem::create([
                'user_id' => $request->user()->id,
                'menu_id' => $request->menu_id,
                'quantity' => $request->quantity,
            ]);
        }
        $cartItem->load('menu');
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'item' => [
                'id' => $cartItem->id,
                'menu_id' => $cartItem->menu_id,
                'menu_name' => $cartItem->menu?->name,
                'quantity' => $cartItem->quantity,
            ],
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'item' => [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
            ],
        ]);
    }
    public function destroy(Request $request, $id)
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }
        $cartItem->delete();
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }
}