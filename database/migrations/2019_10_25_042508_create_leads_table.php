<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leads', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name');
            $table->float('price')->default(0);
            $table->integer('stage')->default(0);
            $table->integer('owner')->default(0);
            $table->integer('client')->default(0);
            $table->integer('source')->default(0);
            $table->integer('created_by')->default(0);
            $table->text('notes');
            $table->smallInteger('item_order')->default(0);
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
        Schema::dropIfExists('leads');
    }
}
