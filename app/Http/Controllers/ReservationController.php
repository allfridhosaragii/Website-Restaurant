<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
class ReservationController extends Controller
{
    public function create()
    {
        return view('reservation.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'guests' => 'required|integer|min:1|max:20',
            'table_id' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'required|url',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diupload.',
            'payment_proof.url' => 'URL bukti transfer tidak valid.',
        ]);
        Reservation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
            'table_id' => $request->table_id,
            'notes' => $request->notes,
            'payment_proof' => $request->payment_proof,
            'status' => 'pending',
        ]);
        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'reservation_created',
            'description' => 'Created reservation for ' . $request->date . ' at ' . $request->time,
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect('/customer/reservations')->with('success', 'Reservasi berhasil dibuat! Menunggu konfirmasi admin.');
    }
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.reservations.index', compact('reservations'));
    }
    public function show($id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return view('customer.reservations.show', compact('reservation'));
    }
}