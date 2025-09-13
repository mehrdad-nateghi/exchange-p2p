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
        Schema::create('foreign_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->index();
            $table->string('holder_name',50);
            $table->string('bank_name',50);
            $table->string('iban', 50)->nullable(); // IBAN (International Bank Account Number)
            $table->string('bic', 50)->nullable(); // BIC (Bank Identifier Code)
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
        Schema::dropIfExists('foreign_bank_accounts');
    }
};
