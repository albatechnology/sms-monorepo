<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelIdToSmsChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_channels', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->integer('channel_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_channels', function (Blueprint $table) {
            $table->dropColumn(['channel_id','user_id']);
        });
    }
}
