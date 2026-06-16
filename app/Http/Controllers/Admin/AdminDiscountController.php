<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDiscountController extends Controller
{
    public function index()
    {
        $discounts = DB::table('discounts')
            ->leftJoin('menus', 'discounts.menu_id', '=', 'menus.id')
            ->select('discounts.*', 'menus.name as menu_name')
            ->orderBy('discounts.created_at', 'desc')
            ->get();
            
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        $menus = DB::table('menus')->orderBy('name', 'asc')->get();
        return view('admin.discounts.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage,free_item',
            'value' => 'required_unless:type,free_item|numeric|min:0',
            'scope' => 'required|in:order,item',
            'menu_id' => 'required_if:scope,item|nullable|exists:menus,id',
            'voucher_code' => 'nullable|string|unique:discounts,voucher_code',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'happy_hour_start' => 'nullable|date_format:H:i',
            'happy_hour_end' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:0',
            'free_menu_id' => 'required_if:type,free_item|nullable|exists:menus,id',
            'is_active' => 'boolean',
        ]);

        $daysOfWeek = $request->has('days_of_week') ? json_encode($request->days_of_week) : null;

        DB::table('discounts')->insert([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->type === 'free_item' ? 0 : $request->value,
            'scope' => $request->scope,
            'menu_id' => $request->scope === 'item' ? $request->menu_id : null,
            'free_menu_id' => $request->type === 'free_item' ? $request->free_menu_id : null,
            'voucher_code' => $request->voucher_code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'happy_hour_start' => $request->happy_hour_start,
            'happy_hour_end' => $request->happy_hour_end,
            'days_of_week' => $daysOfWeek,
            'usage_limit' => $request->usage_limit,
            'max_usage_per_user' => $request->max_usage_per_user ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/discounts')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $discount = DB::table('discounts')->where('id', $id)->first();
        if (!$discount) {
            abort(404);
        }
        
        $discount->days_of_week = $discount->days_of_week ? json_decode($discount->days_of_week, true) : [];
        $menus = DB::table('menus')->orderBy('name', 'asc')->get();
        
        return view('admin.discounts.edit', compact('discount', 'menus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage,free_item',
            'value' => 'required_unless:type,free_item|numeric|min:0',
            'scope' => 'required|in:order,item',
            'menu_id' => 'required_if:scope,item|nullable|exists:menus,id',
            'voucher_code' => 'nullable|string|unique:discounts,voucher_code,'.$id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'happy_hour_start' => 'nullable|date_format:H:i|date_format:H:i:s', // HTML time input might send seconds
            'happy_hour_end' => 'nullable|date_format:H:i|date_format:H:i:s',
            'days_of_week' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:0',
            'free_menu_id' => 'required_if:type,free_item|nullable|exists:menus,id',
            'is_active' => 'boolean',
        ]);

        $daysOfWeek = $request->has('days_of_week') ? json_encode($request->days_of_week) : null;
        
        // Handle time format if seconds are sent
        $happyHourStart = $request->happy_hour_start;
        $happyHourEnd = $request->happy_hour_end;
        
        if ($happyHourStart && strlen($happyHourStart) > 5) {
            $happyHourStart = substr($happyHourStart, 0, 5);
        }
        if ($happyHourEnd && strlen($happyHourEnd) > 5) {
            $happyHourEnd = substr($happyHourEnd, 0, 5);
        }

        DB::table('discounts')->where('id', $id)->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->type === 'free_item' ? 0 : $request->value,
            'scope' => $request->scope,
            'menu_id' => $request->scope === 'item' ? $request->menu_id : null,
            'free_menu_id' => $request->type === 'free_item' ? $request->free_menu_id : null,
            'voucher_code' => $request->voucher_code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'happy_hour_start' => $happyHourStart,
            'happy_hour_end' => $happyHourEnd,
            'days_of_week' => $daysOfWeek,
            'usage_limit' => $request->usage_limit,
            'max_usage_per_user' => $request->max_usage_per_user ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'updated_at' => now(),
        ]);

        return redirect('/admin/discounts')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('discounts')->where('id', $id)->delete();
        return redirect('/admin/discounts')->with('success', 'Diskon berhasil dihapus.');
    }
}
