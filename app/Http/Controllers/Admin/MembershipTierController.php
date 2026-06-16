<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;

class MembershipTierController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::orderBy('sort_order', 'asc')->get();
        return view('admin.membership_tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.membership_tiers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_spent' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'point_multiplier' => 'required|numeric|min:1',
            'sort_order' => 'required|integer',
            'benefits' => 'nullable|string',
        ]);

        MembershipTier::create($request->all());

        return redirect()->route('admin.membership_tiers.index')->with('success', 'Tier berhasil ditambahkan.');
    }

    public function edit(MembershipTier $membershipTier)
    {
        return view('admin.membership_tiers.edit', compact('membershipTier'));
    }

    public function update(Request $request, MembershipTier $membershipTier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_spent' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'point_multiplier' => 'required|numeric|min:1',
            'sort_order' => 'required|integer',
            'benefits' => 'nullable|string',
        ]);

        $membershipTier->update($request->all());

        return redirect()->route('admin.membership_tiers.index')->with('success', 'Tier berhasil diupdate.');
    }

    public function destroy(MembershipTier $membershipTier)
    {
        $membershipTier->delete();
        return redirect()->route('admin.membership_tiers.index')->with('success', 'Tier berhasil dihapus.');
    }
}
