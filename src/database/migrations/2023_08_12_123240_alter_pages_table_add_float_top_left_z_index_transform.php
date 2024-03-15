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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('float_top')->nullable();
            $table->string('float_left')->nullable();
            $table->string('float_z_index')->nullable();
            $table->string('float_transform')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('float_top');
            $table->dropColumn('float_left');
            $table->dropColumn('float_z_index');
            $table->dropColumn('float_transform');
        });
    }
};
