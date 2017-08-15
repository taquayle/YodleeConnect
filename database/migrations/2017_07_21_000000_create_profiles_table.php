<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->timestamps();
            $table->string('key1');
            $table->string('key2');
            $table->string('key3');
            $table->string('sector1');
            $table->string('sec1_comp1');
            $table->string('sec1_comp2');
            $table->string('sec1_comp3');
            $table->string('sector2');
            $table->string('sec2_comp1');
            $table->string('sec2_comp2');
            $table->string('sec2_comp3');
            $table->string('sector3');
            $table->string('sec3_comp1');
            $table->string('sec3_comp2');
            $table->string('sec3_comp3');
            $table->string ('algo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('userprofile');
    }
}
