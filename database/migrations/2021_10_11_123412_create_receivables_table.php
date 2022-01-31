<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');            
            $table->date('date')->nullable();
            $table->string('folio',10)->nullable();
            $table->float('amount',11,2)->nullable();
            $table->float('balance',11,2)->nullable();
            $table->Integer('way_pay')->unsigned()->nullable();
            $table->Integer('method_pay')->unsigned()->nullable();
            $table->Integer('condition_pay')->unsigned()->nullable();
            $table->Integer('days')->nullable();
            $table->string('description',1000)->nullable();
            $table->date('close_date')->nullable();
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
        Schema::dropIfExists('receivables');
    }
}
