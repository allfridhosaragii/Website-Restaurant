<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get all cart items for the authenticated user.
     */
    public function index()
    {
        $cartItems = CartItem::with('menu')
            ->where('user_id', Auth::id())
            ->get();
        
        $total = $cartItems->sum(function ($item) {
            return $item->menu->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity')
        ]);
    }

    /**
     * Add item to cart (AJAX).
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'quantity' => 'integer|min:1'
            ]);

            $menuId = $request->menu_id;
            $quantity = $request->quantity ?? 1;

            // Check if item already exists in cart
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('menu_id', $menuId)
                ->first();

            if ($cartItem) {
                // Update quantity
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'user_id' => Auth::id(),
                    'menu_id' => $menuId,
                    'quantity' => $quantity
                ]);
            }

            // Get updated count
            $count = CartItem::where('user_id', Auth::id())->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'item' => $cartItem->load('menu'),
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart (AJAX).
     */
    public function remove($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        $cartItem->delete();

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'count' => $count
        ]);
    }

    /**
     * Update item quantity (AJAX).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
            'item' => $cartItem->load('menu'),
            'count' => $count
        ]);
    }

    /**
     * Get cart item count (AJAX).
     */
    public function count()
    {
        $count = Auth::check() 
            ? CartItem::where('user_id', Auth::id())->sum('quantity')
            : 0;

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Clear all items from cart (AJAX).
     */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'count' => 0
        ]);
    }
}
