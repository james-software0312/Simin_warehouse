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
        Schema::create('transaction_order', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('contactid')->nullable();
            $table->unsignedBigInteger('warehouseid')->nullable();
            $table->string('reference')->nullable();
            $table->string('show_reference')->nullable();
            $table->string('status'); //in or out
            $table->date('transactiondate');
            $table->string('description')->nullable();
            $table->boolean('signed')->default(false);
            $table->boolean('confirmed')->default(false);
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
        Schema::dropIfExists('transaction_order');
    }
};
