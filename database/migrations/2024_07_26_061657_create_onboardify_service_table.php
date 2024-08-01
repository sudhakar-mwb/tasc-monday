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
        Schema::create('onboardify_service', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('file_location')->nullable();
            $table->longText('service_setting_data');
            $table->string('board_id');
            $table->longText('service_column_value_filter');
            $table->longText('service_form_link');
            $table->longText('service_chart_link');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedBigInteger('profile_id');
            $table->foreign('profile_id')->references('id')->on('onboardify_profiles')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardify_service');
    }
};
