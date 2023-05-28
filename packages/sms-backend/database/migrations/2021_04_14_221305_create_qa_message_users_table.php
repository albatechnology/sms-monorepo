<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaMessageUsersTable extends Migration
{
    public function up()
    {
        // Schema::create('qa_message_user', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->foreignId('topic_id')->constrained('qa_topics');
        //     $table->foreignId('message_id')->constrained('qa_messages');
        //     $table->foreignId('user_id')->constrained();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('qa_message_user');
    }
}
