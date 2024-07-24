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
        Schema::create('onboardify_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('users');
            $table->boolean('make_default')->default(0);
            $table->timestamps();
        });
    }
    // 0 -> 
    // 1 -> default
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardify_profiles');
    }
};
