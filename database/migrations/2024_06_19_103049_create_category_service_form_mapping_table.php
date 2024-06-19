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
        Schema::create('category_service_form_mapping', function (Blueprint $table) {
            $table->id();
            //form id
            $table->unsignedBigInteger('service_form_id');
            $table->foreign('service_form_id')->references('id')->on('governify_service_request_forms')->nullable();
            // service id
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('governify_service_requests')->nullable();
            // service categorie id
            $table->unsignedBigInteger('categorie_id');
            $table->foreign('categorie_id')->references('id')->on('governify_service_categories')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_service_form_mapping');
    }
};
