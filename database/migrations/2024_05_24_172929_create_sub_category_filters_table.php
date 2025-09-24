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
        Schema::create('sub_category_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('filter_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unique(['sub_category_id', 'filter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_category_filters');
    }
};
