<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old columns
        Schema::table('food_items', function (Blueprint $table) {
            if (Schema::hasColumn('food_items', 'full_portion_price')) {
                $table->dropColumn('full_portion_price');
            }
            if (Schema::hasColumn('food_items', 'half_portion_price')) {
                $table->dropColumn('half_portion_price');
            }
            if (Schema::hasColumn('food_items', 'full_portion_name')) {
                $table->dropColumn('full_portion_name');
            }
            if (Schema::hasColumn('food_items', 'half_portion_name')) {
                $table->dropColumn('half_portion_name');
            }
            // Remove legacy single-price rice columns if present
            if (Schema::hasColumn('food_items', 'samba_rice_price')) {
                $table->dropColumn('samba_rice_price');
            }
            if (Schema::hasColumn('food_items', 'basmathi_rice_price')) {
                $table->dropColumn('basmathi_rice_price');
            }
        });

        // Add new rice-type portion price columns
        Schema::table('food_items', function (Blueprint $table) {
            $table->decimal('full_basmathi_price', 10, 2)->nullable()->after('price');
            $table->decimal('half_basmathi_price', 10, 2)->nullable()->after('full_basmathi_price');
            $table->decimal('full_samba_price', 10, 2)->nullable()->after('half_basmathi_price');
            $table->decimal('half_samba_price', 10, 2)->nullable()->after('full_samba_price');
        });
    }

    public function down(): void
    {
        // Drop new columns
        Schema::table('food_items', function (Blueprint $table) {
            $table->dropColumn([
                'full_basmathi_price',
                'half_basmathi_price',
                'full_samba_price',
                'half_samba_price',
            ]);
        });

        // Re-add old columns
        Schema::table('food_items', function (Blueprint $table) {
            $table->decimal('full_portion_price', 10, 2)->nullable();
            $table->decimal('half_portion_price', 10, 2)->nullable();
            $table->string('full_portion_name')->default('Full Portion');
            $table->string('half_portion_name')->default('Half Portion');
            $table->decimal('samba_rice_price', 10, 2)->nullable();
            $table->decimal('basmathi_rice_price', 10, 2)->nullable();
        });
    }
};