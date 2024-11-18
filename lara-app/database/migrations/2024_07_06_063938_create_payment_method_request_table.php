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
        Schema::create('payment_method_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            // Add a unique constraint to prevent duplicate entries
            $table->unique(['request_id', 'payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_method_request');
    }
};
