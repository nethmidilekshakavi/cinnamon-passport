<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->foreignId('grower_id')->constrained('users')->cascadeOnDelete();
            $table->string('plot', 30);
            $table->decimal('weight_kg', 8, 2);
            $table->date('harvest_date');
            $table->unsignedTinyInteger('drying_days');
            $table->string('grade', 20)->nullable();
            $table->string('source', 10)->default('app');
            $table->string('status', 20)->default('recorded');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
