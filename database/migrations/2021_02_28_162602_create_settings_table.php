<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company', 100);
            $table->string('NIT', 20);
            $table->string('address', 500);
            $table->string('phone', 25);
            $table->string('email', 50);
            $table->string('coin',10);
            $table->char('money_format',3)->default('PC2');
            $table->integer('demo_days')->default(15);
            $table->string('logo',100)->nullable();
            $table->string('logo_name',100)->nullable();
            $table->string('logo_type',10)->nullable();
            $table->float('logo_size',11,2)->nullable();
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
        Schema::dropIfExists('settings');
    }
}
