<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaParkeringarTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkeringar', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('start_tid');
            $table->integer('stop_tid')->nullable();

            $table->integer('kostnad')->default(0);
            $table->json('kostnad_data')->nullable();

            $table->integer('anvandare_id')->unsigned();
            $table->foreign('anvandare_id')->references('id')->on('anvandare');

            $table->integer('parkeringsomrade_id');
            $table->foreign('parkeringsomrade_id')->references('id')->on('parkeringsomraden');

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
        Schema::drop('parkeringar');
    }
}
