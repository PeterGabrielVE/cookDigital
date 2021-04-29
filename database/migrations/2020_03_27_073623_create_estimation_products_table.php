<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'estimation_products', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estimation_id');
            $table->string('name');
            $table->float('price');
            $table->integer('quantity');
            $table->text('description');
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimation_products');
    }
}
