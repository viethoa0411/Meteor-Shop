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
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            $table->string('zalo_link')->nullable();         
            $table->string('messenger_link')->nullable();     
            $table->string('phone_number')->nullable();       
            $table->boolean('show_zalo')->default(true);
            $table->boolean('show_messenger')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->timestamps();
        });
        // Tạo bản ghi mặc định
        \DB::table('contact_infos')->insert([
            'zalo_link' => 'https://zalo.me/0123456789',
            'messenger_link' => 'https://m.me/yourfanpage',
            'phone_number' => '0123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
