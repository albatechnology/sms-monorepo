<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportLinesTable extends Migration
{
    public function up()
    {
        Schema::create('import_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status')->index();
            $table->unsignedTinyInteger('preview_status')->index();
            $table->unsignedBigInteger('row')->index();
            $table->json('errors');
            $table->json('data');
            $table->text('exception_message')->nullable();
            $table->foreignId('import_batch_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_lines');
    }
}
