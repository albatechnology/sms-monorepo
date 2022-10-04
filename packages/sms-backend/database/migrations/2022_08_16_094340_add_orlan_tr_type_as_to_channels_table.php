<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrlanTrTypeAsToChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->char('orlan_tr_type_as', 5)->nullable()->after('orlan_tr_type');
            $table->char('orlan_tr_type_sa', 5)->nullable()->after('orlan_tr_type_as'); // Sales Advance
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn(['orlan_tr_type_as','orlan_tr_type_sa']);
        });
    }
}
