<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Quality facts
            $table->string('quality_level')->default('high'); // high | medium | low
            $table->string('grade')->nullable();              // Alba, C5, C4, H1 ...
            $table->text('ingredients');
            $table->unsignedInteger('net_quantity');          // e.g. 100
            $table->string('unit')->default('g');             // g | ml
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_code')->nullable();
            $table->boolean('is_organic')->default(false);
            $table->string('growing_method')->nullable();     // organic | compost | inorganic

            // Selling
            $table->decimal('price', 10, 2);                  // USD
            $table->unsignedInteger('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};