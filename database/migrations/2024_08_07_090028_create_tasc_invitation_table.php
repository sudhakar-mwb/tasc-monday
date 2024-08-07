<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTascInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasc_invitation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inviter_id');
            $table->string('invitee_email');
            $table->string('invitation_status')->default('pending'); // You can set default value if required
            $table->boolean('onboardify_status')->default(false);
            $table->boolean('incorpify_status')->default(false);
            $table->boolean('governify_status')->default(false);
            $table->timestamps();

            // Add foreign key constraint if you have a related users table
            $table->foreign('inviter_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasc_invitation');
    }
}
