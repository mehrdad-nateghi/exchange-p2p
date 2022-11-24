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
        Schema::create('financial', function (Blueprint $table) {
            $table->id();
            $table->integer('system_commission_rate_CA');
            $table->integer('system_commission_rate_CB');
            $table->integer('system_commission_rate_CC');
            $table->integer('system_commission_rate_CD');
            $table->integer('total_system_income');
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
