<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReservationCalendarController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number')->get();
        return view('admin.reservations.calendar', compact('tables'));
    }

    public function data(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $query = Reservation::with(['user', 'table']);

        if ($start && $end) {
            $query->whereBetween('date', [
                Carbon::parse($start)->format('Y-m-d'),
                Carbon::parse($end)->format('Y-m-d')
            ]);
        }

        $reservations = $query->get();

        $events = $reservations->map(function ($res) {
            // Build start datetime
            $startDateTime = Carbon::parse($res->date->format('Y-m-d') . ' ' . $res->time);
            
            // Build end datetime (default 2 hours if end_time not set)
            if ($res->end_time) {
                $endDateTime = Carbon::parse($res->end_time);
            } else {
                $endDateTime = $startDateTime->copy()->addHours(2);
            }

            // Colors based on status
            $color = '#ffc107'; // yellow for pending
            if ($res->status === 'confirmed' || $res->status === 'accepted') $color = '#198754'; // green
            if ($res->status === 'checked_in') $color = '#0dcaf0'; // cyan
            if ($res->status === 'completed') $color = '#0d6efd'; // blue
            if ($res->status === 'cancelled' || $res->status === 'rejected') $color = '#dc3545'; // red
            if ($res->status === 'no_show') $color = '#6c757d'; // gray

            return [
                'id' => $res->id,
                'title' => ($res->table ? 'Meja ' . $res->table->number : 'Unassigned') . ' - ' . $res->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'color' => $color,
                'extendedProps' => [
                    'name' => $res->name,
                    'phone' => $res->phone,
                    'guests' => $res->guests,
                    'table_id' => $res->table_id,
                    'status' => $res->status,
                    'deposit_amount' => $res->deposit_amount,
                    'deposit_status' => $res->deposit_status,
                ]
            ];
        });

        return response()->json($events);
    }

    public function checkConflict($tableId, $start, $end, $excludeId = null)
    {
        $query = Reservation::where('table_id', $tableId)
            ->whereNotIn('status', ['cancelled', 'rejected', 'no_show', 'completed']);
            
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Check overlapping logic (start1 < end2 AND end1 > start2)
        $conflicts = $query->get()->filter(function ($res) use ($start, $end) {
            $resStart = Carbon::parse($res->date->format('Y-m-d') . ' ' . $res->time);
            $resEnd = $res->end_time ? Carbon::parse($res->end_time) : $resStart->copy()->addHours(2);
            
            return ($start < $resEnd && $end > $resStart);
        });

        return $conflicts->isNotEmpty();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'table_id' => 'nullable|exists:tables,id'
        ]);

        $reservation = Reservation::findOrFail($id);
        
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $tableId = $request->table_id ?? $reservation->table_id;

        if ($tableId) {
            if ($this->checkConflict($tableId, $start, $end, $reservation->id)) {
                return response()->json(['error' => 'Meja sudah dipesan di rentang waktu tersebut.'], 422);
            }
        }

        $reservation->update([
            'date' => $start->format('Y-m-d'),
            'time' => $start->format('H:i:s'),
            'end_time' => $end,
            'table_id' => $tableId
        ]);

        return response()->json(['success' => true]);
    }

    public function checkIn(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        if ($reservation->status === 'checked_in') {
            return back()->with('error', 'Reservasi sudah check-in.');
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_in_by' => auth()->id()
            ]);

            if ($reservation->table_id) {
                Table::where('id', $reservation->table_id)->update(['status' => 'occupied']);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'reservation_checkin',
                'description' => "Check-in reservasi ID #{$reservation->id} a.n. {$reservation->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Berhasil check-in reservasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }
}
