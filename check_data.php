<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Total Orders: " . Order::count() . "\n";
echo "Orders by Status:\n";
$statuses = Order::select('order_status', DB::raw('count(*) as total'))
    ->groupBy('order_status')
    ->get();

foreach ($statuses as $status) {
    echo " - " . $status->order_status . ": " . $status->total . "\n";
}

echo "Total Users: " . User::count() . "\n";
