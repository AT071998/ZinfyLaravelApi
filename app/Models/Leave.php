<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table="leaves";
    protected $fillable = [
        'employee_id',
        'leave_type',
        'date_from',
        'date_to',
        'reason'
    ];
}
