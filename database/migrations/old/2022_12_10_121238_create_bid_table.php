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
            $table->string('support_id')->unique();
            $table->tinyInteger('type');
            $table->decimal('bid_rate',13,2);
            $table->tinyInteger('status');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('target_account_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
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
