<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOhlcValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ohlc_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('symbol_id')->unsigned()->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->double('open', 20, 4)->nullable();
            $table->double('high', 20, 4)->nullable();
            $table->double('low', 20, 4)->nullable();
            $table->double('close', 20, 4)->nullable();
            $table->double('volume', 20, 4)->nullable();
            $table->foreign('symbol_id')->references('id')->on('symbols')->onDelete('cascade');
            $table->unique(['symbol_id','timestamp']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ohlc_values');
    }
}
