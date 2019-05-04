<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyOfflineDistributionScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_offline_distribution_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('default_header');
            $table->text('default_footer');
            $table->unsignedInteger('offline_media_id');
            $table->enum('distribution_weekofmonth', [1, 2, 3, 4, 5]);
            $table->enum('distribution_dayofweek', [0, 1, 2, 3, 4, 5, 6]);
            $table->time('distribution_time');
            $table->enum('deadline_dayofweek', [0, 1, 2, 3, 4, 5, 6]);
            $table->time('deadline_time');
            $table->unsignedBigInteger('create_timestamp');
            $table->unsignedBigInteger('update_timestamp')->nullable();

            $table->foreign('offline_media_id')
                ->references('media_id')->on('offline_media')
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
        Schema::dropIfExists('monthly_offline_distribution_schedule');
    }
}
