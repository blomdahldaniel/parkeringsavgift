<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkapaOrstaTimmenXKrTabell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forsta_timmen_x_kr', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taxa');
            $table->boolean('gratis_timme')->default(true);
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
        Schema::drop('forsta_timmen_x_kr');
    }
}
