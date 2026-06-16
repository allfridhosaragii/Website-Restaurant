<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateMembershipTier implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;

        if (!$order->user_id) {
            return;
        }

        $user = User::find($order->user_id);
        if (!$user || $user->is_admin) {
            return;
        }

        // Add order total to user's total spent
        $user->total_spent += $order->total_price;

        // Check for tier upgrade
        // Assuming MembershipTier has a string column 'name' (Bronze, Silver, Gold, Platinum)
        // and 'min_spent' for the required amount.
        
        $eligibleTier = MembershipTier::where('min_spent', '<=', $user->total_spent)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($eligibleTier && strtolower($user->membership_tier) !== strtolower($eligibleTier->name)) {
            // Find current tier to see if it's an upgrade
            $currentTier = $user->tier;
            
            // Only upgrade if the new tier has a higher sort_order or if no current tier exists
            if (!$currentTier || $eligibleTier->sort_order > $currentTier->sort_order) {
                $user->membership_tier = strtolower($eligibleTier->name);
                $user->tier_upgraded_at = now();
                
                Log::channel('single')->info("User {$user->name} upgraded to {$eligibleTier->name} tier!");
                
                // TODO: Send notification to user about their upgrade
            }
        }

        $user->save();
    }
}
