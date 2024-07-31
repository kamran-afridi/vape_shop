<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->id(); 
            $table->integer('quantity');  
            $table->bigInteger('product_id');
            $table->bigInteger('order_id');
            $table->bigInteger('category_id');
            $table->bigInteger('user_id');
            $table->timestamps(); 

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->foreign('user_id')->nulleable()->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_product');
    }
}
