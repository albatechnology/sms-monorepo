<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreatePromosTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('promos');

        Schema::create('promos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('lead_category_id')->nullable()->constrained();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {

    }
}