<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->string('name',150)->nullable();
            $table->string('target',150)->nullable();
            $table->string('rfc',15)->nullable();
            $table->string('bussines_name',150)->nullable();
            $table->integer('number')->unsigned();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->string('city',100)->nullable();
            $table->string('address',200)->nullable();
            $table->string('location',200)->nullable();
            $table->string('zipcode',10)->nullable();            
            $table->string('contact',150)->nullable();
            $table->string('phone',10)->nullable();
            $table->string('cell',10)->nullable();
            $table->string('email',50)->nullable();
            $table->boolean('active')->default(true);            
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
        Schema::dropIfExists('suppliers');
    }
}
