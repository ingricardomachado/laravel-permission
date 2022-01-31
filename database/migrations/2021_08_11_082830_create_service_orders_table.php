<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('service_id')->unsigned()->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->integer('number')->nullable();
            $table->boolean('custom_folio')->default(false);            
            $table->string('folio',10)->nullable();
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->string('activities', 1000)->nullable();
            $table->string('recomendations', 1000)->nullable();
            $table->string('notes', 1000)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('contact_cell', 1000)->nullable();
            $table->string('contact_email', 1000)->nullable();
            $table->string('photos', 50)->nullable();
            $table->string('file',100)->nullable();
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
        Schema::dropIfExists('service_orders');
    }
}
