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
        Schema::create('linkedmethod_methodattribute', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('linked_method_id');
            $table->unsignedBigInteger('method_attribute_id');
            $table->foreign('method_attribute_id')->references('id')->on('method_attribute')->onDelete('cascade');
            $table->foreign('linked_method_id')->references('id')->on('linked_method')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linkedmethod_methodattribute');
    }
};
