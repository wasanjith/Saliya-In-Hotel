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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Table name/number
            $table->integer('capacity'); // Number of seats
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->string('location')->nullable(); // Optional location description
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
