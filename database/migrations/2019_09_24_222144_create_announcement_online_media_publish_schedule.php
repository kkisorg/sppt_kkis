<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementOnlineMediaPublishSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_online_media_publish_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('announcement_id');
            $table->unsignedInteger('online_media_id');
            $table->text('content');
            $table->integer('sequence');
            $table->unsignedBigInteger('publish_timestamp');
            $table->string('status');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();


            $table->foreign('announcement_id', 'aomps_announcement_id_foreign')
                ->references('id')->on('announcement')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('online_media_id', 'aomps_online_media_id_foreign')
                ->references('media_id')->on('online_media')
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
        Schema::dropIfExists('announcement_online_media_publish_schedule');
    }
}
