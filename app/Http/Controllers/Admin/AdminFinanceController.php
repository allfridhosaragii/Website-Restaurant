<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use App\Models\SupplierDebt;
use App\Models\DepositTransaction;

class AdminFinanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // ==========================================
        // 1. PROFIT & LOSS (RUGI LABA)
        // ==========================================
        // Pendapatan
        $orderIncome = Order::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_status', 'paid')
            ->sum('total');

        $depositTopupIncome = DepositTransaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('type', 'topup')
            ->sum('amount');

        $totalIncome = $orderIncome + $depositTopupIncome;

        // Beban (Expenses - Exclude Pembayaran Hutang to avoid double counting if materials are tracked differently, but let's assume all expenses are here)
        $expensesByCategory = Expense::with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('category.name')
            ->map(function ($items) {
                return $items->sum('amount');
            });
        
        $totalExpense = $expensesByCategory->sum();
        $netProfit = $totalIncome - $totalExpense;

        // ==========================================
        // 2. BALANCE SHEET (NERACA)
        // ==========================================
        // Aktiva (Asset)
        // Simplified: Kas Tunai = Total Income - Total Kas Keluar all time
        $allTimeOrderIncome = Order::where('payment_status', 'paid')->sum('total');
        $allTimeDepositTopup = DepositTransaction::where('type', 'topup')->sum('amount');
        $allTimeExpenses = Expense::sum('amount');
        $cashBalance = ($allTimeOrderIncome + $allTimeDepositTopup) - $allTimeExpenses;
        
        // Persediaan (Stok x Harga)
        // Simplified estimate for demo, usually needs proper inventory valuation
        $inventoryValue = \App\Models\Menu::sum(\DB::raw('stock * price'));
        $totalAssets = $cashBalance + $inventoryValue;

        // Kewajiban (Liability)
        $totalSupplierDebt = SupplierDebt::where('status', '!=', 'paid')->sum('remaining_amount');
        $totalCustomerDeposit = \App\Models\User::sum('deposit_balance');
        $totalLiability = $totalSupplierDebt + $totalCustomerDeposit;

        // Ekuitas (Equity)
        $equity = $totalAssets - $totalLiability;

        // ==========================================
        // 3. CASH FLOW (ARUS KAS)
        // ==========================================
        // Bulan sebelumnya untuk saldo awal
        $prevMonth = date('m', strtotime("$year-$month-01 -1 month"));
        $prevYear = date('Y', strtotime("$year-$month-01 -1 month"));
        
        $prevOrderIncome = Order::where('payment_status', 'paid')->where('created_at', '<', "$year-$month-01")->sum('total');
        $prevDepositTopup = DepositTransaction::where('type', 'topup')->where('created_at', '<', "$year-$month-01")->sum('amount');
        $prevExpenses = Expense::where('date', '<', "$year-$month-01")->sum('amount');
        
        $openingBalance = ($prevOrderIncome + $prevDepositTopup) - $prevExpenses;
        $netCashFlow = $totalIncome - $totalExpense;
        $endingBalance = $openingBalance + $netCashFlow;

        return view('admin.finance.index', compact(
            'month', 'year',
            'orderIncome', 'depositTopupIncome', 'totalIncome',
            'expensesByCategory', 'totalExpense', 'netProfit',
            'cashBalance', 'inventoryValue', 'totalAssets',
            'totalSupplierDebt', 'totalCustomerDeposit', 'totalLiability', 'equity',
            'openingBalance', 'netCashFlow', 'endingBalance'
        ));
    }
}
