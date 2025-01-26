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
        Schema::create('sell_order_detail', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('stockitemid')->nullable();
            $table->unsignedBigInteger('warehouseid')->nullable();
            $table->unsignedBigInteger('contactid')->nullable();
            $table->string('reference')->nullable();
            $table->date('selldate');
            $table->float('price', 8, 2)->default(0);
            $table->float('discount', 8, 2)->default(0); // for only sell
            $table->unsignedBigInteger('quantity');
            $table->integer('unitid');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('sell_order_detail');
    }
};
