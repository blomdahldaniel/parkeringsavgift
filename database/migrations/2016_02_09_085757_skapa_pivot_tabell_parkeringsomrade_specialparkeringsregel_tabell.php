<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaPivotTabellParkeringsomradeSpecialparkeringsregelTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkeringsomrade_specialparkeringsregel', function (Blueprint $table) {
            $table->integer('parkeringsomrade_id')->unsigned();
            $table->integer('specialparkeringsregel_id')->unsigned();
            $table->primary(['parkeringsomrade_id', 'specialparkeringsregel_id']);
            $table->timestamps();

            $table->foreign('parkeringsomrade_id')
                ->references('id')
                ->on('parkeringsomraden')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('specialparkeringsregel_id')
                ->references('id')
                ->on('specialparkeringsregler')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parkeringsomrade_specialparkeringsregel');
    }
}
