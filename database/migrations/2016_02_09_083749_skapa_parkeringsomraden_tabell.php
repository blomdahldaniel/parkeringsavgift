<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaParkeringsomradenTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkeringsomraden', function (Blueprint $table) {
            $table->increments('id');
            $table->string('namn');
            $table->string('kod_omrade');
            $table->integer('max_kostnad_per_dygn')->nullable();
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
        Schema::drop('parkeringsomraden');
    }
}
