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
            $table->foreignId('lead_category_id')->nullable()->constrained();
            $table->foreignId('sub_lead_category_id')->nullable()->constrained('sub_lead_categories');
            $table->foreignId('parent_id')->nullable();
            $table->unsignedTinyInteger('type')->index();
            $table->unsignedTinyInteger('status')->index();
            $table->string('label')->nullable();
            $table->boolean('is_new_customer')->default(0)->nullable();
            $table->boolean('is_unhandled')->index()->default(0)->cascadeOnDelete();
            $table->unsignedBigInteger('group_id')->default(0)->nullable();

            $table->foreignId('user_id')->constrained();
            $table->foreignId('user_referral_id')->nullable()->index();;
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('channel_id')->constrained();

            $table->json('status_history')->nullable();
            $table->dateTime('status_change_due_at')->nullable();
            $table->boolean('has_pending_status_change')->nullable()->default(0)->index();
            $table->boolean('has_activity')->default(false);
            $table->text('interest')->nullable();
            $table->unsignedTinyInteger('last_activity_status')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(["customer_id", "group_id"]);
        });
    }
}
