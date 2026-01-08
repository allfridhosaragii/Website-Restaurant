<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * Register a new user and return API token
     */
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
            'status' => 'active',
        ]);

        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'register_mobile',
            'description' => 'User registered via mobile app',
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_admin' => $user->is_admin,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login and return API token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->isBlocked()) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah diblokir. Silakan hubungi admin.'],
            ]);
        }

        if ($user->isSuspended()) {
            return response()->json([
                'success' => true,
                'warning' => 'Akun Anda sedang dalam status suspend.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'token' => $user->createToken('mobile-app')->plainTextToken,
            ]);
        }

        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'login_mobile',
            'description' => 'User logged in via mobile app',
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_admin' => $user->is_admin,
            ],
            'token' => $user->createToken('mobile-app')->plainTextToken,
        ]);
    }

    /**
     * Handle Google OAuth for mobile
     */
    public function googleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
        ]);

        $allowedDomains = [
            'gmail.com', 'outlook.com', 'hotmail.com', 
            'yahoo.com', 'icloud.com', 'proton.me', 
            'protonmail.com', 'yandex.com',
        ];

        $emailDomain = substr(strrchr($request->email, "@"), 1);
        if (!in_array(strtolower($emailDomain), $allowedDomains)) {
            throw ValidationException::withMessages([
                'email' => ['Hanya email dengan domain tertentu yang diizinkan.'],
            ]);
        }

        $user = User::where('google_id', $request->google_id)->first();

        if (!$user) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['google_id' => $request->google_id]);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'password' => null,
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);
            }
        }

        if ($user->isBlocked()) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah diblokir.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Google authentication successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_admin' => $user->is_admin,
            ],
            'token' => $user->createToken('mobile-app-google')->plainTextToken,
        ]);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request)
    {
        \DB::table('activity_logs')->insert([
            'user_id' => $request->user()->id,
            'action' => 'logout_mobile',
            'description' => 'User logged out from mobile app',
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_admin' => $user->is_admin,
                'status' => $user->status,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Update user profile
     */
    /**
     * Update user profile with avatar support
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $user = $request->user();
        
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('avatar')) {
            try {
                // Use Cloudinary for cloud storage
                \Cloudinary\Configuration\Configuration::instance('cloudinary://474775265674185:pI64ZhoDmEy2fhevZp-kqzzVuCE@dh9ysyfit');
                
                $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                    $request->file('avatar')->getRealPath(),
                    ['folder' => 'profile-photos']
                );
                
                // Get the secure URL
                $url = $result->getSecurePath();
                if ($url) {
                    $user->profile_photo_path = $url;
                }
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            }
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_admin' => $user->is_admin,
                'avatar_url' => $user->profile_photo_path,
            ],
        ]);
    }
}
