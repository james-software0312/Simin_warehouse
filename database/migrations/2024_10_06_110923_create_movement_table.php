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
        Schema::create('movement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stockitemid')->nullable();
            $table->string('code');
            $table->unsignedBigInteger('source_warehouse_id');
            $table->unsignedBigInteger('target_warehouse_id');
            $table->string('reference')->nullable();
            $table->string('status'); //in or out
            $table->date('movement_date');
            $table->float('price', 8, 2)->default(0);
            $table->unsignedBigInteger('quantity');
            $table->integer('unitid');
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
        Schema::dropIfExists('movements');
    }
};
