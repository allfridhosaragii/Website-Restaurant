<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierDebt;
use Illuminate\Support\Facades\Storage;

class AdminSupplierDebtController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierDebt::with('creator');

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', '!=', 'paid')->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        $debts = $query->orderBy('due_date', 'asc')->get();
        
        $totalDebt = SupplierDebt::where('status', '!=', 'paid')->sum('remaining_amount');
        $overdueDebt = SupplierDebt::where('status', '!=', 'paid')->where('due_date', '<', now()->toDateString())->sum('remaining_amount');

        return view('admin.supplier_debts.index', compact('debts', 'totalDebt', 'overdueDebt'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('receipt_image');
        $data['paid_amount'] = 0;
        $data['remaining_amount'] = $request->amount;
        $data['status'] = $request->due_date < now()->toDateString() ? 'overdue' : 'unpaid';
        $data['created_by'] = auth()->id();

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')->store('supplier_debts', 'public');
        }

        SupplierDebt::create($data);

        return back()->with('success', 'Hutang supplier berhasil dicatat.');
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:1'
        ]);

        $debt = SupplierDebt::findOrFail($id);

        if ($request->payment_amount > $debt->remaining_amount) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa hutang.');
        }

        $debt->paid_amount += $request->payment_amount;
        $debt->remaining_amount -= $request->payment_amount;

        if ($debt->remaining_amount <= 0) {
            $debt->status = 'paid';
        } else {
            $debt->status = $debt->due_date < now()->toDateString() ? 'overdue' : 'partial';
        }

        $debt->save();

        // Automatically log this as an expense
        \App\Models\Expense::create([
            'title' => 'Pembayaran Hutang: ' . $debt->title . ' (' . $debt->supplier_name . ')',
            'category_id' => \App\Models\ExpenseCategory::firstOrCreate(
                ['name' => 'Pembayaran Hutang'],
                ['color' => '#dc3545']
            )->id,
            'amount' => $request->payment_amount,
            'date' => now()->toDateString(),
            'description' => 'Pembayaran otomatis dari modul Hutang Supplier.',
            'created_by' => auth()->id()
        ]);

        return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }

    public function destroy($id)
    {
        $debt = SupplierDebt::findOrFail($id);
        
        if ($debt->receipt_image) {
            Storage::disk('public')->delete($debt->receipt_image);
        }
        
        $debt->delete();

        return back()->with('success', 'Catatan hutang berhasil dihapus.');
    }
}
