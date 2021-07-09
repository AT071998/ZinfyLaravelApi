<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
     protected $table = "advancepayment";
    protected $fillable = ['employee_id','amount','paidDate','year','month','pendingAmount','days','status'];
    public $timestamps = false;
}
