<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Menu;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::with(['buyMenu', 'getMenu'])->orderBy('created_at', 'desc')->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        $menus = Menu::orderBy('name')->get();
        return view('admin.promos.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:buy_x_get_y',
            'buy_menu_id' => 'required|exists:menus,id',
            'buy_quantity' => 'required|integer|min:1',
            'get_menu_id' => 'nullable|exists:menus,id',
            'get_quantity' => 'required|integer|min:1',
            'get_type' => 'required|in:free,discount',
            'get_discount_percent' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['get_menu_id'] = $request->get_menu_id ?: $request->buy_menu_id;

        if ($data['get_type'] === 'free') {
            $data['get_discount_percent'] = null;
        }

        Promo::create($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil ditambahkan.');
    }

    public function edit(Promo $promo)
    {
        $menus = Menu::orderBy('name')->get();
        return view('admin.promos.edit', compact('promo', 'menus'));
    }

    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:buy_x_get_y',
            'buy_menu_id' => 'required|exists:menus,id',
            'buy_quantity' => 'required|integer|min:1',
            'get_menu_id' => 'nullable|exists:menus,id',
            'get_quantity' => 'required|integer|min:1',
            'get_type' => 'required|in:free,discount',
            'get_discount_percent' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['get_menu_id'] = $request->get_menu_id ?: $request->buy_menu_id;

        if ($data['get_type'] === 'free') {
            $data['get_discount_percent'] = null;
        }

        $promo->update($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil diupdate.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil dihapus.');
    }
}
