<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_id')->unsigned();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->char('type',1);
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->float('quantity', 11,2)->nullable();
            $table->float('unit_price', 11,2)->nullable();
            $table->float('sub_total', 11,2)->nullable();
            $table->float('percent_tax', 11,2)->nullable();
            $table->float('tax', 11,2)->nullable();
            $table->float('percent_discount', 11,2)->nullable();
            $table->float('discount', 11,2)->nullable();
            $table->float('total', 11,2)->nullable();
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
        Schema::dropIfExists('purchase_item');
    }
}
