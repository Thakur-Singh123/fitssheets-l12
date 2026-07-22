<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVaccatioStatusnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vaccatio_statusns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('vacc_start')->nullable();
            $table->string('vacc_end')->nullable();
            $table->string('vacc_comments')->nullable();
            $table->string('vacc_top')->nullable();
            $table->string('vacc_rbu')->nullable();
            $table->integer('vacc_status')->nullable();
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
        Schema::dropIfExists('user_vaccatio_statusns');
    }
}
