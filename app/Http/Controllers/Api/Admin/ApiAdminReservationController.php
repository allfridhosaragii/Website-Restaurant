<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAdminReservationController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservations = \DB::table('reservations')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
            
        foreach ($reservations as $res) {
            $res->user = \DB::table('users')->where('id', $res->user_id)->first();
        }

        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,completed,cancelled'
        ]);

        \DB::table('reservations')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Reservation status updated']);
    }
}
