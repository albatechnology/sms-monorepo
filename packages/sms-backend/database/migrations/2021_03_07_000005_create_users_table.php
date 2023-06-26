<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->nullable()->constrained('subscribtion_users');
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->datetime('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->tinyInteger('type')->default(1)->index();
            $table->foreignId('channel_id')->nullable()->constrained();
            // $table->foreignId('company_id')->nullable()->constrained(); // unused
            $table->foreignId('supervisor_type_id')->nullable()->constrained();
            $table->foreignId('supervisor_id')->nullable()->constrained('users');
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
