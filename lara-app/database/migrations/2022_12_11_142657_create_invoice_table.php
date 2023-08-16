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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('trade_stage');
            $table->decimal('trade_net_value',13,2);
            $table->decimal('trade_fee',13,2);
            $table->decimal('trade_gross_value',13,2);
            $table->tinyInteger('status');
            $table->string('payment_reason');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('trade_id');
            $table->unsignedBigInteger('target_account_id');
            $table->timestamp('created_at');
            $table->foreign('target_account_id')->references('id')->on('linked_methods')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice');
    }
};
