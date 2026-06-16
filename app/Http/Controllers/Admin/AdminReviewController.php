<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Carbon\Carbon;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'order'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply,
            'replied_at' => Carbon::now()
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function toggleStatus($id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        return back()->with('success', 'Status review berhasil diubah.');
    }
}
