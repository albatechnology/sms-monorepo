<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_categories', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('company_id')->constrained();
            $table->string('name')->nullable();
            $table->longText('description');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('promos', function (Blueprint $table) {
            $table->integer('promo_category_id')->nullable()->after('id');
            $table->index('promo_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_categories');
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['promo_category_id']);
        });
    }
}
