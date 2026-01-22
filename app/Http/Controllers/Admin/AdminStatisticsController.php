<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Models\SiteVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminStatisticsController extends Controller
{
    public function index()
    {
        $todayActivities = DB::table('activity_logs')->whereDate('created_at', today())->count();
        $totalActivities = DB::table('activity_logs')->count();
        $unresolvedErrors = ErrorLog::where('is_resolved', false)->count();
        $totalErrors = ErrorLog::count();
        $liveVisitors = SiteVisitor::where('is_active', true)
            ->where('last_heartbeat', '>=', now()->subSeconds(60))
            ->count();
        return view('admin.statistics.index', compact(
            'todayActivities',
            'totalActivities',
            'unresolvedErrors',
            'totalErrors',
            'liveVisitors'
        ));
    }
    public function getActivities(Request $request)
    {
        $query = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select(
                'activity_logs.id',
                'activity_logs.action',
                'activity_logs.description',
                'activity_logs.ip_address',
                'activity_logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'users.is_admin'
            )
            ->orderBy('activity_logs.created_at', 'desc');
        if ($request->type === 'admin') {
            $query->where('users.is_admin', true);
        } elseif ($request->type === 'customer') {
            $query->where(function($q) {
                $q->where('users.is_admin', false)->orWhereNull('users.is_admin');
            });
        }
        if ($request->date === 'today') {
            $query->whereDate('activity_logs.created_at', today());
        } elseif ($request->date === 'week') {
            $query->where('activity_logs.created_at', '>=', now()->subDays(7));
        }
        $activities = $query->limit(100)->get();
        return response()->json([
            'success' => true,
            'data' => $activities,
            'count' => $activities->count()
        ]);
    }
    public function getErrors(Request $request)
    {
        $query = ErrorLog::with(['user:id,name,email', 'resolver:id,name'])
            ->orderBy('created_at', 'desc');
        if ($request->status === 'unresolved') {
            $query->where('is_resolved', false);
        } elseif ($request->status === 'resolved') {
            $query->where('is_resolved', true);
        }
        if ($request->date === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($request->date === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        }
        $errors = $query->limit(100)->get();
        return response()->json([
            'success' => true,
            'data' => $errors,
            'count' => $errors->count()
        ]);
    }
    public function resolveError(Request $request, $id)
    {
        $error = ErrorLog::findOrFail($id);
        $error->resolve();
        return response()->json([
            'success' => true,
            'message' => 'Error marked as resolved'
        ]);
    }
    public function getLiveVisitors()
    {
        $visitors = SiteVisitor::where('is_active', true)
            ->where('last_heartbeat', '>=', now()->subSeconds(60))
            ->orderBy('entry_time', 'desc')
            ->get(['id', 'page_url', 'page_title', 'browser', 'device_type', 'entry_time', 'user_id']);
        return response()->json([
            'success' => true,
            'data' => $visitors,
            'count' => $visitors->count()
        ]);
    }
    public function getUserHistory(Request $request)
    {
        $userId = $request->user_id;
        $ip = $request->ip;
        if (!$userId && !$ip) {
            return response()->json(['success' => false, 'message' => 'User ID or IP is required'], 400);
        }
        $activities = DB::table('activity_logs')
            ->where(function ($q) use ($userId, $ip) {
                if ($userId) $q->where('user_id', $userId);
                if ($ip) $q->orWhere('ip_address', $ip);
            })
            ->select('action', 'description', 'created_at as time', DB::raw("'activity' as type"))
            ->get();
        $visits = DB::table('site_visitors')
            ->where(function ($q) use ($userId, $ip) {
                if ($userId) $q->where('user_id', $userId);
                if ($ip) $q->orWhere('ip_address', $ip);
            })
            ->select(DB::raw("CONCAT('Membuka halaman: ', page_title) as action"), 'page_url as description', 'entry_time as time', DB::raw("'visit' as type"))
            ->get();
        $history = $activities->concat($visits)->sortByDesc('time')->values();
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}