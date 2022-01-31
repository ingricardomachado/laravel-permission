<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id')->unsigned()->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('number')->unsigned()->nullable();
            $table->string('code',20)->nullable();
            $table->string('code_fe',20)->nullable();
            $table->string('name',100)->nullable();
            $table->string('description',200)->nullable();
            $table->float('initial_stock',11,2)->nullable();
            $table->float('inputs',11,2)->nullable();
            $table->float('outputs',11,2)->nullable();
            $table->float('stock',11,2)->unsigned()->nullable();
            $table->float('reorder_point',11,2)->unsigned()->nullable();
            $table->float('safety_stock',11,2)->unsigned()->nullable();
            $table->float('cost',11,2)->unsigned()->nullable();
            $table->float('price',11,2)->unsigned()->nullable();
            $table->string('field1',200)->nullable();
            $table->string('photo_name',255)->nullable();
            $table->string('photo_type',10)->nullable();
            $table->Integer('photo_size')->unsigned()->nullable();
            $table->string('photo',255)->nullable();
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
        Schema::dropIfExists('products');
    }
}
