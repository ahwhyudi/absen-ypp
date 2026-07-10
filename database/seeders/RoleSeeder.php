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
            'name' => 'Admin',
            'username' => 'admin1',
            'email' => 'admin1@perusahaan.com',
            'password' => Hash::make('password'), // Jangan lupa diganti kalau mau rilis
        ]);

        // 3. Masukkan akun tersebut ke role 'admin'
        $admin->assignRole('admin');
        
        // 4. (Opsional) Bikin contoh akun Karyawan
        // $karyawan = User::create([
        //     'name' => 'staff',
        //     'username' => 'staff1',
        //     'email' => 'staff1@perusahaan.com',
        //     'password' => Hash::make('password'),
        // ]);
        
        // $karyawan->assignRole('employee');


        // $karyawan = User::create([
        //     'name' => 'manager',
        //     'username' => 'manager1',
        //     'email' => 'manager1@perusahaan.com',
        //     'password' => Hash::make('password'),
        // ]);
        
        // $karyawan->assignRole('manager');
    }
}