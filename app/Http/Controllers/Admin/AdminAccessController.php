<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AdminAccessController extends Controller
{
    public function index()
    {
        $admins = User::where(function($query) {
            $query->where('is_admin', true)
                  ->orWhere('role', 'viewer')
                  ->orWhere('email', 'pedoprimasaragi@gmail.com');
        })
        ->orderByRaw("CASE WHEN email = 'pedoprimasaragi@gmail.com' THEN 0 ELSE 1 END")
        ->orderBy('created_at', 'desc')
        ->get();
        return view('admin.access.index', compact('admins'));
    }
    public function create()
    {
        return view('admin.access.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'admin_type' => 'required|in:admin,super_admin',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'status' => 'active',
            'role' => 'admin',
        ]);
        $allEnabled = $request->admin_type === 'super_admin';
        AdminPermission::createDefaultPermissions($user->id, $allEnabled);
        return redirect()->route('admin.access.index')
            ->with('success', 'Admin berhasil ditambahkan!');
    }
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        if ($admin->isSuperAdmin() && auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
            return redirect()->route('admin.access.index')
                ->with('error', 'Tidak dapat mengedit Super Admin!');
        }
        $permissions = AdminPermission::getAvailablePermissions();
        $userPermissions = $admin->adminPermissions->pluck('is_enabled', 'permission_key')->toArray();
        return view('admin.access.edit', compact('admin', 'permissions', 'userPermissions'));
    }
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
        }
        $permissions = $request->input('permissions', []);
        foreach (AdminPermission::getAvailablePermissions() as $key => $label) {
            $admin->adminPermissions()->updateOrCreate(
                ['permission_key' => $key],
                ['is_enabled' => in_array($key, $permissions)]
            );
        }
        return redirect()->route('admin.access.index')
            ->with('success', 'Admin berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        if ($admin->isSuperAdmin()) {
            return redirect()->route('admin.access.index')
                ->with('error', 'Tidak dapat menghapus Super Admin!');
        }
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.access.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }
        $admin->adminPermissions()->delete();
        $admin->delete();
        return redirect()->route('admin.access.index')
            ->with('success', 'Admin berhasil dihapus!');
    }
    public function toggleSuperAdmin($id)
    {
        $admin = User::findOrFail($id);
        if ($admin->email === 'pedoprimasaragi@gmail.com') {
            return redirect()->route('admin.access.index')
                ->with('error', 'Tidak dapat mengubah status Super Admin utama!');
        }
        $allEnabled = $admin->adminPermissions()->where('is_enabled', false)->count() === 0;
        if ($allEnabled) {
            $admin->adminPermissions()->update(['is_enabled' => false]);
            $message = $admin->name . ' sekarang menjadi Admin Biasa.';
        } else {
            $admin->adminPermissions()->update(['is_enabled' => true]);
            $message = $admin->name . ' sekarang memiliki akses penuh!';
        }
        return redirect()->route('admin.access.index')
            ->with('success', $message);
    }
    public function toggleOnlineStatus($id)
    {
        $admin = User::findOrFail($id);
        if ($admin->isSuperAdmin()) {
            return redirect()->route('admin.access.index')
                ->with('error', 'Tidak dapat mengubah status Super Admin!');
        }
        if ($admin->status === 'offline') {
            $admin->update(['status' => 'active']);
            $message = $admin->name . ' sekarang Online dan dapat login.';
        } else {
            $admin->update(['status' => 'offline']);
            $message = $admin->name . ' sekarang Offline dan tidak dapat login.';
        }
        return redirect()->route('admin.access.index')
            ->with('success', $message);
    }
}