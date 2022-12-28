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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount',13,2);
            $table->string('description');
            $table->string('status');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('transaction_method_id');
            $table->timestamp('created_at');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('transaction_method_id')->references('id')->on('transaction_methods')->onDelete('cascade');
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
};
