<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropInviterIdForeignKeyFromTascInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasc_invitation', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['inviter_id']);
            
            // Drop the inviter_id column
            $table->dropColumn('inviter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasc_invitation', function (Blueprint $table) {
            // Re-add the inviter_id column
            $table->unsignedBigInteger('inviter_id')->nullable();
            
            // Re-add the foreign key constraint
            $table->foreign('inviter_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
