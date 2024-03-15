<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const COLOR_GRAY_50 = '#f8fafc';
    const COLOR_GRAY_100 = '#f1f5f9';
    const COLOR_GRAY_900 = '#111827';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('pages_per_row')->default(4)->after('business_name');
            $table->string('path_bg_color')->after('business_name')->default(static::COLOR_GRAY_100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pages_per_row', 'path_bg_color']);
        });
    }
};
