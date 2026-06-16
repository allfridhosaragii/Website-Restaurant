<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * Show shift history (admin only).
     */
    public function index(Request $request)
    {
        $query = Shift::with('user')->orderBy('clock_in', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('clock_in', $request->date);
        }

        $shifts = $query->paginate(20);
        $staff = User::whereIn('role', ['admin', 'cashier', 'waiter', 'manager'])
            ->orWhere('is_admin', true)
            ->orderBy('name')
            ->get();

        return view('admin.shifts.index', compact('shifts', 'staff'));
    }

    /**
     * Start a new shift (POST).
     */
    public function start(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        // Check if user already has an active shift
        $activeShift = Shift::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki shift aktif. Tutup shift tersebut terlebih dahulu.',
            ], 422);
        }

        $shift = Shift::create([
            'user_id'      => Auth::id(),
            'clock_in'     => now(),
            'opening_cash' => $request->opening_cash,
            'status'       => 'active',
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Shift dimulai! Selamat bekerja.',
            'shift_id'  => $shift->id,
            'clock_in'  => $shift->clock_in->format('H:i'),
        ]);
    }

    /**
     * Get current active shift for the authenticated user (GET).
     */
    public function activeShift()
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (!$shift) {
            return response()->json(['active' => false]);
        }

        $expectedCash = $shift->calculateExpectedCash();

        return response()->json([
            'active'         => true,
            'shift_id'       => $shift->id,
            'clock_in'       => $shift->clock_in->format('H:i'),
            'duration'       => $shift->duration,
            'total_orders'   => $shift->total_orders,
            'total_revenue'  => $shift->total_revenue,
            'opening_cash'   => $shift->opening_cash,
            'expected_cash'  => $expectedCash,
        ]);
    }

    /**
     * Close an active shift (POST).
     */
    public function close(Request $request)
    {
        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $expectedCash    = $shift->calculateExpectedCash();
        $cashDifference  = $request->closing_cash - $expectedCash;

        $shift->update([
            'clock_out'       => now(),
            'closing_cash'    => $request->closing_cash,
            'expected_cash'   => $expectedCash,
            'cash_difference' => $cashDifference,
            'notes'           => $request->notes,
            'status'          => 'closed',
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'Shift berhasil ditutup.',
            'expected_cash'   => $expectedCash,
            'closing_cash'    => $request->closing_cash,
            'cash_difference' => $cashDifference,
            'duration'        => $shift->fresh()->duration,
        ]);
    }

    /**
     * Show detail of a single shift (admin).
     */
    public function show(Shift $shift)
    {
        $shift->load('user');
        $orders = DB::table('orders')
            ->where('shift_id', $shift->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.shifts.show', compact('shift', 'orders'));
    }
}
