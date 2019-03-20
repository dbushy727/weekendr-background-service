<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovedColumnToFlightDeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flight_deals', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->after('return_carrier')->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flight_deals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
