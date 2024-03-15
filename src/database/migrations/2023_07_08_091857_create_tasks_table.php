<?php

use App\Enums\TaskAccess;
use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('path_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('name');
            $table->string('status')->default(TaskStatus::OPEN);
            $table->string('access');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
