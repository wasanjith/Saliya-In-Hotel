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
        Schema::table('food_items', function (Blueprint $table) {
            // New unified pricing fields
            $table->decimal('price', 10, 2)->default(0)->after('image');
            $table->decimal('full_portion_price', 10, 2)->nullable()->after('price');
            $table->decimal('half_portion_price', 10, 2)->nullable()->after('full_portion_price');
        });

        // Migrate existing data into new fields
        // Use takeaway prices as base since dine-in will be a surcharge on totals
        DB::statement(
            "UPDATE food_items 
            SET 
                price = COALESCE(full_portion_takeaway_price, takeaway_price, dine_in_price, 0),
                full_portion_price = COALESCE(full_portion_takeaway_price, full_portion_dine_in_price, takeaway_price, dine_in_price),
                half_portion_price = COALESCE(half_portion_takeaway_price, half_portion_dine_in_price)
            "
        );

        Schema::table('food_items', function (Blueprint $table) {
            // Drop legacy per-order-type pricing columns
            if (Schema::hasColumn('food_items', 'dine_in_price')) {
                $table->dropColumn('dine_in_price');
            }
            if (Schema::hasColumn('food_items', 'takeaway_price')) {
                $table->dropColumn('takeaway_price');
            }
            if (Schema::hasColumn('food_items', 'full_portion_dine_in_price')) {
                $table->dropColumn('full_portion_dine_in_price');
            }
            if (Schema::hasColumn('food_items', 'full_portion_takeaway_price')) {
                $table->dropColumn('full_portion_takeaway_price');
            }
            if (Schema::hasColumn('food_items', 'half_portion_dine_in_price')) {
                $table->dropColumn('half_portion_dine_in_price');
            }
            if (Schema::hasColumn('food_items', 'half_portion_takeaway_price')) {
                $table->dropColumn('half_portion_takeaway_price');
            }
            if (Schema::hasColumn('food_items', 'samba_rice_price')) {
                $table->dropColumn('samba_rice_price');
            }
            if (Schema::hasColumn('food_items', 'basmathi_rice_price')) {
                $table->dropColumn('basmathi_rice_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            // Recreate legacy columns (without data backfill)
            $table->decimal('dine_in_price', 10, 2)->default(0)->after('image');
            $table->decimal('takeaway_price', 10, 2)->default(0)->after('dine_in_price');
            $table->decimal('full_portion_dine_in_price', 10, 2)->nullable()->after('takeaway_price');
            $table->decimal('full_portion_takeaway_price', 10, 2)->nullable()->after('full_portion_dine_in_price');
            $table->decimal('half_portion_dine_in_price', 10, 2)->nullable()->after('full_portion_takeaway_price');
            $table->decimal('half_portion_takeaway_price', 10, 2)->nullable()->after('half_portion_dine_in_price');
            $table->decimal('samba_rice_price', 10, 2)->nullable()->after('half_portion_takeaway_price');
            $table->decimal('basmathi_rice_price', 10, 2)->nullable()->after('samba_rice_price');
        });

        // Best-effort data restoration: map back from unified fields
        DB::statement(
            "UPDATE food_items 
            SET 
                dine_in_price = COALESCE(full_portion_price, price, 0),
                takeaway_price = COALESCE(price, full_portion_price, 0),
                full_portion_dine_in_price = full_portion_price,
                full_portion_takeaway_price = full_portion_price,
                half_portion_dine_in_price = half_portion_price,
                half_portion_takeaway_price = half_portion_price
            "
        );

        Schema::table('food_items', function (Blueprint $table) {
            // Drop unified columns
            if (Schema::hasColumn('food_items', 'half_portion_price')) {
                $table->dropColumn('half_portion_price');
            }
            if (Schema::hasColumn('food_items', 'full_portion_price')) {
                $table->dropColumn('full_portion_price');
            }
            if (Schema::hasColumn('food_items', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};

