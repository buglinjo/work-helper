<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_config', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('start_date');
            $table->string('timezone');
            $table->time('work_day_starts');
            $table->time('work_day_ends');
            $table->time('lunch_break_starts');
            $table->time('lunch_break_ends');
            $table->integer('num_of_workdays');
            $table->integer('pay_frequency_id')->unsigned();
            $table->foreign('pay_frequency_id')->references('id')->on('pay_frequencies')->onDelete('cascade');
            $table->float('hourly_wage');
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
        Schema::drop('work_config');
    }
}
