<?php

use App\Enums\PathBackgroundType;
use App\Enums\PathBackgroundColor;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('path_bg_type')->default(PathBackgroundType::COLOR)->after('business_name');
            $table->string('path_bg_value')->default(PathBackgroundColor::DEFAULT)->after('business_name')->nullable();

            $table->dropColumn('path_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('path_bg_type');
            $table->dropColumn('path_bg_value');

            $table->string('path_bg_color')->nullable()->after('business_name');
        });
    }
};
