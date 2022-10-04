<?php

use App\Models\ProductList;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToProductListsTable extends Migration
{
    public function up()
    {
        Schema::table('product_lists', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->foreignId('company_id')->nullable()->after('product_ids')->constrained();
        });
    }

    public function down()
    {
        Schema::table('product_lists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['name']);
        });
    }
}