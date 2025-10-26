<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Admin
        User::firstOrCreate(
            ['email' => 'admin@meteor.com'],
            [
                'name' => 'Admin User',
                'phone' => '0123456789',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'address' => 'Hà Nội, Việt Nam',
                'status' => 'active',
            ]
        );

        // Tạo Staff
        User::firstOrCreate(
            ['email' => 'staff@meteor.com'],
            [
                'name' => 'Staff User',
                'phone' => '0987654321',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'address' => 'TP. Hồ Chí Minh, Việt Nam',
                'status' => 'active',
            ]
        );

        // Tạo User thường (không thể đăng nhập admin)
        User::firstOrCreate(
            ['email' => 'user@meteor.com'],
            [
                'name' => 'Regular User',
                'phone' => '0111111111',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'address' => 'Đà Nẵng, Việt Nam',
                'status' => 'active',
            ]
        );

        // Tạo User bị cấm
        User::firstOrCreate(
            ['email' => 'banned@meteor.com'],
            [
                'name' => 'Banned User',
                'phone' => '0222222222',
                'password' => Hash::make('banned123'),
                'role' => 'user',
                'address' => 'Cần Thơ, Việt Nam',
                'status' => 'banned',
            ]
        );

        // Tạo User không hoạt động
        User::firstOrCreate(
            ['email' => 'inactive@meteor.com'],
            [
                'name' => 'Inactive User',
                'phone' => '0333333333',
                'password' => Hash::make('inactive123'),
                'role' => 'user',
                'address' => 'Huế, Việt Nam',
                'status' => 'inactive',
            ]
        );

        $this->command->info('✅ Admin users created successfully!');
        $this->command->info('');
        $this->command->info('📝 Test Accounts:');
        $this->command->info('');
        $this->command->info('Admin Account:');
        $this->command->info('  Email: admin@meteor.com');
        $this->command->info('  Password: admin123');
        $this->command->info('');
        $this->command->info('Staff Account:');
        $this->command->info('  Email: staff@meteor.com');
        $this->command->info('  Password: staff123');
        $this->command->info('');
        $this->command->info('Regular User (Cannot login to admin):');
        $this->command->info('  Email: user@meteor.com');
        $this->command->info('  Password: user123');
        $this->command->info('');
        $this->command->info('Banned User (Cannot login):');
        $this->command->info('  Email: banned@meteor.com');
        $this->command->info('  Password: banned123');
        $this->command->info('');
        $this->command->info('Inactive User (Cannot login):');
        $this->command->info('  Email: inactive@meteor.com');
        $this->command->info('  Password: inactive123');
    }
}

