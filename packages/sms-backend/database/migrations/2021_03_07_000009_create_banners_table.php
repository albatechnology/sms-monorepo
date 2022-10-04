<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bannerable');
            $table->boolean('is_active')->default(0)->nullable();
            $table->datetime('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('bannerable_type');
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
