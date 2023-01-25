<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name');
            $table->enum('rate_type', ['time', 'distance', 'max']);
            $table->integer('start_hour')->nullable();
            $table->integer('end_hour')->nullable();
            $table->integer('default_rate');
            $table->integer('special_rate')->nullable();
            $table->integer('special_rate_limit')->nullable();
            $table->boolean('is_weekend')->default(false);
            $table->boolean('daily_max')->default(false);
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
        Schema::dropIfExists('rates');
    }
}
