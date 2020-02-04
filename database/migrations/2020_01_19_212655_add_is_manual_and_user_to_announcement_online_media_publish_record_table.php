<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsManualAndUserToAnnouncementOnlineMediaPublishRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcement_online_media_publish_record', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false);
            $table->unsignedInteger('creator_id')->nullable();

            $table->foreign('creator_id', 'aompr_creator_id_foreign')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_online_media_publish_record', function (Blueprint $table) {
            $table->dropForeign('aompr_creator_id_foreign');
            $table->dropColumn(['is_manual', 'creator_id']);
        });
    }
}
