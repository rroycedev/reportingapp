<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TsopReportFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsop.tsop_report_files', function (Blueprint $table) {
            $table->collation  = 'latin1_swedish_ci'; 
  	    $table->charset = 'latin1';

            $table->increments('Id');

            $table->integer('report_Id')->unsigned();
            $table->integer('execution_type')->unsigned()->default(41);
            $table->integer('sequence')->unsigned()->default(1);
            $table->tinyInteger('async')->unsigned()->default(0);
            $table->string('file_name', 300)->nullable();
            $table->string('file_name_mask', 300)->nullable();
            $table->string('url', 1500)->nullable();
            $table->tinyInteger('sp_needed')->nullable();
            $table->integer('spid')->unsigned()->nullable();
            $table->integer('file_type')->unsigned()->nullable();
            $table->tinyInteger('embed_attachment')->nullable();
            $table->string('template_file', 200)->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('send_blank')->default(0);
            $table->string('script_path', 500)->nullable();

	    $table->foreign('report_Id')->references('Id')->on('tsop.tsop_report');
	    $table->foreign('file_type')->references('Id')->on('tsop.tsop_file_type');
	    $table->foreign('spid')->references('spId')->on('tsop.tsop_sp');

        });

	DB::statement("ALTER TABLE tsop.tsop_report_files ADD template_data LONGBLOB AFTER template_file");        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsop.tsop_report_files');
    }
}


