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
        Schema::table('users', function (Blueprint $table) {
            $table->index('business_name');
            $table->index('contact_no');
            $table->index('email');
        });

        Schema::table('paths', function (Blueprint $table) {
            $table->index('name');
            $table->index('business_name');
            $table->index('user_id');
        });

        Schema::table('phases', function (Blueprint $table) {
            $table->index('name');
            $table->index('path_id');
        });

        Schema::table('phase_items', function (Blueprint $table) {
            $table->index('name');
            $table->index('phase_id');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->index('name');
            $table->index(['attachable_id', 'attachable_type']);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->index('filename');
            $table->index('path_id');
            $table->index('user_id');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->index('name');
            $table->index('path_id');
            $table->index('user_id');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->index('token');
            $table->index('inviter_id');
            $table->index('path_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('name');
            $table->index('path_id');
            $table->index('user_id');
            $table->index('order');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->index('name');
            $table->index('path_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['business_name']);
            $table->dropIndex(['contact_no']);
            $table->dropIndex(['email']);
        });

        Schema::table('paths', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['business_name']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('phases', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['path_id']);
        });

        Schema::table('phase_items', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['phase_id']);
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['attachable_id', 'attachable_type']);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex(['filename']);
            $table->dropIndex(['path_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['path_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['token']);
            $table->dropIndex(['inviter_id']);
            $table->dropIndex(['path_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['path_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['path_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
