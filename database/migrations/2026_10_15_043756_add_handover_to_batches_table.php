<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedBigInteger('collector_id')->nullable();
            $table->timestamp('handover_requested_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['collector_id', 'handover_requested_at', 'handed_over_at']);
        });
    }
};
