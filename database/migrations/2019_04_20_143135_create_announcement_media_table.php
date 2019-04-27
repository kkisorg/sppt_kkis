<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_media', function (Blueprint $table) {
            $table->unsignedInteger('announcement_id');
            $table->unsignedInteger('media_id');
            $table->text('content');

            $table->foreign('announcement_id')
                ->references('id')->on('announcement')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('media_id')
                ->references('id')->on('media')
                ->onUpdate('cascade')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_media');
    }
}
