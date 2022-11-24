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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('applicant_id');
            $table->integer('currency_count');
            $table->integer('lower_bound_feasibility');
            $table->integer('upper_bound_feasibility');
            $table->integer('acceptance_threshold');
            $table->string('status');
            $table->boolean('is_removed');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
};
