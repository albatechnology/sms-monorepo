<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('user_sms_id')->nullable();
            $table->integer('sms_channel_id')->nullable();
            $table->integer('product_brand_id')->nullable();

            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('channel_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['user_sms_id', 'sms_channel_id', 'product_brand_id']);

            $table->foreignId('user_id')->nullable('false')->change();
            $table->foreignId('channel_id')->nullable('false')->change();
        });
    }
}
