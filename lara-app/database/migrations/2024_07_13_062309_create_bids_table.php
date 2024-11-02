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
            $table->ulid('ulid')->unique()->index();
            $table->foreignId('user_id')->index()->constrained();
            $table->foreignId('request_id')->constrained();
            $table->foreignId('payment_method_id')->constrained();
            $table->string('number')->unique()->index();
            $table->decimal('price',13,2);
            $table->tinyInteger('status')->index();
            $table->dateTime('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bids');
    }
};
