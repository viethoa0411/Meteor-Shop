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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('marked_as_received_by')->nullable()->after('processed_by');
            $table->timestamp('marked_as_received_at')->nullable()->after('marked_as_received_by');

            $table->foreign('marked_as_received_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['marked_as_received_by']);
            $table->dropColumn(['marked_as_received_by', 'marked_as_received_at']);
        });
    }
};

