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
        Schema::create('contact', function (Blueprint $table) {
           
                $table->id();
                $table->string('name');
                $table->string('surname')->nullable();
                $table->string('company');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->string('email')->unique();
                $table->string('phone');
                $table->string('vat_number')->nullable();
                $table->string('whatsapp')->nullable();
                $table->text('description')->nullable();
                $table->string('status'); //supplier or customer;
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
        Schema::dropIfExists('contact');
    }
};
