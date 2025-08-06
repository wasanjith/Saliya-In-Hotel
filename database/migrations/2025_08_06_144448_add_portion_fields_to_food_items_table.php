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
            // Add portion fields
            $table->decimal('full_portion_dine_in_price', 10, 2)->nullable()->after('takeaway_price');
            $table->decimal('full_portion_takeaway_price', 10, 2)->nullable()->after('full_portion_dine_in_price');
            $table->decimal('half_portion_dine_in_price', 10, 2)->nullable()->after('full_portion_takeaway_price');
            $table->decimal('half_portion_takeaway_price', 10, 2)->nullable()->after('half_portion_dine_in_price');
            $table->boolean('has_half_portion')->default(false)->after('half_portion_takeaway_price');
            $table->string('full_portion_name')->default('Full Portion')->after('has_half_portion');
            $table->string('half_portion_name')->default('Half Portion')->after('full_portion_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->dropColumn([
                'full_portion_dine_in_price',
                'full_portion_takeaway_price',
                'half_portion_dine_in_price',
                'half_portion_takeaway_price',
                'has_half_portion',
                'full_portion_name',
                'half_portion_name'
            ]);
        });
    }
};
