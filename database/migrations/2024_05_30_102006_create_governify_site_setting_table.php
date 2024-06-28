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
        Schema::create('governify_site_setting', function (Blueprint $table) {
            $table->id();
            $table->string('ui_settings');
            $table->string('logo_name')->nullable();
            $table->string('logo_location')->nullable();
            $table->string('status')->default(0);
            $table->string('domain')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governify_site_setting');
    }
};
