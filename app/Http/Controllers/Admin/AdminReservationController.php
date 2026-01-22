<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
class AdminReservationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = Reservation::with('user')->orderBy('created_at', 'desc');
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'accepted') {
            $query->where('status', 'accepted');
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($filter === 'today') {
            $query->whereDate('date', today());
        }
        $reservations = $query->paginate(20);
        $pendingCount = Reservation::where('status', 'pending')->count();
        $acceptedCount = Reservation::where('status', 'accepted')->count();
        $rejectedCount = Reservation::where('status', 'rejected')->count();
        $todayCount = Reservation::whereDate('date', today())->count();
        return view('admin.reservations.index', compact(
            'reservations', 'filter', 'pendingCount', 'acceptedCount', 'rejectedCount', 'todayCount'
        ));
    }
    public function show($id)
    {
        $reservation = Reservation::with('user')->findOrFail($id);
        return view('admin.reservations.show', compact('reservation'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);
        $statusLabels = [
            'pending' => 'Pending',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];
        return redirect('/admin/reservations')->with('success', "Status reservasi berhasil diperbarui menjadi {$statusLabels[$request->status]}");
    }
}