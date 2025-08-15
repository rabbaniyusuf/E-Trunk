<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Laporan Management
            'view_reports',
            'create_reports',
            'edit_reports',
            'delete_reports',
            'approve_reports',
            'assign_reports',

            // Kebersihan Management
            'view_cleaning_tasks',
            'create_cleaning_tasks',
            'edit_cleaning_tasks',
            'complete_cleaning_tasks',

            // Dashboard Access
            'view_admin_dashboard',
            'view_petugas_dashboard',
            'view_user_dashboard',

            // Statistics
            'view_statistics',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat roles dan assign permissions

        // Role: Petugas Pusat (Super Admin)
        $petugasPusat = Role::create(['name' => 'petugas_pusat']);
        $petugasPusat->givePermissionTo(['view_users', 'create_users', 'edit_users', 'delete_users', 'view_reports', 'create_reports', 'edit_reports', 'delete_reports', 'approve_reports', 'assign_reports', 'view_cleaning_tasks', 'create_cleaning_tasks', 'edit_cleaning_tasks', 'view_admin_dashboard', 'view_statistics', 'export_data']);

        // Role: Petugas Kebersihan
        $petugasKebersihan = Role::create(['name' => 'petugas_kebersihan']);
        $petugasKebersihan->givePermissionTo(['view_reports', 'edit_reports', 'view_cleaning_tasks', 'edit_cleaning_tasks', 'complete_cleaning_tasks', 'view_petugas_dashboard']);

        // Role: Masyarakat (User)
        $masyarakat = Role::create(['name' => 'masyarakat']);
        $masyarakat->givePermissionTo(['view_reports', 'create_reports', 'view_user_dashboard']);

        // Buat user default untuk testing
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@wastemonitor.com',
            'password' => Hash::make('admin123'),
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Malang',
            'balance' => 0,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('petugas_pusat');

        $petugas = User::create([
            'name' => 'Ahmad Petugas',
            'email' => 'petugas1@wastemonitor.com',
            'phone' => '081234567891',
            'address' => 'Jl. Petugas No. 1, Malang',
            'password' => Hash::make('petugas123'),
            'balance' => 0,
            'email_verified_at' => now(),
        ]);
        $petugas->assignRole('petugas_kebersihan');

        // $user = User::create([
        //     'name' => 'Charlie Wilson',
        //     'email' => 'charlie@example.com',
        //     'phone' => '081234567898',
        //     'address' => 'Jl. Thamrin No. 30, Malang',
        //     'waste_bin_code' => 'WB005',
        //     'balance' => 0,
        //     'password' => Hash::make('charlie123'),
        //     'email_verified_at' => now(),
        // ]);
        // $user->assignRole('masyarakat');
    }
}
