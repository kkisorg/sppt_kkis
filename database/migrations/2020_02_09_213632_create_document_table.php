<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->string('file_path');
            $table->unsignedBigInteger('publish_timestamp');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('editor_id')->nullable();
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

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
        Schema::dropIfExists('document');
    }
}
