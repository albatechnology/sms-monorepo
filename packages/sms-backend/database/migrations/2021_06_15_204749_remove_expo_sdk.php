<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveExpoSdk extends Migration
{
    public function up()
    {
        Schema::dropIfExists('exponent_push_notification_interests');
    }

    public function down()
    {
        Schema::create('exponent_push_notification_interests', function (Blueprint $table) {
            $table->string('key')->index();
            $table->string('value');

            $table->unique(['key', 'value']);
        });
    }
}