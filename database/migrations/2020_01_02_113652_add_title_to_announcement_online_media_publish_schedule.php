<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleToAnnouncementOnlineMediaPublishSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcement_online_media_publish_schedule', function (Blueprint $table) {
            $table->string('title', 500)->after('online_media_id')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_online_media_publish_schedule', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
