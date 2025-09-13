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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();
            $table->string('number')->unique()->index();
            $table->foreignId('user_id')->constrained();
            $table->morphs('invoiceable');
            $table->decimal('amount', 13, 2);
            $table->decimal('fee', 13, 2)->default(0);
            $table->decimal('fee_foreign', 13, 2)->default(0);  // Fee in foreign currency
            $table->string('fee_foreign_currency_code', 3)->default('EUR');  // Currency code (EUR, USD, etc)
            $table->tinyInteger('type');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('invoices');
    }
};
