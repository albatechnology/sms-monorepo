<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            $table->string('name');
            // $table->foreignId('company_id')->constrained(); // unused
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
