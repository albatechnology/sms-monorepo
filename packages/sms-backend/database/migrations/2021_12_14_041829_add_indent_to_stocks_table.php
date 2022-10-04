<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndentToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('stocks', function (Blueprint $table) {
        //     $table->integer('indent')->default(0)->after('stock');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('stocks', function (Blueprint $table) {
        //     $table->dropColumn(['indent']);
        // });
    }
}
