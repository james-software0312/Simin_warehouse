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
        Schema::create('movement_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_warehouse_id');
            $table->unsignedBigInteger('target_warehouse_id');
            $table->string('reference')->nullable();
            $table->string('status'); 
            $table->date('movement_date');
            $table->float('total_price', 8, 2)->default(0);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('movement_order_models');
    }
};
