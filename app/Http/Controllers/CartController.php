<?php
namespace App\Http\Controllers;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('menu')
            ->where('user_id', Auth::id())
            ->get();
        $total = $cartItems->sum(function ($item) {
            $basePrice = $item->menu->price;
            $modifierPrice = 0;
            if (is_array($item->modifiers)) {
                foreach ($item->modifiers as $mod) {
                    if (isset($mod['price'])) {
                        $modifierPrice += $mod['price'];
                    }
                }
            }
            return ($basePrice + $modifierPrice) * $item->quantity;
        });
        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity')
        ]);
    }
    public function add(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'quantity' => 'integer|min:1'
            ]);
            $menuId = $request->menu_id;
            $quantity = $request->quantity ?? 1;
            
            $modifiers = $request->input('modifiers', []);
            // Sort modifiers to ensure consistent signature
            if (!empty($modifiers)) {
                // sort by modifier id or just json encode consistently
                // We'll just json_encode it for signature, maybe hashing it
                $signature = md5($menuId . json_encode($modifiers));
            } else {
                $signature = md5($menuId . '[]');
            }

            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('menu_id', $menuId)
                ->where('signature', $signature)
                ->first();
            
            $menu = Menu::find($menuId);
            if (!$menu) {
                throw new \Exception('Menu tidak ditemukan');
            }

            $totalRequestedQty = $quantity;
            if ($cartItem) {
                $totalRequestedQty += $cartItem->quantity;
            }

            if ($menu->stock < $totalRequestedQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Sisa stok: {$menu->stock}"
                ], 400);
            }

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'user_id' => Auth::id(),
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'signature' => $signature,
                    'modifiers' => $modifiers
                ]);
            }
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

        $menu = Menu::find($cartItem->menu_id);
        if ($menu && $menu->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Sisa stok: {$menu->stock}"
            ], 400);
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