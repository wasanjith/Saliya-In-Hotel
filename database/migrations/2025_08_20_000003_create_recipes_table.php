<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('recipes', function (Blueprint $table) {
			$table->id();
			$table->foreignId('food_item_id')->constrained('food_items')->onDelete('cascade');
			$table->string('name');
			$table->text('notes')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('recipes');
	}
};


