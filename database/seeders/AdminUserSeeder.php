<?php

namespace Database\Seeders;

use App\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@vis.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin123'),
                'phone' => '0790000000',
                'is_active' => true,
            ]
        );

        $admin->assignRole('Super Admin');

        $this->command->info('Admin user created: admin@vis.com / admin123');
    }
}