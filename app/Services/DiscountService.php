<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiscountService
{
    public static function applyDiscounts($items, $voucherCode = null, $user = null)
    {
        $now = now();
        $currentTime = $now->format('H:i:s');
        $currentDayOfWeek = $now->dayOfWeek; // 0 (Sunday) - 6 (Saturday)

        // Find active valid discounts
        $query = DB::table('discounts')
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('usage_count < usage_limit');
            });

        $activeDiscounts = $query->get();

        // Filter by days_of_week and happy_hour
        $activeDiscounts = $activeDiscounts->filter(function($d) use ($currentDayOfWeek, $currentTime) {
            // Check day
            if ($d->days_of_week) {
                $days = json_decode($d->days_of_week, true);
                if (!in_array($currentDayOfWeek, $days)) {
                    return false;
                }
            }
            
            // Check happy hour
            if ($d->happy_hour_start && $d->happy_hour_end) {
                if ($currentTime < $d->happy_hour_start || $currentTime > $d->happy_hour_end) {
                    return false;
                }
            }

            return true;
        });

        $subtotal = 0;
        $orderItems = [];
        $appliedDiscounts = [];

        // 1. Process items and apply item-level discounts
        foreach ($items as $item) {
            $menu = DB::table('menus')->find($item['menu_id']);
            if (!$menu) continue;

            $basePrice = $menu->price;
            
            // Calculate modifier price
            $modifierPrice = 0;
            $modifiers = $item['modifiers'] ?? [];
            if (is_array($modifiers)) {
                foreach ($modifiers as $mod) {
                    if (isset($mod['price'])) {
                        $modifierPrice += floatval($mod['price']);
                    }
                }
            }

            // Total price for one unit before discount
            $priceBeforeDiscount = $basePrice + $modifierPrice;
            $qty = $item['quantity'];
            
            // Apply Item-Level discount if any (Discount usually applies to base price or total item price? 
            // We'll apply it to the base price + modifier price)
            $itemDiscount = $activeDiscounts->where('scope', 'item')
                ->where('menu_id', $menu->id)
                ->whereNull('voucher_code') // typically item level shouldn't require voucher, but just in case
                ->first();

            $discountAmount = 0;
            if ($itemDiscount) {
                if ($itemDiscount->type === 'fixed') {
                    $discountAmount = $itemDiscount->value;
                } else {
                    $discountAmount = $priceBeforeDiscount * ($itemDiscount->value / 100);
                }
                
                // Discount cannot exceed price
                if ($discountAmount > $priceBeforeDiscount) {
                    $discountAmount = $priceBeforeDiscount;
                }
                
                if (!isset($appliedDiscounts[$itemDiscount->id])) {
                    $appliedDiscounts[$itemDiscount->id] = $itemDiscount;
                }
            }

            $priceAfterDiscount = $priceBeforeDiscount - $discountAmount;
            $itemSubtotal = $priceAfterDiscount * $qty;
            $subtotal += $itemSubtotal;
            
            $orderItems[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'original_price' => $priceBeforeDiscount,
                'price' => $priceAfterDiscount,
                'quantity' => $qty,
                'subtotal' => $itemSubtotal,
                'modifiers' => is_array($modifiers) ? json_encode($modifiers) : null,
            ];
        }

        $subtotalBeforeOrderDiscount = $subtotal;
        $orderDiscountAmount = 0;
        $bestOrderDiscount = null;

        // 2. Find Best Order-Level Discount
        $orderDiscounts = $activeDiscounts->where('scope', 'order');
        
        // If voucher provided, try to find the voucher
        $voucherDiscount = null;
        if ($voucherCode) {
            $voucherDiscount = $orderDiscounts->where('voucher_code', $voucherCode)->first();
            if (!$voucherDiscount) {
                // Return an error if voucher invalid
                throw new \Exception('Kode voucher tidak valid, sudah mencapai limit, atau di luar masa berlaku.');
            }

            // Check user_id ownership
            if ($voucherDiscount->user_id && (!$user || $voucherDiscount->user_id !== $user->id)) {
                throw new \Exception('Voucher ini tidak valid untuk akun Anda.');
            }
            
            // Check max_usage_per_user
            if ($user && $voucherDiscount->max_usage_per_user > 0) {
                $userUsageCount = DB::table('discount_usages')
                    ->where('discount_id', $voucherDiscount->id)
                    ->where('user_id', $user->id)
                    ->count();
                if ($userUsageCount >= $voucherDiscount->max_usage_per_user) {
                    throw new \Exception('Anda sudah mencapai batas penggunaan voucher ini.');
                }
            }

            $bestOrderDiscount = $voucherDiscount;
        } else {
            // Find best auto discount
            $bestDeduction = -1;
            foreach ($orderDiscounts->whereNull('voucher_code') as $d) {
                $deduction = $d->type === 'fixed' ? $d->value : ($subtotalBeforeOrderDiscount * ($d->value / 100));
                if ($deduction > $bestDeduction) {
                    $bestDeduction = $deduction;
                    $bestOrderDiscount = $d;
                }
            }
        }

        if ($bestOrderDiscount) {
            if ($bestOrderDiscount->type === 'free_item' && $bestOrderDiscount->free_menu_id) {
                $orderDiscountAmount = 0;
                $freeMenu = DB::table('menus')->find($bestOrderDiscount->free_menu_id);
                if ($freeMenu) {
                    $orderItems[] = [
                        'menu_id' => $freeMenu->id,
                        'menu_name' => $freeMenu->name . ' (Voucher Gratis)',
                        'quantity' => 1,
                        'price' => 0,
                        'original_price' => $freeMenu->price,
                        'discount_amount' => $freeMenu->price,
                        'subtotal' => 0,
                        'total' => 0,
                        'modifiers' => null,
                        'is_promo' => true,
                        'promo_name' => $bestOrderDiscount->name
                    ];
                }
            } else if ($bestOrderDiscount->type === 'fixed') {
                $orderDiscountAmount = $bestOrderDiscount->value;
            } else {
                $orderDiscountAmount = $subtotalBeforeOrderDiscount * ($bestOrderDiscount->value / 100);
            }

            // Cannot exceed subtotal
            if ($orderDiscountAmount > $subtotalBeforeOrderDiscount) {
                $orderDiscountAmount = $subtotalBeforeOrderDiscount;
            }
            
            $appliedDiscounts[$bestOrderDiscount->id] = $bestOrderDiscount;
        }

        // Apply Promo Buy X Get Y for free items
        $activePromos = \App\Models\Promo::where('is_active', true)
            ->where('type', 'buy_x_get_y')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $itemCounts = [];
        foreach ($orderItems as $item) {
            $itemCounts[$item['menu_id']] = ($itemCounts[$item['menu_id']] ?? 0) + $item['quantity'];
        }

        foreach ($activePromos as $promo) {
            if (isset($itemCounts[$promo->buy_menu_id])) {
                $qtyBought = $itemCounts[$promo->buy_menu_id];
                if ($qtyBought >= $promo->buy_quantity) {
                    $multiplier = floor($qtyBought / $promo->buy_quantity);
                    $qtyToGive = $multiplier * $promo->get_quantity;

                    if ($promo->get_type === 'free') {
                        $orderItems[] = [
                            'menu_id' => $promo->get_menu_id,
                            'name' => $promo->getMenu->name . ' (Promo)',
                            'quantity' => $qtyToGive,
                            'price' => 0,
                            'original_price' => 0,
                            'discount_amount' => 0,
                            'total' => 0,
                            'modifiers' => [],
                            'is_promo' => true,
                            'promo_name' => $promo->name
                        ];
                    }
                }
            }
        }

        // Calculate order-level discounts (from subtotal)
        $subtotalBeforeOrderDiscount = array_sum(array_column($orderItems, 'total'));

        $subtotalAfterDiscount = $subtotalBeforeOrderDiscount - $orderDiscountAmount;

        // Apply Membership Tier Discount on top
        if ($user && $user->tier && $user->tier->discount_percent > 0) {
            $tierDiscountAmount = $subtotalAfterDiscount * ($user->tier->discount_percent / 100);
            $orderDiscountAmount += $tierDiscountAmount;
            $subtotalAfterDiscount -= $tierDiscountAmount;
        }

        $tax = $subtotalAfterDiscount * 0.10;
        $total = $subtotalAfterDiscount + $tax;

        return [
            'orderItems' => $orderItems,
            'subtotal_before_discount' => $subtotalBeforeOrderDiscount,
            'discount_amount' => $orderDiscountAmount,
            'discount_id' => $bestOrderDiscount ? $bestOrderDiscount->id : null,
            'tax' => $tax,
            'total' => $total,
            'applied_discounts' => array_values($appliedDiscounts)
        ];
    }
}
