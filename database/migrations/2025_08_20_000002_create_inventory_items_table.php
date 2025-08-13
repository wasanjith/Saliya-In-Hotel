<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('inventory_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('category_id')->constrained('inventory_categories')->onDelete('cascade');
			$table->string('name');
			$table->string('unit')->default('g'); // g, kg, ml, l, pcs
			$table->decimal('quantity', 12, 3)->default(0);
			$table->decimal('reorder_level', 12, 3)->default(0);
			$table->boolean('is_active')->default(true);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('inventory_items');
	}
};


