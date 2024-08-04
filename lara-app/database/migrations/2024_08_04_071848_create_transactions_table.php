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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();
            $table->morphs('transactionable');
            $table->foreignId('user_id')->constrained();
            $table->decimal('amount', 13, 2);
            $table->string('currency', 3);
            $table->tinyInteger('status')->index(); // pending, completed, failed
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
        Schema::dropIfExists('transactions');
    }
};
