<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaTable extends Migration
{
    public function up()
    {
        // Schema::create('qa_topics', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('subject');
        //     $table->foreignId('creator_id')->nullable()->constrained('users');
        //     $table->unsignedBigInteger('latest_message_id')->nullable()->index();

        //     $table->softDeletes();
        //     $table->timestamps();

        //     $table->index('updated_at');
        // });

        // Schema::create('qa_messages', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->foreignId('topic_id')->constrained('qa_topics');
        //     $table->foreignId('sender_id')->constrained('users');
        //     $table->text('content');
        //     $table->softDeletes();
        //     $table->timestamps();
        // });

        // Schema::table('qa_topics', function (Blueprint $table) {
        //     $table->foreign('latest_message_id')->references('id')->on('qa_messages')->nullOnDelete();
        // });
    }
}
