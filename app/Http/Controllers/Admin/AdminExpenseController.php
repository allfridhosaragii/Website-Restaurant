<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Storage;

class AdminExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator']);

        if ($request->filled('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        } else {
            $query->whereMonth('date', date('m'))
                  ->whereYear('date', date('Y'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $expenses = $query->orderBy('date', 'desc')->get();
        $categories = ExpenseCategory::all();

        $totalExpense = $expenses->sum('amount');
        
        $chartData = $expenses->groupBy('category.name')->map(function ($items, $key) {
            return [
                'name' => $key,
                'total' => $items->sum('amount'),
                'color' => $items->first()->category->color
            ];
        })->values();

        return view('admin.expenses.index', compact('expenses', 'categories', 'totalExpense', 'chartData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('receipt_image');
        $data['created_by'] = auth()->id();

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')->store('expenses', 'public');
        }

        Expense::create($data);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        
        $expense->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // Category Methods
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7'
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'color' => $request->color ?? '#6c757d'
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }
}
