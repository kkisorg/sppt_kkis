<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementRequestMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_request_media', function (Blueprint $table) {
            $table->unsignedInteger('announcement_request_id');
            $table->unsignedInteger('media_id');

            $table->foreign('announcement_request_id')
                ->references('id')->on('announcement_request')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('media_id')
                ->references('id')->on('media')
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
        Schema::dropIfExists('announcement_request_media');
    }
}
