<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipientEmailToMonthlyOfflineDistributionScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_offline_distribution_schedule', function (Blueprint $table) {
            $table->string('recipient_email', 500)->after('deadline_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_offline_distribution_schedule', function (Blueprint $table) {
            $table->dropColumn('recipient_email');
        });
    }
}
