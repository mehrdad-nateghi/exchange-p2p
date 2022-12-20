<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_constraint', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_rial_time_constraint');
            $table->integer('payment_currency_time_constraint');
            $table->integer('confirmation_receipt_time_constraint');
            $table->integer('system_payment_time_constraint');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_constraint');
    }
};
