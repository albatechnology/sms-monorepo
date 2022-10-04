<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoveringsTable extends Migration
{
    public function up()
    {
        Schema::create('coverings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->nullable();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'type']);
        });
    }
}
