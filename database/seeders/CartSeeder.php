<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->take(5)->all();

        if (count($userIds) < 5) {
            $this->call(AdminUserSeeder::class);
            $userIds = User::pluck('id')->take(5)->all();
        }

        foreach ($userIds as $index => $userId) {
            DB::table('carts')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'total_price' => 0,
                    'status' => $index % 2 === 0 ? 'active' : 'checked_out',
                    'created_at' => now()->subDays(3 - $index),
                    'updated_at' => now()->subDays(3 - $index),
                ]
            );
        }
    }
}
