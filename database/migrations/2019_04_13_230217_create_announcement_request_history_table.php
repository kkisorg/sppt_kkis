<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementRequestHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_request_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('announcement_request_id');
            $table->unsignedInteger('revision_no');
            $table->string('organization_name');
            $table->string('title');
            $table->text('content');
            $table->unsignedInteger('duration');
            $table->unsignedBigInteger('event_timestamp');
            $table->unsignedInteger('creator_id');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

            $table->foreign('announcement_request_id', 'revision_no')
                ->references('id', 'revision_no')->on('announcement_request')
                ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('announcement_request_history');
    }
}
