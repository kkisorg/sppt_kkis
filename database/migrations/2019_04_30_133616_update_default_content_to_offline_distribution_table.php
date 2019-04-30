<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDefaultContentToOfflineDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_distribution', function (Blueprint $table) {
            $table->text('header')->nullable()->change();
            $table->text('footer')->nullable()->change();
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
            $table->text('header')->nullable(false)->change();
            $table->text('footer')->nullable(false)->change();
        });
    }
}
