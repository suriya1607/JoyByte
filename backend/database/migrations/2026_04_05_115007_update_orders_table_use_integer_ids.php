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
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing enum constraints
            $table->dropColumn('status');
            $table->dropColumn('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Add as tinyInteger (0-255) for ID mapping
            $table->unsignedTinyInteger('status')->default(2)->after('shop_id'); // 2 = confirmed
            $table->unsignedTinyInteger('payment_method')->default(1)->after('status'); // 1 = cod
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Restore as enum if rolling back
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'])->default('confirmed');
            $table->enum('payment_method', ['cod'])->default('cod');
        });
    }
};
