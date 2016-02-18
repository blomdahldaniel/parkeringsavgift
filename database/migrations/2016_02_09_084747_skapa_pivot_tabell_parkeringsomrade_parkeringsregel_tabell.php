<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaPivotTabellParkeringsomradeParkeringsregelTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkeringsomrade_parkeringsregel', function (Blueprint $table) {
            $table->integer('parkeringsomrade_id')->unsigned();
            $table->integer('parkeringsregel_id')->unsigned();
            $table->primary(['parkeringsomrade_id', 'parkeringsregel_id']);
            $table->timestamps();

            $table->foreign('parkeringsomrade_id')
                ->references('id')
                ->on('parkeringsomraden')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('parkeringsregel_id')
                ->references('id')
                ->on('parkeringsregler')
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
        Schema::drop('parkeringsomrade_parkeringsregel');
    }
}
