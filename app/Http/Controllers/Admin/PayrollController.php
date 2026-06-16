<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $payrolls = DB::table('payrolls')
            ->join('users', 'users.id', '=', 'payrolls.user_id')
            ->where('payrolls.year', $year)
            ->where('payrolls.month', (int)$mon)
            ->select('payrolls.*', 'users.name as employee_name', 'users.role as employee_role')
            ->orderBy('users.name')
            ->get();

        $staff = User::where('is_admin', true)
            ->orWhereIn('role', ['admin', 'cashier', 'waiter', 'manager'])
            ->orderBy('name')->get();

        return view('admin.payroll.index', compact('payrolls', 'staff', 'month'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|string', // "2026-06"
        ]);

        [$year, $mon] = explode('-', $request->month);

        // Get all staff
        $staff = User::where(function($q) {
            $q->where('is_admin', true)
              ->orWhereIn('role', ['admin', 'cashier', 'waiter', 'manager']);
        })->get();

        $generated = 0;
        foreach ($staff as $employee) {
            // Skip if already exists
            if (DB::table('payrolls')->where('user_id', $employee->id)->where('year', $year)->where('month', $mon)->exists()) {
                continue;
            }

            // Count attendance days
            $presentDays = DB::table('attendances')
                ->where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $mon)
                ->whereIn('status', ['present', 'late'])
                ->count();

            // Working days in month (approx)
            $workingDays = 26;
            $attendanceBonus = $presentDays >= $workingDays ? 500000 : 0;

            $baseSalary = $employee->base_salary ?? 3000000;
            $total = $baseSalary + $attendanceBonus;

            DB::table('payrolls')->insert([
                'user_id'          => $employee->id,
                'year'             => $year,
                'month'            => $mon,
                'base_salary'      => $baseSalary,
                'attendance_bonus' => $attendanceBonus,
                'commission'       => 0,
                'allowance'        => 0,
                'deduction'        => 0,
                'total_salary'     => $total,
                'status'           => 'draft',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $generated++;
        }

        return back()->with('success', "Payroll berhasil di-generate untuk {$generated} karyawan.");
    }

    public function approve($id)
    {
        DB::table('payrolls')->where('id', $id)->update([
            'status'     => 'approved',
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Payroll berhasil disetujui.');
    }

    public function pay($id)
    {
        DB::table('payrolls')->where('id', $id)->update([
            'status'     => 'paid',
            'paid_at'    => now(),
            'paid_by'    => Auth::id(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Pembayaran gaji berhasil dicatat.');
    }

    public function slip($id)
    {
        $payroll = DB::table('payrolls')
            ->join('users', 'users.id', '=', 'payrolls.user_id')
            ->where('payrolls.id', $id)
            ->select('payrolls.*', 'users.name as employee_name', 'users.role as employee_role', 'users.email as employee_email')
            ->first();

        if (!$payroll) abort(404);

        $monthName = \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->translatedFormat('F Y');

        return view('admin.payroll.slip', compact('payroll', 'monthName'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'allowance'  => 'nullable|numeric|min:0',
            'deduction'  => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string|max:500',
        ]);

        $payroll = DB::table('payrolls')->find($id);
        $total = $payroll->base_salary + $payroll->attendance_bonus + $payroll->commission
               + ($request->allowance ?? 0) - ($request->deduction ?? 0);

        DB::table('payrolls')->where('id', $id)->update([
            'allowance'    => $request->allowance ?? 0,
            'deduction'    => $request->deduction ?? 0,
            'total_salary' => $total,
            'notes'        => $request->notes,
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Payroll berhasil diperbarui.');
    }
}
