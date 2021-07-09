<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
             $table->id();
            //Personal details. . . 
            $table->string('studentName');
            $table->Integer('college_id')->unsigned();
            $table->string('regId');
            $table->Integer('course_id')->unsigned();
            $table->string('phone',11);
            $table->string('email')->unique();
            //Training details. . .
            $table->Integer('batchYear');
         
            $table->bigInteger('paid_fee');
            $table->bigInteger('due_fee')->nullable();
            $table->bigInteger('Total_Fee');
            //Final Declaration. . . . 
            $table->Integer('final_status')->nullable();
            //Foreign key. . .
            
        });
        Schema::table('students', function($table) {
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('college_id')->references('id')->on('college');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
