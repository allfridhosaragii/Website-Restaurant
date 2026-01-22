<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->only('email'));
        }
        if ($user->isBlocked()) {
            return back()->withErrors([
                'email' => 'Akun Anda telah diblokir. Silakan hubungi admin untuk informasi lebih lanjut.',
            ])->withInput($request->only('email'));
        }
        if ($user->isOffline() && $user->is_admin) {
            return back()->withErrors([
                'email' => 'Akun admin Anda sedang dalam status offline. Silakan hubungi Super Admin.',
            ])->withInput($request->only('email'));
        }
        Auth::login($user, $request->boolean('remember'));
        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'User logged in',
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($user->is_admin) {
            return redirect('/admin/dashboard');
        }
        if ($user->isSuspended()) {
            return redirect('/')->with('warning', 'Peringatan: Akun Anda sedang dalam status suspend karena terdeteksi adanya aktivitas yang melanggar ketentuan layanan. Harap perbaiki perilaku Anda atau akun akan diblokir permanen.');
        }
        return redirect('/');
    }
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'customer', 
            'is_admin' => false,
            'is_blocked' => false,
            'is_suspended' => false,
        ]);
        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'register',
            'description' => 'User registered',
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Auth::login($user);
        return redirect('/customer/dashboard')->with('success', 'Registration successful! Welcome to ' . config('app.name'));
    }
}