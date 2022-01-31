<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('number')->unsigned();
            $table->string('email')->nullable();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('full_name',200)->nullable();
            $table->string('bussines_name',150)->nullable();
            $table->string('rfc',150)->nullable();
            $table->string('tax_number',20)->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->bigInteger('timezone_id')->unsigned()->nullable();
            $table->foreign('timezone_id')->references('id')->on('timezones')->onDelete('cascade');            
            $table->string('city',100)->nullable();
            $table->string('address',200)->nullable();
            $table->string('zipcode',50)->nullable();
            $table->string('phone',10)->nullable();
            $table->string('cell',10)->nullable();
            $table->string('coin',10)->default('$');
            $table->char('money_format',3)->default('PC2');
            $table->string('logo',100)->nullable();
            $table->string('logo_name',100)->nullable();
            $table->string('logo_type',10)->nullable();
            $table->float('logo_size',11,2)->nullable();
            $table->boolean('full_registration')->default(false);
            $table->boolean('demo')->default(true);
            $table->integer('remaining_days')->default(0);
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
        Schema::dropIfExists('subscribers');
    }
}
