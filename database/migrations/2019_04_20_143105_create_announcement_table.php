<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('announcement_request_id');
            $table->unsignedInteger('revision_no');
            $table->string('organization_name');
            $table->string('title');
            $table->text('content');
            $table->unsignedInteger('duration');
            $table->unsignedBigInteger('event_timestamp');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('editor_id')->nullable();
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

            $table->foreign('announcement_request_id')
                ->references('id')->on('announcement_request')
                ->onUpdate('cascade')->onDelete('cascade');;
            $table->foreign('creator_id')->references('id')->on('user');
            $table->foreign('editor_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement');
    }
}
