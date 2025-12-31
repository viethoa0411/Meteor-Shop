<?php
$o = App\Models\Order::find(81);
if ($o) {
    $o->order_status = 'delivered';
    $o->delivered_at = now()->subDays(3);
    $o->save();
    echo "SUCCESS: Updated Order ID 81 to 'delivered' status with date " . $o->delivered_at . "\n";
} else {
    echo "ERROR: Order not found\n";
}
