<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('prospect',100)->nullable();
            $table->char('type',1)->nullable();
            $table->string('contact',100)->nullable();
            $table->integer('order_number')->nullable();
            $table->boolean('custom_order_folio')->default(false);
            $table->string('order_folio',10)->nullable();
            $table->integer('purchase_number')->nullable();
            $table->boolean('custom_purchase_folio')->default(false);
            $table->string('purchase_folio',10)->nullable();
            $table->string('coin',5)->nullable();
            $table->boolean('coin_type')->unsigned()->default(false);
            $table->string('coin_type_description',20)->nullable();
            $table->datetime('date')->nullable();
            $table->datetime('due_date')->nullable();
            $table->string('observations',2000)->nullable();
            $table->string('conditions',2000)->nullable();
            $table->float('sub_total',11,2)->nullable();
            $table->float('total_discount',11,2)->nullable();
            $table->float('total_tax',11,2)->nullable();
            $table->float('total',11,2)->nullable();
            $table->string('created_by',100)->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
