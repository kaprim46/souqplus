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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->longText('description')
                ->nullable();

            $table->string('slug')
                ->unique();

            $table->unsignedBigInteger('category_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories');

            $table->unsignedBigInteger('sub_category_id')->nullable();

            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_categories');

            $table->unsignedBigInteger('sub_sub_category_id')->nullable();

            $table->foreign('sub_sub_category_id')
                ->references('id')
                ->on('sub_sub_categories');

            $table->decimal('latitude', 10, 8);

            $table->decimal('longitude', 11, 8);

            $table->string('country_code');

            $table->string('phone_number');

            $table->decimal('price', 10, 2);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->mediumText('reject_reason')
                ->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->json('custom_fields')
                ->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')->nullOnDelete();

            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')->nullOnDelete();


            $table->integer('fake_views')
                ->default(0); // This is a fake views column for testing purposes

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
