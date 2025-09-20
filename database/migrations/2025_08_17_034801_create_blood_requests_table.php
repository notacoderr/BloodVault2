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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('blood_type', 5);
            $table->integer('units_needed');
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('reason')->nullable();
            $table->string('hospital', 255)->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->date('request_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->boolean('blood_available')->default(false);
            $table->integer('allocated_units')->nullable()->default(0);
            $table->text('additional_notes')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('USER_ID')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('blood_type');
            $table->index('status');
            $table->index('urgency');
            $table->index('request_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blood_requests');
    }
};
