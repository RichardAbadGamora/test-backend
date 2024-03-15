<?php

use App\Enums\InvitationChannel;
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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('token');
            $table->unsignedBigInteger('inviter_id');
            $table->unsignedBigInteger('path_id');
            $table->string('channel')->default(InvitationChannel::MAIL);
            $table->string('invitee_email')->nullable();
            $table->string('invitee_contact_no')->nullable();
            $table->dateTimeTz('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
