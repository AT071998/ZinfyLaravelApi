<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\EmpAttendance;
use App\Models\Employee;
use Carbon\Carbon;

class EmpAttendanceController extends Controller
{
    //
    //insert the attendance. . 
    public function insertAttendance(Request $request){
            $empattendance = new EmpAttendance();
            $empattendance->date = date('Y-m-d'); //2021-04-14
            //return response()->json([ "date"=>$empattendance->date]);
            $check_attendance_if_already_taken = EmpAttendance::where('date',$empattendance->date)       
                                                                ->select('date')                                                       
                                                                ->get()
                                                                ->first();
          //  return $check_attendance_if_already_taken; //2021-04-14
            if($check_attendance_if_already_taken){
                return response()->json(['status'=>'already','message' => 'Attendance already taken'], 200);          
            }
            else{
                $employees = Employee::all();
    
                foreach ($employees as $employee) {
                            $empattendance = new EmpAttendance();
                            $empattendance->employee_id = $employee->id;
                            $empattendance->date=Carbon::today();
                            $empattendance->attendance_status=$request->attendance_status;
                            if($empattendance->save()){
                                return "Attendance recorded";
                            }else{
                                return "Something went wrong";
                            }
                }     
            }
            
        }


        //To display all the employees for attendance in flatlist. . . 
        public function DisplayEmployee(Request $request){
            $db = Employee::select('employees.employee_name','employees.id')
                            ->get();
            return $db;
        }


        //To display all the dates in order to change the attendance
        public function DisplayAllDates(Request $request){
            $db = EmpAttendance::select('date')->get();
            return $db;
        }
        //In order to edit the attendance by taking the date into consideration. . . 
        public function EditByDate(Request $request){
            $users = User::select('')
        }
}
