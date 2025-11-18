<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('sub_total', 10, 2)->nullable()->after('final_total');
            $table->string('shipping_method')->nullable()->after('shipping_phone');
            $table->decimal('shipping_fee', 10, 2)->nullable()->after('shipping_method');
            $table->string('voucher_code')->nullable()->after('discount_amount');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_district')->nullable()->after('shipping_city');
            $table->string('shipping_ward')->nullable()->after('shipping_district');
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->string('tracking_code')->nullable()->after('shipping_ward');
            $table->string('tracking_url')->nullable()->after('tracking_code');
            $table->string('shipping_provider')->nullable()->after('tracking_url');
            $table->string('cancel_reason')->nullable()->after('order_status');
            $table->text('notes')->nullable()->after('cancel_reason');
            $table->enum('return_status', ['none', 'requested', 'approved', 'rejected', 'refunded'])
                ->default('none')->after('notes');
            $table->text('return_reason')->nullable()->after('return_status');
            $table->text('return_note')->nullable()->after('return_reason');
            $table->json('return_attachments')->nullable()->after('return_note');
            $table->timestamp('order_date')->nullable()->after('return_attachments');
            $table->timestamp('confirmed_at')->nullable()->after('order_date');
            $table->timestamp('packed_at')->nullable()->after('confirmed_at');
            $table->timestamp('shipped_at')->nullable()->after('packed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            $table->timestamp('refunded_at')->nullable()->after('cancelled_at');
        });

        // Update enum list for order_status to support more states
        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN order_status 
             ENUM('pending','processing','shipping','completed','cancelled','return_requested','returned') 
             DEFAULT 'pending'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'sub_total',
                'shipping_method',
                'shipping_fee',
                'voucher_code',
                'shipping_city',
                'shipping_district',
                'shipping_ward',
                'customer_name',
                'customer_phone',
                'customer_email',
                'tracking_code',
                'tracking_url',
                'shipping_provider',
                'cancel_reason',
                'notes',
                'return_status',
                'return_reason',
                'return_note',
                'return_attachments',
                'order_date',
                'confirmed_at',
                'packed_at',
                'shipped_at',
                'delivered_at',
                'cancelled_at',
                'refunded_at',
            ]);
        });

        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN order_status 
             ENUM('pending','processing','completed','cancelled') 
             DEFAULT 'pending'"
        );
    }
};

