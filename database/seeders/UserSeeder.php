<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin default
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'vanluan2k2bg@gmail.com'],
            [
                'name' => 'Admin Van Luan',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Tạo users với role user
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'user1@example.com',
                'phone' => '0912345678',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'status' => 'active',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'user2@example.com',
                'phone' => '0923456789',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => '456 Đường XYZ, Quận 2, TP.HCM',
                'status' => 'active',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'user3@example.com',
                'phone' => '0934567890',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => '789 Đường DEF, Quận 3, TP.HCM',
                'status' => 'active',
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'user4@example.com',
                'phone' => '0945678901',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => '321 Đường GHI, Quận 4, TP.HCM',
                'status' => 'active',
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'user5@example.com',
                'phone' => '0956789012',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => '654 Đường JKL, Quận 5, TP.HCM',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}