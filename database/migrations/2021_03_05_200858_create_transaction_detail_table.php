<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->increments('transaction_detail_id');
            $table->integer('transaction_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('sell_price')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->tinyInteger('discount')->default(0);
            $table->integer('price')->unsigned();
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_detail');
    }
}
