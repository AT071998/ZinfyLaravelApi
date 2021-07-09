<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancepaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advancepayment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->Integer('amount');
            $table->date('paidDate');
            $table->Integer('year');
            $table->String('month');
            $table->Integer('pendingAmount');
            $table->foreign('employee_id')->references('id')->on('employees');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advancepayment');
    }
}
