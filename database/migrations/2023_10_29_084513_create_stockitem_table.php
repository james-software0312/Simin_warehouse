<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockitem', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('categoryid', 20)->nullable();
            $table->string('unitid', 20)->nullable();
            $table->string('warehouseid', 20)->nullable();
            $table->string('name');
            $table->unsignedBigInteger('quantity');
            $table->integer('single_quantity')->default(0);            
            $table->string('photo')->nullable();
            $table->string('itemsubtype');
            $table->string('size');
            $table->string('color');
            $table->float('price', 8, 2);
            $table->float('purchase_price', 8, 2)->nullable();
            $table->unsignedBigInteger('contactid')->nullable();
            $table->float('vat', 8, 2);
            $table->integer('unitconverter')->nullable();
            $table->integer('unitconverter1')->nullable();
            $table->integer('unitconverterto')->nullable();
            $table->integer('hidden_amount')->default(0);
            $table->string('description')->nullable();
            $table->tinyInteger('is_visible')->default(0);
            $table->tinyInteger('is_delete')->default(0);
            $table->unsignedBigInteger('product_id');
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
        Schema::dropIfExists('stockitem');
    }
};
