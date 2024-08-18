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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->integer('guest_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('total_item_quantity')->unsigned();
            $table->integer('total_price')->unsigned();
            $table->integer('final_price')->default(0);
            $table->integer('money_received')->default(0);
            $table->boolean('is_temp')->default(false);
            $table->timestamps();

            // Add indexes
            $table->index('guest_id');
            $table->index('user_id');
            $table->index('is_temp');
            $table->index('created_at');
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
