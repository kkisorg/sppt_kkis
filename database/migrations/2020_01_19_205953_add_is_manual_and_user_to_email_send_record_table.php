<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsManualAndUserToEmailSendRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_send_record', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false);
            $table->unsignedInteger('creator_id')->nullable();

            $table->foreign('creator_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_send_record', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn(['is_manual', 'creator_id']);
        });
    }
}
