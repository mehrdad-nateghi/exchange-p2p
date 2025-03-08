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
        Schema::create('rial_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->index();
            $table->string('holder_name',50);
            $table->string('bank_name',50);
            $table->string('card_number', 50);
            $table->string('iban', 50)->nullable();
            $table->string('account_no', 50)->nullable();
            $table->string('bank_code', 6)->nullable(); // Bank identifier
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('rial_bank_accounts');
    }
};
