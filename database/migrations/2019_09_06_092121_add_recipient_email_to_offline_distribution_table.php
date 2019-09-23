<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipientEmailToOfflineDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_distribution', function (Blueprint $table) {
            $table->string('recipient_email', 500)->after('deadline_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_distribution', function (Blueprint $table) {
            $table->dropColumn('recipient_email');
        });
    }
}
