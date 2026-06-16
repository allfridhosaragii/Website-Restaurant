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
        $query = CartItem::with('menu');
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', request()->session()->getId());
        }
        $cartItems = $query->get();
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

            $query = CartItem::where('menu_id', $menuId)->where('signature', $signature);
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $request->session()->getId());
            }
            $cartItem = $query->first();
            
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
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'session_id' => Auth::check() ? null : $request->session()->getId(),
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'signature' => $signature,
                    'modifiers' => $modifiers
                ]);
            }
            
            $countQuery = CartItem::query();
            if (Auth::check()) {
                $countQuery->where('user_id', Auth::id());
            } else {
                $countQuery->where('session_id', $request->session()->getId());
            }
            $count = $countQuery->sum('quantity');

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
        $query = CartItem::where('id', $id);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', request()->session()->getId());
        }
        $cartItem = $query->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
        $cartItem->delete();
        
        $countQuery = CartItem::query();
        if (Auth::check()) {
            $countQuery->where('user_id', Auth::id());
        } else {
            $countQuery->where('session_id', request()->session()->getId());
        }
        $count = $countQuery->sum('quantity');

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
        
        $query = CartItem::where('id', $id);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $request->session()->getId());
        }
        $cartItem = $query->first();

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
        
        $countQuery = CartItem::query();
        if (Auth::check()) {
            $countQuery->where('user_id', Auth::id());
        } else {
            $countQuery->where('session_id', $request->session()->getId());
        }
        $count = $countQuery->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
            'item' => $cartItem->load('menu'),
            'count' => $count
        ]);
    }
    public function count()
    {
        $countQuery = CartItem::query();
        if (Auth::check()) {
            $countQuery->where('user_id', Auth::id());
        } else {
            $countQuery->where('session_id', request()->session()->getId());
        }
        $count = $countQuery->sum('quantity');
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    public function clear()
    {
        $query = CartItem::query();
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', request()->session()->getId());
        }
        $query->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'count' => 0
        ]);
    }
}