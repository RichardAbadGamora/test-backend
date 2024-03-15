<?php

use App\Enums\PageGapPresetValue;
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
            $table->string('gap_type')->after('pages_per_row');
            $table->string('gap_value')->after('pages_per_row')->default(PageGapPresetValue::NARROW);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gap_type', 'gap_value']);
        });
    }
};
