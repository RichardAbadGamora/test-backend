<?php

use App\Enums\PageBackgroundColor;
use App\Enums\PathBackgroundType;
use App\Enums\PathBackgroundColor;
use App\Enums\PathVisibility;
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
        Schema::create('paths', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('icon')->nullable();
            $table->string('visibility')->default(PathVisibility::PRIVATE);
            $table->string('bg_type')->default(PathBackgroundType::COLOR);
            $table->string('bg_value')->default(PathBackgroundColor::DEFAULT);
            $table->string('page_bg_color')->default(PageBackgroundColor::COLOR_GRAY_50);
            $table->integer('base_text_size')->default(16);
            $table->string('typo_color')->default(PathBackgroundColor::COLOR_GRAY_900);
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paths');
    }
};
