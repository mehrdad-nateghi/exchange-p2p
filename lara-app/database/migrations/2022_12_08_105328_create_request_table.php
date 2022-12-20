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
        Schema::create('request', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('trade_volume',13,2);
            $table->decimal('lower_bound_feasibility',13,2);
            $table->decimal('upper_bound_feasibility',13,2);
            $table->decimal('acceptance_threshold',13,2);
            $table->string('status');
            $table->boolean('is_removed');
            $table->unsignedBigInteger('applicant_id');
            $table->timestamp('created_at');
            $table->foreign('applicant_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request');
    }
};
