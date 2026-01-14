<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ApiTableController extends Controller
{
    /**
     * Get all tables with availability status for a specific date
     */
    public function index(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        
        $tables = Table::where('is_active', true)
            ->orderBy('number')
            ->get();
        
        // Get booked table IDs for the requested date
        $bookedTableIds = Reservation::where('date', $date)
            ->whereIn('status', ['pending', 'accepted'])
            ->pluck('table_id')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'date' => $date,
            'tables' => $tables->map(function ($table) use ($bookedTableIds) {
                $isBooked = in_array($table->id, $bookedTableIds);
                return [
                    'id' => $table->id,
                    'number' => $table->number,
                    'capacity' => $table->capacity,
                    'zone' => $table->zone,
                    'shape' => $table->shape,
                    'is_premium' => $table->is_premium,
                    'status' => $isBooked ? 'booked' : 'available',
                ];
            }),
        ]);
    }

    /**
     * Check if a specific table is available
     */
    public function checkAvailability(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $table = Table::find($id);
        
        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found',
            ], 404);
        }

        $isBooked = Reservation::where('table_id', $id)
            ->where('date', $request->date)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        return response()->json([
            'success' => true,
            'table' => [
                'id' => $table->id,
                'number' => $table->number,
                'capacity' => $table->capacity,
            ],
            'available' => !$isBooked,
        ]);
    }
}
