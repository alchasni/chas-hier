<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest', function (Blueprint $table) {
            $table->increments('guest_id');
            $table->string('name');
            $table->string('member_code');
            $table->text('address')->nullable();
            $table->string('phone_number');
            $table->timestamps();
        });

        Schema::create('loan', function (Blueprint $table) {
            $table->increments('loan_id');
            $table->integer('amount');
            $table->unsignedInteger('guest_idz');
            $table->foreign('guest_idz')
                ->references('guest_id')
                ->on('guest')
                ->restrictOnUpdate()
                ->restrictOnDelete();
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
        Schema::dropIfExists('guest');
    }
}
