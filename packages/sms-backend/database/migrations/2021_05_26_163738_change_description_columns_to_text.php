<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDescriptionColumnsToText extends Migration
{
    const tables = ['discounts'];

    public function up()
    {
        foreach (self::tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
        }
    }

    public function down()
    {
        foreach (array_reverse(self::tables) as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('description')->nullable()->change();
            });
        }
    }
}
