<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class ApiPointController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $transactions = $user->transactions()
            ->latest()
            ->paginate(20);
        return response()->json([
            'success' => true,
            'points' => $user->points,
            'transactions' => $transactions,
        ]);
    }
}