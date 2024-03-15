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
        Schema::create('phase_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('content_type')->nullable();
            $table->string('content_value')->nullable();
            $table->unsignedBigInteger('phase_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_items');
    }
};
