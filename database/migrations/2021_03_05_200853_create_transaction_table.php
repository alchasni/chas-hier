<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->integer('guest_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('total_item_quantity')->unsigned();
            $table->integer('total_price')->unsigned();
            $table->tinyInteger('discount')->default(0);
            $table->integer('final_price')->default(0);
            $table->integer('money_received')->default(0);
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
        Schema::dropIfExists('transaction');
    }
}
