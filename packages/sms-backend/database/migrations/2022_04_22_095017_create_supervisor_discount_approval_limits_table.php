<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorDiscountApprovalLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervisor_discount_approval_limits', function (Blueprint $table) {
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            $table->foreignId('supervisor_type_id')->constrained('supervisor_types');
            $table->tinyInteger('limit')->default(0);
            // $table->foreignId('product_brand_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            // $table->tinyInteger('limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supervisor_discount_approval_limits');
    }
}
