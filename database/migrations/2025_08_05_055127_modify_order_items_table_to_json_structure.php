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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the existing individual columns
            $table->dropForeign(['food_item_id']);
            $table->dropColumn([
                'food_item_id',
                'item_name',
                'quantity',
                'unit_price',
                'total_price',
                'notes'
            ]);
            
            // Add new JSON column for storing multiple food items
            $table->json('items')->after('order_id');
            
            // Add total amount for the entire order item record
            $table->decimal('total_amount', 10, 2)->after('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn(['items', 'total_amount']);
            
            // Restore the original columns
            $table->foreignId('food_item_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
        });
    }
};
