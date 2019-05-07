<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementOfflineDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_offline_distribution', function (Blueprint $table) {
            $table->unsignedInteger('announcement_id');
            $table->unsignedInteger('offline_distribution_id');
            $table->text('content');

            $table->foreign('announcement_id')
                ->references('id')->on('announcement')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('offline_distribution_id', 'aod_od_id_foreign')
                ->references('id')->on('offline_distribution')
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
        Schema::dropIfExists('announcement_offline_distribution');
    }
}
