<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat daftar Role
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'employee']);

        // 2. Buat satu akun untuk Super Admin
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin_super',
            'email' => 'admin@perusahaan.com',
            'password' => Hash::make('rahasia123'), // Jangan lupa diganti kalau mau rilis
        ]);

        // 3. Masukkan akun tersebut ke role 'admin'
        $admin->assignRole('admin');
        
        // 4. (Opsional) Bikin contoh akun Karyawan
        $karyawan = User::create([
            'name' => 'Udin Petot',
            'username' => 'udin_absen',
            'email' => 'udin@perusahaan.com',
            'password' => Hash::make('password123'),
        ]);
        
        $karyawan->assignRole('employee');
    }
}