<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePriceAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //estimation_products tbl
        Schema::table(
            'estimation_products', function (Blueprint $table){
            $table->float('price', 25, 2)->change();
        }
        );

        // invoice_products
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->change();
        }
        );

        // leads
        Schema::table(
            'leads', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0')->change();
        }
        );

        // products
        Schema::table(
            'products', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->change();
        }
        );

        // projects
        Schema::table(
            'projects', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->change();
        }
        );

        // expenses
        Schema::table(
            'expenses', function (Blueprint $table){
            $table->float('amount', 25, 2)->nullable()->change();
        }
        );

        // invoice_payments
        Schema::table(
            'invoice_payments', function (Blueprint $table){
            $table->float('amount', 25, 2)->change();
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
        //estimation_products tbl
        Schema::table(
            'estimation_products', function (Blueprint $table){
            $table->float('price')->change();
        }
        );

        // invoice_products
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->change();
        }
        );

        // leads
        Schema::table(
            'leads', function (Blueprint $table){
            $table->float('price')->default('0')->change();
        }
        );

        // products
        Schema::table(
            'products', function (Blueprint $table){
            $table->float('price')->default('0.00')->change();
        }
        );

        // projects
        Schema::table(
            'projects', function (Blueprint $table){
            $table->float('price', 15, 2)->default('0.00')->change();
        }
        );

        // expenses
        Schema::table(
            'expenses', function (Blueprint $table){
            $table->float('amount')->nullable()->change();
        }
        );

        // invoice_payments
        Schema::table(
            'invoice_payments', function (Blueprint $table){
            $table->float('amount', 15, 2)->change();
        }
        );
    }
}
