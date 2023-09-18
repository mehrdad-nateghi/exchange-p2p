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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('support_id')->unique();
            $table->tinyInteger('type');
            $table->decimal('trade_volume',13,2);
            $table->decimal('lower_bound_feasibility_threshold',13,2);
            $table->decimal('upper_bound_feasibility_threshold',13,2);
            $table->decimal('acceptance_threshold',13,2);
            $table->decimal('request_rate',13,2);
            $table->tinyInteger('status');
            $table->string('description')->nullable();
            $table->boolean('is_removed');
            $table->unsignedBigInteger('applicant_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->foreign('applicant_id')->references('id')->on('users')->onDelete('cascade');
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
