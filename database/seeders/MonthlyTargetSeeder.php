<?php

namespace Database\Seeders;

use App\Models\MonthlyTarget;
use Illuminate\Database\Seeder;

class MonthlyTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = now()->year;

        $targets = [
            ['month' => 1, 'target_amount' => 150000000],
            ['month' => 2, 'target_amount' => 160000000],
            ['month' => 3, 'target_amount' => 170000000],
            ['month' => 4, 'target_amount' => 190000000],
            ['month' => 5, 'target_amount' => 200000000],
        ];

        foreach ($targets as $target) {
            MonthlyTarget::updateOrCreate(
                ['year' => $year, 'month' => $target['month']],
                ['target_amount' => $target['target_amount']]
            );
        }
    }
}
