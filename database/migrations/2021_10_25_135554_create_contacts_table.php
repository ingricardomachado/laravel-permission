<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->string('code',20)->nullable();
            $table->integer('number')->unsigned();
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('full_name',100)->nullable();
            $table->string('type',50)->nullable();
            $table->string('profession',150)->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->string('street',100)->nullable();
            $table->string('street_number',100)->nullable();
            $table->string('neighborhood',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('zipcode',10)->nullable();            
            $table->string('phone',11)->nullable();
            $table->string('cell',11)->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
