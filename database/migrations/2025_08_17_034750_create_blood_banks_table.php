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
        Schema::create('blood_banks', function (Blueprint $table) {
            $table->id('STOCK_ID');
            $table->unsignedBigInteger('donor');
            $table->string('blood_type', 10)->nullable();
            $table->date('acquisition_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->tinyInteger('status')->default(0); // -1 = denied, 0 = pending, 1 = approved
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('donor')->references('USER_ID')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blood_banks');
    }
};
