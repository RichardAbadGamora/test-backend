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
        // sqlite support: isolate renameColumn
        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('visibility', 'access');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('deletable')->default(true);
            $table->boolean('singleton')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('deletable');
            $table->dropColumn('singleton');
            $table->renameColumn('access', 'visibility');
        });
    }
};
