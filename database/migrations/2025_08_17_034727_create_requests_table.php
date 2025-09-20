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
            $table->id('RESERVATION_ID');
            $table->string('request_date', 6)->nullable();
            $table->string('required_blood', 6)->nullable();
            $table->integer('quantity')->nullable();
            $table->binary('proof')->nullable();
            $table->tinyInteger('status')->default(0); // -1 = denied, 0 = pending, 1 = approved
            $table->timestamps();
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
