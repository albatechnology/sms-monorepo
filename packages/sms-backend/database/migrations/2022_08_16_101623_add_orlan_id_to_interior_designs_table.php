<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrlanIdToInteriorDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interior_designs', function (Blueprint $table) {
            $table->string('orlan_id', 30)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interior_designs', function (Blueprint $table) {
            $table->dropColumn(['orlan_id']);
        });
    }
}
