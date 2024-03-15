<?php

use App\Enums\PageBackgroundColor;
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
        Schema::table('paths', function (Blueprint $table) {
            $table->string('page_bg_color')->default(PageBackgroundColor::COLOR_WHITE)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->string('page_bg_color')->default(PageBackgroundColor::COLOR_GRAY_50)->change();
        });
    }
};
