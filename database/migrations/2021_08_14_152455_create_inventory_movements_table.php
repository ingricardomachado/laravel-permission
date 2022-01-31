<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->date('date')->nullable();
            $table->char('type',1)->nullable();
            $table->bigInteger('input_type_id')->unsigned()->nullable();
            $table->foreign('input_type_id')->references('id')->on('input_types')->onDelete('cascade');
            $table->bigInteger('output_type_id')->unsigned()->nullable();
            $table->foreign('output_type_id')->references('id')->on('output_types')->onDelete('cascade');
            $table->float('quantity',11,2)->nullable();
            $table->float('unit_price',11,2)->nullable();
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
        Schema::dropIfExists('inventory_movements');
    }
}
