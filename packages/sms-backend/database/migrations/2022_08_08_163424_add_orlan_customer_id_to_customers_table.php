<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrlanCustomerIdToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('orlan_customer_id', 50)->nullable()->after('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('orlan_user_id', 50)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['orlan_customer_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['orlan_user_id']);
        });
    }
}
