<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Drop tables
        Schema::dropIfExists('transaction_logs');
        Schema::dropIfExists('wallet_withdrawals');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('wallets');

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot recreate tables in down() - data would be lost anyway
    }
};

