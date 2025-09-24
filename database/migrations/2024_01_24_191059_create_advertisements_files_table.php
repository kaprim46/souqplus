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
        Schema::create('advertisements_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisements_id');
            $table->unsignedBigInteger('file_id');
            $table->foreign('advertisements_id')
                ->references('id')
                ->on('advertisements')
                ->onDelete('cascade');
            $table->foreign('file_id')
                ->references('id')
                ->on('files')
                ->onDelete('cascade');
            $table->boolean('is_main')
                ->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_files');
    }
};
