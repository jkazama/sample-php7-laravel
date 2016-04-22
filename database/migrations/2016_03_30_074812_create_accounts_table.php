<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('mail')->nullable();
            $table->string('statusType');
        });

        Schema::create('fi_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accountId');
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
        Schema::drop('accounts');
        Schema::drop('fi_accounts');
    }
}
