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
        Schema::create('users', function (Blueprint $table) {
            $table->id('USER_ID');
            $table->string('email', 30)->unique();
            $table->string('password', 255); // Increased length for Laravel's hashed passwords
            $table->string('name', 50)->nullable();
            $table->string('dob', 10)->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('province', 50)->nullable();
            $table->string('contact', 11)->nullable();
            $table->string('bloodtype', 4)->nullable();
            $table->string('usertype', 30)->nullable();
            $table->string('schedule_date', 10)->nullable();
            $table->string('last_donation_date', 10)->nullable();
            $table->rememberToken(); // For Laravel authentication
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
        Schema::dropIfExists('users');
    }
};
