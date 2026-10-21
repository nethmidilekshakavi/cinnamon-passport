<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_trials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('participant', 20);
            $table->string('channel', 3);
            $table->unsignedTinyInteger('scenario');
            $table->date('trial_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedSmallInteger('errors')->default(0);
            $table->unsignedTinyInteger('data_errors')->nullable();
            $table->boolean('success')->default(false);
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->timestamps();

            $table->index(['participant', 'channel']);
        });

        Schema::create('study_sus', function (Blueprint $table) {
            $table->id();
            $table->string('participant', 20);
            $table->string('channel', 3);
            $table->json('answers');
            $table->decimal('score', 5, 1);
            $table->timestamps();

            $table->unique(['participant', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sus');
        Schema::dropIfExists('study_trials');
    }
};
