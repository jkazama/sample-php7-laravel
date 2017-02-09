<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accountId');
            $table->date('baseDay');
            $table->string('currency');
            $table->float('amount');
            $table->datetime('updateDate');
        });

        Schema::create('cashflows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accountId');
            $table->string('currency');
            $table->float('amount');
            $table->string('cashflowType');
            $table->string('remark');
            $table->date('eventDay');
            $table->datetime('eventDate');
            $table->date('valueDay')->nullable();
            $table->string('statusType');
            $table->datetime('createDate');
            $table->string('createId');
            $table->datetime('updateDate');
            $table->string('updateId');
        });

        Schema::create('cash_in_outs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accountId');
            $table->string('currency');
            $table->float('absAmount');
            $table->boolean('withdrawal');
            $table->date('requestDay');
            $table->datetime('requestDate');
            $table->date('eventDay');
            $table->date('valueDay')->nullable();
            $table->string('targetFiCode');
            $table->string('targetFiAccountId');
            $table->string('selfFiCode');
            $table->string('selfFiAccountId');
            $table->bigInteger('cashflowId')->nullable();
            $table->string('statusType');
            $table->datetime('createDate');
            $table->string('createId');
            $table->datetime('updateDate');
            $table->string('updateId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cash_balances');
        Schema::drop('cashflows');
        Schema::drop('cash_in_outs');
    }
}
