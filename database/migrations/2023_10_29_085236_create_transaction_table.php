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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stockitemid')->nullable();
            $table->unsignedBigInteger('warehouseid')->nullable();
            $table->unsignedBigInteger('contactid')->nullable();
            $table->string('reference')->nullable();
            $table->string('status'); //in or out
            $table->date('transactiondate');
            $table->float('price', 8, 2)->default(0);
            $table->unsignedBigInteger('quantity');
            $table->integer('unitid');
            $table->string('description')->nullable();
            $table->integer('hidden_amount')->default(0);
            $table->integer('creator');
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
        Schema::dropIfExists('transaction');
    }
};
