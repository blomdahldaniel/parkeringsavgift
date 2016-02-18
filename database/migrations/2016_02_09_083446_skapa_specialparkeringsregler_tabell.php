<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaSpecialparkeringsreglerTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialparkeringsregler', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('specialregel_id');
            $table->string('specialregel_type');
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
        Schema::drop('specialparkeringsregler');
    }
}
