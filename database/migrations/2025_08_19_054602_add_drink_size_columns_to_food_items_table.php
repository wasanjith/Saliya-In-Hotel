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
        Schema::table('food_items', function (Blueprint $table) {
            // JSON column to store drink sizes and prices
            $table->json('beverage_prices')->nullable()->after('half_samba_price');
            
            // Boolean to indicate if this item has multiple drink sizes
            $table->boolean('has_drink_sizes')->default(false)->after('beverage_prices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->dropColumn([
                'beverage_prices',
                'has_drink_sizes'
            ]);
        });
    }
};
