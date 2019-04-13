<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementRequestHistoryMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_request_history_media', function (Blueprint $table) {
            $table->unsignedInteger('announcement_request_history_id');
            $table->unsignedInteger('media_id');

            $table->foreign('announcement_request_history_id', 'arh_media_arh_id_foreign')
                ->references('id')->on('announcement_request_history')
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
        Schema::dropIfExists('announcement_request_history_media');
    }
}
