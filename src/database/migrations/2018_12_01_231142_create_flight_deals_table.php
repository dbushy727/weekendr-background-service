<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('price');

            $table->string('departure_origin');
            $table->string('departure_destination');
            $table->string('departure_carrier');
            $table->datetime('departure_date');

            $table->string('return_origin');
            $table->string('return_destination');
            $table->string('return_carrier');
            $table->datetime('return_date');

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
        Schema::dropIfExists('flight_deals');
    }
}
