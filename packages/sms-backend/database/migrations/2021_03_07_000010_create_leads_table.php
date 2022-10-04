<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('type')->index();
            $table->unsignedTinyInteger('status')->index();
            $table->string('label')->nullable();
            $table->boolean('is_new_customer')->default(0)->nullable();
            $table->unsignedBigInteger('group_id')->default(0)->nullable();

            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('channel_id')->constrained();

            $table->json('status_history')->nullable();
            $table->dateTime('status_change_due_at')->nullable();
            $table->boolean('has_pending_status_change')->nullable()->default(0)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(["customer_id", "group_id"]);
        });
    }
}
