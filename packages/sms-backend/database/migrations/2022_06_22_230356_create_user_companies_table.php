<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('user_companies', function (Blueprint $table) {
        //     $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('company_id')->constrained()->cascadeOnDelete(); // unused
        // });
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('company_ids')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('user_companies');
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn(['company_ids']);
        // });
    }
}
