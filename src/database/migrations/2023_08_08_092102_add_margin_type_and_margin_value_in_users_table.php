<?php

use App\Enums\ContainerMarginPresetValue;
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
            $table->string('margin_type')->after('pages_per_row');
            $table->string('margin_value')->after('pages_per_row')->default(ContainerMarginPresetValue::NARROW);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['margin_type', 'margin_value']);
        });
    }
};
