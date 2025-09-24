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
        Schema::create('advertisement_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->foreign('advertisement_id')
                ->references('id')
                ->on('advertisements')
                ->onDelete('cascade');
            $table->unsignedBigInteger('filter_id');
            $table->foreign('filter_id')
                ->references('id')
                ->on('filters')
                ->onDelete('cascade');
            $table->string('value');
            $table->unique(['advertisement_id', 'filter_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_filters');
    }
};
