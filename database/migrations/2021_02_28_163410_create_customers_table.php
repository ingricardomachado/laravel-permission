<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('type',1)->default('P');
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('natural')->default(false);
            $table->string('name',150)->nullable();
            $table->string('target',150)->nullable();
            $table->string('rfc',15)->nullable();
            $table->string('tax_number',20)->nullable();
            $table->string('bussines_name',150)->nullable();
            $table->integer('number')->unsigned();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->string('street',100)->nullable();
            $table->string('street_number',100)->nullable();
            $table->string('neighborhood',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('address',200)->nullable();
            $table->string('zipcode',10)->nullable();
            $table->string('urls',1000)->nullable();
            $table->string('bussines_address',200)->nullable();
            $table->string('shipping_street',200)->nullable();
            $table->string('shipping_number',200)->nullable();
            $table->string('shipping_neighborhood',100)->nullable();
            $table->string('shipping_zipcode',10)->nullable();
            $table->string('shipping_city',100)->nullable();
            $table->bigInteger('shipping_country_id')->unsigned()->nullable();
            $table->foreign('shipping_country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('shipping_state_id')->unsigned()->nullable();
            $table->foreign('shipping_state_id')->references('id')->on('states')->onDelete('cascade');
            $table->Integer('discount')->unsigned()->nullable();
            $table->string('notes',1000)->nullable();
            $table->string('phone',11)->nullable();
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
        Schema::dropIfExists('customers');
    }
}
