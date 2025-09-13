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
        Schema::create('trade_steps', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();
            $table->foreignId('trade_id')->constrained();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('priority')->nullable();
            $table->tinyInteger('owner')->index()->nullable(); // buyer or seller
            $table->tinyInteger('status')->index(); // todo, doing, done
            $table->integer('duration_minutes')->nullable(); // Duration in minutes for the step
            $table->timestamp('expire_at')->nullable();
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('trade_steps');
    }
};
