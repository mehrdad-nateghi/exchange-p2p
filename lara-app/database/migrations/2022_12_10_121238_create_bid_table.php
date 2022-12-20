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
        Schema::create('bid', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('bid_rate');
            $table->boolean('status');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('applicant_id');
            $table->timestamp('created_at');
            $table->foreign('applicant_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('request')->onDelete('cascade');


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
