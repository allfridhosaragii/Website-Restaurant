<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $allowedDomains = [
                'gmail.com',
                'outlook.com',
                'hotmail.com',
                'yahoo.com',
                'icloud.com',
                'proton.me',
                'protonmail.com',
                'yandex.com',
            ];
            $email = $googleUser->getEmail();
            $emailDomain = substr(strrchr($email, "@"), 1);
            if (!in_array(strtolower($emailDomain), $allowedDomains)) {
                return redirect()->route('login')->with('error', 'Maaf, hanya email dengan domain ' . implode(', ', $allowedDomains) . ' yang diizinkan untuk mendaftar.');
            }
            $user = User::where('google_id', $googleUser->getId())->first();
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                } else {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password' => null,
                        'email_verified_at' => now(),
                    ]);
                }
            }
            if ($user->isBlocked()) {
                return redirect()->route('login')->with('error', 'Akun Anda telah diblokir. Silakan hubungi admin untuk informasi lebih lanjut.');
            }
            Auth::login($user, true);
            if ($user->isSuspended()) {
                return redirect('/')->with('warning', 'Peringatan: Akun Anda sedang dalam status suspend karena terdeteksi adanya aktivitas yang melanggar ketentuan layanan. Harap perbaiki perilaku Anda atau akun akan diblokir permanen.');
            }
            return redirect('/');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}