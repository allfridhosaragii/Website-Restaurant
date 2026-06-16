<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\DepositTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }
        
        $customers = $query->paginate(20);
        return view('admin.deposits.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $transactions = DepositTransaction::where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.deposits.show', compact('customer', 'transactions'));
    }

    public function topup(Request $request, User $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $amount = $request->amount;
            $balanceBefore = $customer->deposit_balance;
            $balanceAfter = $balanceBefore + $amount;

            $customer->deposit_balance = $balanceAfter;
            $customer->save();

            DepositTransaction::create([
                'user_id' => $customer->id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $request->description ?? 'Topup by Admin',
                'processed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Deposit berhasil ditambahkan sebesar Rp ' . number_format($amount, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan deposit: ' . $e->getMessage());
        }
    }
}
