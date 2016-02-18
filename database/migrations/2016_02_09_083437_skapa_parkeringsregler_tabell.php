<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaParkeringsreglerTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkeringsregler', function (Blueprint $table) {
            $table->increments('id');
            $table->string('start_tid');
            $table->string('stop_tid');
            $table->boolean('parkering_tillaten')->default(true);
            $table->integer('taxa');
            $table->text('beskrivning');
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
        Schema::drop('parkeringsregler');
    }
}
