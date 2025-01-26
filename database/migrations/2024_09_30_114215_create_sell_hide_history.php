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
        Schema::create('sell_hide_history', function (Blueprint $table) {
            //
            $table->id();
            $table->string('sell_reference');
            $table->unsignedBigInteger('purchase_transaction_id');
            $table->integer('hidden_amount');
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
        Schema::dropIfExists('sell_hide_history');
    }
};
