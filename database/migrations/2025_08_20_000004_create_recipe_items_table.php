<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('recipe_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
			$table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('restrict');
			$table->decimal('quantity', 12, 3); // quantity required per 1 full portion
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('recipe_items');
	}
};


