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
        Schema::table('advertisements', function (Blueprint $table) {
            if (!Schema::hasColumn('advertisements', 'sub_sub_category_id')) {
                $table->unsignedBigInteger('sub_sub_category_id')->nullable()->after('sub_category_id');
                $table->foreign('sub_sub_category_id')
                    ->references('id')
                    ->on('sub_sub_categories')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            if (Schema::hasColumn('advertisements', 'sub_sub_category_id')) {
                $table->dropForeign(['sub_sub_category_id']);
                $table->dropColumn('sub_sub_category_id');
            }
        });
    }
}; 