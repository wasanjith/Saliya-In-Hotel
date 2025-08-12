<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullPriceAndHalfPriceToFoodItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->decimal('full_price', 10, 2)->nullable()->after('price');
            $table->decimal('half_price', 10, 2)->nullable()->after('full_price');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->dropColumn(['full_price', 'half_price']);
        });
    }
}
