<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_id')->unique();
            $table->string('mobile');
            $table->boolean('is_active')->default(true);
            $table->decimal('latitude', 10, 8)->index('marketplace_latitude');
            $table->decimal('longitude', 11, 8)->index('marketplace_longitude');
            $table->string('password');
            $table->string('description')->nullable();
            $table->json('rate_and_review')->nullable();
            $table->string('reset_password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
