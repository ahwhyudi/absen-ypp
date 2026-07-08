<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        // Ambil data user beserta rolenya dari Spatie, urutkan dari yang terbaru
        $users = User::with('roles')->latest()->get();

        // Ambil semua role yang ada di database untuk dropdown form
        $roles = Role::all();

        return view('dashboard.admin.staff', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ], [
            'username.unique' => 'Username sudah terdaftar di sistem!',
            'email.unique'    => 'Email sudah terdaftar di sistem!',
        ]);

        // 2. Simpan user baru
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Assign Role menggunakan Spatie
        $user->assignRole($request->role);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff berhasil didaftarkan ke sistem.');
    }

    public function update(Request $request, User $user)
    {
        // 1. Validasi
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id, // Abaikan email milik user itu sendiri
            'password' => 'nullable|string|min:6', // Password opsional
            'role'     => 'required|exists:roles,name',
        ]);

        // 2. Update Data
        $userData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // 3. Update Role (Spatie)
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data staff berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Proteksi: Jangan sampai admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Akun staff berhasil dihapus.');
    }
}
