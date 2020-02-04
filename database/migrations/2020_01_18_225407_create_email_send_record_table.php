<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSendRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_send_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('email_send_schedule_id');
            $table->text('request_parameter');
            $table->text('error')->nullable($value = true);;
            $table->string('status');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

            $table->foreign('email_send_schedule_id')
                ->references('id')->on('email_send_schedule')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_send_record');
    }
}
