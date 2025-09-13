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
        Schema::create('financials', function (Blueprint $table) {
            $table->id();
            $table->integer('system_fee_a');
            $table->integer('system_fee_b');
            $table->integer('system_fee_c');
            $table->integer('system_fee_d');
            $table->decimal('total_system_income',13,2);
            $table->integer('feasibility_band_percentage');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial');
    }
};
