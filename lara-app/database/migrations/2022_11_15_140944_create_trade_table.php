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
        Schema::create('trade', function (Blueprint $table) {
            $table->id();
            $table->integer('currency_count');
            $table->integer('currency_amount');
            $table->integer('system_commission_amount');
            $table->string('request_type');
            $table->string('status');
            $table->timestamp('created_at');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('bid_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade');
    }
};
