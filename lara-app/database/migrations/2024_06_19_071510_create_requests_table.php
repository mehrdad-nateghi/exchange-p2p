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
            $table->ulid('ulid')->unique()->index();
            $table->foreignId('user_id')->index()->constrained();
            $table->string('number')->unique()->index();
            $table->decimal('volume',13,2);
            $table->decimal('price',13,2);
            $table->decimal('min_allowed_price',13,2); // todo-mn: need to save it or just for validation?
            $table->decimal('max_allowed_price',13,2);
            //$table->text('description')->nullable();
           /* $table->text('deposit_reason')->nullable();
            $table->boolean('deposit_reason_accepted')->default(true);*/
            $table->tinyInteger('type')->index();
            $table->tinyInteger('status')->index();
            $table->timestamp('canceled_at')->nullable();
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
        Schema::dropIfExists('requests');
    }
};
