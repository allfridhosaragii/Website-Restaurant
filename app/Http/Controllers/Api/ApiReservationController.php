<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ApiReservationController extends Controller
{
    /**
     * List user's reservations
     */
    public function index(Request $request)
    {
        $reservations = Reservation::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'date' => $reservation->date,
                    'time' => $reservation->time,
                    'guests' => $reservation->guests,
                    'name' => $reservation->name,
                    'phone' => $reservation->phone,
                    'notes' => $reservation->notes,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at,
                ];
            }),
        ]);
    }

    /**
     * Create new reservation
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'guests' => 'required|integer|min:1|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_id' => 'required|integer|exists:tables,id',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Check if table is already booked for that date
        $existingReservation = Reservation::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();
            
        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Meja ini sudah dipesan untuk tanggal tersebut',
            ], 422);
        }
        
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
            'name' => $request->name,
            'phone' => $request->phone,
            'table_id' => $request->table_id,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully',
            'reservation' => [
                'id' => $reservation->id,
                'date' => $reservation->date,
                'time' => $reservation->time,
                'guests' => $reservation->guests,
                'table_id' => $reservation->table_id,
                'status' => $reservation->status,
            ],
        ], 201);
    }

    /**
     * Get single reservation detail
     */
    public function show(Request $request, $id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'date' => $reservation->date,
                'time' => $reservation->time,
                'guests' => $reservation->guests,
                'name' => $reservation->name,
                'phone' => $reservation->phone,
                'notes' => $reservation->notes,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
            ],
        ]);
    }
}
