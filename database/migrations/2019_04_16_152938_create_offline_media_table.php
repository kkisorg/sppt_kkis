<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfflineMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_media', function (Blueprint $table) {
            $table->unsignedInteger('media_id');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

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
        Schema::dropIfExists('offline_media');
    }
}
