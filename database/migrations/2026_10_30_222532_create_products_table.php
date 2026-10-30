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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('reference', 30);
            $table->string('name', 150)->index();
            $table->text('description')->nullable();
            $table->string('unit', 20)->default('piece');
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('alert_threshold')->default(5);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
