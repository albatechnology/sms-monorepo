<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('follow_up_datetime');
            $table->longText('feedback')->nullable();
            $table->unsignedTinyInteger('follow_up_method')->nullable()->index();
            $table->unsignedTinyInteger('status')->nullable()->index();
            $table->foreignId('lead_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('channel_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('latest_activity_comment_id')->nullable();
            $table->unsignedBigInteger('activity_comment_count')->default(0);

            $table->dateTime('reminder_datetime')->nullable();
            $table->string('reminder_note')->nullable();
            $table->boolean('reminder_sent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activity_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('content')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('activity_comment_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('latest_activity_comment_id')
                  ->references('id')
                  ->on('activity_comments')
                  ->nullOnDelete();
        });
    }
}
