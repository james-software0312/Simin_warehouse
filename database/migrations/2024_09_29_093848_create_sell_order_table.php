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
        Schema::create('sell_order', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('warehouseid')->nullable();
            $table->unsignedBigInteger('contactid')->nullable();
            $table->string('reference');
            $table->string('show_reference');
            $table->boolean('pre_order');
            $table->date('selldate');
            $table->float('discount', 8, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->string('description')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('withinvoice')->default(false);
            $table->boolean('hidden')->default(false);
            $table->boolean('signed')->default(false);
            $table->integer('creator');
            $table->string('payment_type')->default("bank_transfer");
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
        Schema::dropIfExists('sell_order');
    }
};
