<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVaccationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vaccations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('vacc_sl')->nullable();
            $table->string('vacc_vc')->nullable();
            $table->string('vacc_be')->nullable();
            $table->string('vacc_jd')->nullable();
            $table->string('vacc_frm')->nullable();
            $table->string('vacc_to')->nullable();
            $table->integer('vacc_aprby')->nullable();
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
        Schema::dropIfExists('user_vaccations');
    }
}
