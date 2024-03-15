<?php

use App\Enums\PathStatus;
use App\Enums\Role;
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
        Schema::create('user_paths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('path_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('pinned')->default(false);
            $table->dateTime('pinned_at')->nullable();
            $table->string('role')->default(Role::AUTHORIZED_USER);
            $table->string('status')->default(PathStatus::ACTIVE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_paths');
    }
};
