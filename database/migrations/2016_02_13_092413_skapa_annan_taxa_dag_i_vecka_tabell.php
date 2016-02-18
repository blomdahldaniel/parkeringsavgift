<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaAnnanTaxaDagIVeckaTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annan_taxa_dag_i_vecka', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taxa');
            $table->integer('max_kostnad_per_dygn')->nullable();
            $table->boolean('gratis_timme')->default(true);
            $table->text('beskrivning');
            $table->json('veckodagar');
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
        Schema::drop('annan_taxa_dag_i_vecka');
    }
}
