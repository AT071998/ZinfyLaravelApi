<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    public $table = 'students';
    protected $fillable = [
        'studentName',
        'college_id',
        'regId',
        'course_id',
        'phone',
        'email',
        'paid_fee',
        'batchYear',
        'due_fee',
        'Total_Fee',
        'final_status',
     ];

     public $timestamps = false;
}
