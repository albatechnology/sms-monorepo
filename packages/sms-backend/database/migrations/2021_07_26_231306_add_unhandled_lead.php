<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnhandledLead extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_unhandled')->index()->default(0)->after('is_new_customer');
        });

        Schema::table('supervisor_types', function (Blueprint $table) {
            $table->boolean('can_assign_lead')->index()->default(0)->after('level');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['is_unhandled']);
        });

        Schema::table('supervisor_types', function (Blueprint $table) {
            $table->dropColumn(['can_assign_lead']);
        });
    }
}