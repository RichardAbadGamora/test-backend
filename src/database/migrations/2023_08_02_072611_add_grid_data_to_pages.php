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
            $table->unsignedInteger('grid_x')->default(0);
            $table->unsignedInteger('grid_y')->default(0);
            $table->unsignedInteger('grid_width')->default(1);
            $table->unsignedInteger('grid_height')->default(25);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('grid_x');
            $table->dropColumn('grid_y');
            $table->dropColumn('grid_width');
            $table->dropColumn('grid_height');
        });
    }
};
