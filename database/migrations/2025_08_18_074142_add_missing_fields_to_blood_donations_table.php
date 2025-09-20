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
        Schema::table('blood_donations', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('notes');
            $table->integer('quantity')->default(1)->after('donation_date');
            $table->string('screening_status', 50)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blood_donations', function (Blueprint $table) {
            $table->dropColumn(['admin_notes', 'quantity', 'screening_status']);
        });
    }
};
