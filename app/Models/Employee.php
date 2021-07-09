<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class Employee extends Model
{
    protected $fillable = [
       'employee_name',
       'designation',
       'email',
       'phone',
       'password',
       'salary',
       'employee_uuid'
    ];
    use HasFactory, Notifiable,HasApiTokens;
}
