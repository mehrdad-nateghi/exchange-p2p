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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->integer('bid_rate');
            $table->tinyInteger('status');
            $table->string('description');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('target_account_id');
            $table->timestamp('created_at');
            $table->foreign('applicant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('target_account_id')->references('id')->on('linked_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bid');
    }
};
