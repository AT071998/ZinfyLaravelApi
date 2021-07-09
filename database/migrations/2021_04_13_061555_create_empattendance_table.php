<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpattendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empattendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('attendance_status');
            $table->date('date');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('empattendance');
    }
}
