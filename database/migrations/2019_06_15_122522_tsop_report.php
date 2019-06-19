<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TsopReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('tsop.tsop_report', function (Blueprint $table) {
    	    $table->collation  = 'latin1_swedish_ci'; 
  	    $table->charset = 'latin1';

            $table->increments('Id');

            $table->string('name', 300)->nullable();
            $table->string('help_url', 1000)->nullable();
            $table->tinyInteger('day_in_title')->nullable();
            $table->tinyInteger('ignore_replication')->default(0);
            $table->dateTime('date_created')->nullable();
            $table->dateTime('last_updated')->nullable();

	    $table->unique('name');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tsop.tsop_report');
    }
}
