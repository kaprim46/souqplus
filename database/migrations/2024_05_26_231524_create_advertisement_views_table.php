<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertisement_views', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->foreignId('user_id')->nullable();
            $table->foreignId('advertisement_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisement_views');
    }
};
