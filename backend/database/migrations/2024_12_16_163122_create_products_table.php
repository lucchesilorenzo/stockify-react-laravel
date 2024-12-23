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
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->float('price');
            $table->integer('quantity');
            $table->integer('max_quantity');
            $table->integer('vat_rate');
            $table->text('description')->nullable();
            $table->string('status')->default('IN_STOCK');
            $table->string('image')->default('placeholder.svg');
            $table->timestamps();
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
