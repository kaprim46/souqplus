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
        Schema::create('advertisement_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->foreign('advertisement_id')
                ->references('id')
                ->on('advertisements')
                ->onDelete('cascade');
            $table->unsignedBigInteger('user_id')
                ->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->string('name')
                ->nullable();
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->mediumText('rejection_reason')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_comments');
    }
};
