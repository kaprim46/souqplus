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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('sender_id')
                ->nullable();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->enum('type', [
                'add_your_ad_to_favorites',
                'followed_business_ad',
                'new_store_follower',
                'admin_notification',
                'followed_user_ad',
                'new_follower',
                'ad_approved',
                'ad_rejected',
                'new_comment',
                'ad_pending'
            ]);

            $table->text('message')
                ->nullable();

            $table->unsignedBigInteger('ad_id')
                ->nullable();

            $table->boolean('read')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
