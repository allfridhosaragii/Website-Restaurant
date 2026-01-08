<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAdminUserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = \DB::table('users')
            ->select('id', 'name', 'email', 'role', 'status', 'created_at', 'phone')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:active,banned'
        ]);

        \DB::table('users')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'User status updated']);
    }
}
