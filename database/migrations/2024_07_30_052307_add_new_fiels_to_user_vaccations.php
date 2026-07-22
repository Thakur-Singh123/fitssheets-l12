<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFielsToUserVaccations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_vaccations', function (Blueprint $table) {
            $table->string('vacc_sl')->after('user_id')->nullable();
            $table->string('vacc_vc')->after('vacc_sl')->nullable();
            $table->string('vacc_be')->after('vacc_vc')->nullable();
            $table->string('vacc_jd')->after('vacc_be')->nullable();
            $table->string('vacc_frm')->after('vacc_jd')->nullable();
            $table->string('vacc_to')->after('vacc_frm')->nullable();
            $table->integer('vacc_aprby')->after('vacc_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vaccations', function (Blueprint $table) {
            //
        });
    }
}
