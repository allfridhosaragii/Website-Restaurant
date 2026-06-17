<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class AdminPaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);

        PaymentMethod::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.payment_methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.payment_methods.index')->with('success', 'Metode pembayaran berhasil diupdate');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        return redirect()->route('admin.payment_methods.index')->with('success', 'Metode pembayaran berhasil dihapus');
    }

    public function toggleActive($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return response()->json(['success' => true, 'is_active' => $paymentMethod->is_active]);
    }
}
