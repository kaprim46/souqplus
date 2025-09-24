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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                ->unique();

            $table->string('name');

            $table->string('email')
                ->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->string('password');

            $table->mediumText('bio')
                ->nullable();

            $table->enum('role',[
                'admin',
                'business',
                'customer',
            ])
                ->default('customer');

            $table->string('country_code')
                ->nullable();

            $table->string('phone_number')
                ->nullable();

            $table->timestamp('phone_number_verified_at')
                ->nullable();

            $table->string('avatar')
                ->nullable();

            $table->boolean('is_verified')
                ->default(false);

            $table->json('business_info')
                ->nullable();

            $table->bigInteger('otp_code')
                ->nullable();

            $table->bigInteger('forget_password_code')
                ->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')->nullOnDelete();

            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')->nullOnDelete();

            $table->text('fcm_token')
                ->nullable();

            $table->softDeletes();

            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
