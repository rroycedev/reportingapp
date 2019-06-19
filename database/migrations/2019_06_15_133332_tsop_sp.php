<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TsopSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsop.tsop_sp', function (Blueprint $table) {
            $table->increments('spId');

            $table->string('spname', 50);
            $table->string('from_parameter_name', 20)->nullable();
            $table->integer('from_parameter_value')->nullable();
            $table->string('to_parameter_name', 20)->nullable();
            $table->integer('to_parameter_value')->nullable();

	    $table->foreign('from_parameter_value')->references('value')->on('tsop.Id');
	    $table->foreign('to_parameter_value')->references('value')->on('tsop.Id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsop.tsop_sp');
    }
}
