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
        Schema::create('incorpify_site_setting', function (Blueprint $table) {
            $table->id();
            $table->json('ui_settings');
            $table->string('logo_name')->nullable();
            $table->string('logo_location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incorpify_site_setting');
    }
};
