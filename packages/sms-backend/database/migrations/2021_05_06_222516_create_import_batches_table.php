<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename');
            $table->string('summary')->nullable();
            $table->string('preview_summary')->nullable();
            $table->unsignedTinyInteger('status')->index();
            $table->unsignedTinyInteger('type')->index();
            $table->unsignedTinyInteger('mode')->nullable();
            $table->json('errors');
            $table->datetime('all_jobs_added_to_batch_at')->nullable();
            $table->uuid('job_batch_id')->nullable();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_batches');
    }
}
