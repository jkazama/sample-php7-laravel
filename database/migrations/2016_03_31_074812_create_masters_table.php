<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_fi_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->string('currency');
            $table->string('fiCode');
            $table->string('fiAccountId');
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
        Schema::drop('self_fi_accounts');
    }
}
