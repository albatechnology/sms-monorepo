<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCompanyOptionsNullable extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('options')->nullable()->after('name')->change();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('options')->change();
        });
    }
}