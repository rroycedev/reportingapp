<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TsopParameterValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsop.tsop_parameter_value', function (Blueprint $table) {
            $table->bigIncrements('Id');
            $table->string('value', 30)->nullable();
            $table->string('description', 300)->nullable();

            $table->index('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsop.tsop_parameter_value');
    }
}
