<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'projects', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->float('price')->default('0.00');
            $table->date('start_date');
            $table->date('due_date');
            $table->integer('client')->default('0');
            $table->text('description');
            $table->integer('label')->default('0');
            $table->integer('lead')->default('0');
            $table->string('status', 25)->default('on_going');
            $table->integer('is_active')->default('1');
            $table->integer('created_by')->default('0');
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
        Schema::dropIfExists('projects');
    }
}
