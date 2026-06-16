<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Employee self-service check-in/check-out page.
     */
    public function index()
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        return view('attendance.index', compact('attendance', 'today'));
    }

    /**
     * Process check-in with selfie photo.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'required|string', // base64 image
        ]);

        $today = now()->toDateString();

        // Prevent double check-in
        if (Attendance::where('user_id', Auth::id())->where('date', $today)->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah check-in hari ini.'], 422);
        }

        // Save base64 photo
        $photoPath = $this->saveBase64Photo($request->photo, 'checkin_' . Auth::id() . '_' . $today);

        $checkInTime = now()->format('H:i:s');
        $status = $checkInTime > '08:00:00' ? 'late' : 'present';

        Attendance::create([
            'user_id'         => Auth::id(),
            'date'            => $today,
            'check_in'        => $checkInTime,
            'check_in_photo'  => $photoPath,
            'status'          => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Status: ' . ucfirst($status),
            'status'  => $status,
            'time'    => now()->format('H:i'),
        ]);
    }

    /**
     * Process check-out with selfie photo.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'photo' => 'required|string', // base64 image
        ]);

        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->whereNull('check_out')
            ->firstOrFail();

        $photoPath = $this->saveBase64Photo($request->photo, 'checkout_' . Auth::id() . '_' . $today);

        $attendance->update([
            'check_out'       => now()->format('H:i:s'),
            'check_out_photo' => $photoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'time'    => now()->format('H:i'),
        ]);
    }

    /**
     * Admin: view all attendance records.
     */
    public function adminIndex(Request $request)
    {
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('check_in', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('month')) {
            $query->whereYear('date', substr($request->month, 0, 4))
                  ->whereMonth('date', substr($request->month, 5, 2));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->paginate(30);
        $staff = User::where('is_admin', true)
            ->orWhereIn('role', ['admin', 'cashier', 'waiter', 'manager'])
            ->orderBy('name')->get();

        return view('admin.attendances.index', compact('records', 'staff'));
    }

    /**
     * Save a base64 photo to storage and return its path.
     */
    private function saveBase64Photo(string $base64, string $name): string
    {
        // Strip "data:image/png;base64," prefix if present
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        $decoded = base64_decode($base64);
        $path = 'attendance/' . $name . '.jpg';
        Storage::disk('public')->put($path, $decoded);

        return $path;
    }
}
