<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('notification_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->timestamps();

            $table->unique(['user_id', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_devices');
    }
}