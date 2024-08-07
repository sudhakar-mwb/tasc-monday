<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTascPlateformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasc_plateform', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->string('plateform_name'); // Platform name
            $table->string('plateform_signuplink'); // Platform signup link
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasc_plateform');
    }
}
