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
        Schema::create('governify_service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('file_location')->nullable();
            $table->string('form');
            $table->integer('service_categories_request_index')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedBigInteger('service_categorie_id');
            $table->foreign('service_categorie_id')->references('id')->on('governify_service_categories');
            // $table->foreignId('service_categorie_id')->constrained('governify_service_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governify_service_requests');
    }
};
