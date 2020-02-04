<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementOnlineMediaPublishRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_online_media_publish_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('announcement_online_media_publish_schedule_id');
            $table->text('request_parameter');
            $table->text('response_content')->nullable($value = true);
            $table->text('error')->nullable($value = true);
            $table->string('status');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

            $table->foreign('announcement_online_media_publish_schedule_id', 'aompr_aomps_id_foreign')
                ->references('id')->on('announcement_online_media_publish_schedule')
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
        Schema::dropIfExists('announcement_online_media_publish_record');
    }
}
