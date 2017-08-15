<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->string('container');
            $table->integer('trans_id')->unique();
            $table->decimal('amount');
            $table->string('base_type');
            $table->string('cat_type');
            $table->integer('cat_id');
            $table->string('category');
            $table->string('simple_desc');
            $table->string('original_desc');
            $table->string('type');
            $table->string('sub_type');
            $table->string('trans_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transInfo');
    }
}
