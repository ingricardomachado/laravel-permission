<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('pin',15)->nullable();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('full_name',200)->nullable();
            $table->integer('number')->unsigned()->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->string('city',100)->nullable();
            $table->string('address',200)->nullable();
            $table->string('phone',10)->nullable();
            $table->string('cell',10)->nullable();
            $table->string('email',50)->nullable();
            $table->string('field1',200)->nullable();
            $table->string('avatar',100)->nullable();
            $table->string('avatar_name',100)->nullable();
            $table->string('avatar_type',10)->nullable();
            $table->float('avatar_size',11,2)->nullable();
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
        Schema::dropIfExists('employees');
    }
}
