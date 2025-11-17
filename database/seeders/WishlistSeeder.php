<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(5)->get();
        $products = Product::all();

        if ($users->count() < 5) {
            $this->call(AdminUserSeeder::class);
            $users = User::take(5)->get();
        }

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        foreach ($users as $user) {
            $favorites = $products->random(min(3, $products->count()));

            foreach ($favorites as $product) {
                DB::table('wishlists')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
