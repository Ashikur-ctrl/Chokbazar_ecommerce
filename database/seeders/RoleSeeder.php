<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::findOrCreate('admin');
        Role::findOrCreate('seller');
        Role::findOrCreate('customer');

        // Sync existing users to spatie roles based on their `role` column
        User::where('role', 'admin')->each(fn ($user) => $user->assignRole('admin'));
        User::where('role', 'seller')->each(fn ($user) => $user->assignRole('seller'));
        User::where('role', 'customer')->each(fn ($user) => $user->assignRole('customer'));
    }
}
