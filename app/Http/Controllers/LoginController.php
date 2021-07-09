<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Attendance;
use DateTime;
use App\Models\Role;
use App\Models\Salary;
use DB;


class LoginController extends Controller
{
  

    public function Login(Request $request,$id){

        $var  = Carbon::now('Asia/Kolkata');
        $date = Carbon::now();
        $format = $date->toDateString();
        $logged_in = 1;

        $email = $request->get('email');
        $password = $request->get('password');
       // return $password;
       // $bcrypted = bcrypt($password);
       /// return $bcrypted;
        

        
        if($id == 1){
            $adminQuery = User::select('email', 'password','id')
                            ->where('email', '=', $email)
                            ->where('password','=',$password)
                            ->get();
         //  return $adminQuery;
            if(!$adminQuery->isEmpty()){
                return response()->json(['status'=>'success','role'=>'admin'],200); 
            }else{
                return response()->json(['status'=>'failure'],201);
            }
        }
        else if($id==2){
            $employeeQuery = Employee::select('email', 'password','id')
                            ->where('email', '=', $email)
                            ->where('password','=',$password)
                            ->get();
            if(!$employeeQuery->isEmpty()){
                //return response()->json(['status'=>'success','role'=>'employee'],300); 
                $q1 = Employee::select('id')
                                ->where('email', '=', $email)->get()->first();
               // return $q1;
                
               // return $time = $var->toTimeString();
               // return $q1;
                $attendance = new Attendance();
                $attendance->employee_id = $q1['id'];
                
                $attendance->date = $format;
                $attendance->year = Carbon::now()->format('Y');
                    $month = $var->format('F');
                    $attendance->month = $month;

                $attendance->login_time = '09:00:00';
              //  $attendance->login_time = $var->toTimeString();
                $attendance->status = $logged_in;
                $attendance->save();
                if($attendance){
                    return response()->json(['status'=>'success','data'=>$attendance,'role'=>'employee'],200); 
                }else{
                    return response()->json(['status'=>'atfailure'],201); 
                }
                

            }else{
                return response()->json(['status'=>'failure'],301);
            }
        }
        else{
            return response()->json(['status'=>'Unauthorised'],500);
        }
    } 


    public function Logout(Request $request,$id){
             #id is employee id. . . . 
             $date = Carbon::now()->toDateString();
             $var  = Carbon::now('Asia/Kolkata');
             $logged_out = 0; 
             $attendance = new Attendance();
             //so we are getting the id of the record for particular employee id and date
             $query = Attendance::select('id','login_time')
                               ->where('date',$date)
                               ->where('employee_id',$id)
                               ->get()->first();
            //so i am getting the record id. . . 
            $record_id = $query['id'];
            $loginTime = $query['login_time'];
            $record = Attendance::find($record_id);
            $record->logout_time =  $var->toTimeString();
            $diff = $this->convTimeToHours($loginTime,$record->logout_time);
            //return $diff;
            //for the purpose of the count for the day..
             if($diff > 4 AND $diff < 8){
                $count = 0.5; //Half day. . . 
            }
            else if($diff == 4){
                $count = 0.5; //Half day. . . 
            }
            else if($diff == 8 OR $diff >= 8){
                $count = 1;
            }else if($diff < 4){
                $count = 0;
                //return response()->json(['status'=>'cannot','role'=>'employee'],200); 
            }
            $record->count = $count;
            $record->status = $logged_out; //logout status. . .
            $record->save();
             if($attendance){
                return response()->json(['status'=>'updated','role'=>'employee','data'=>$record],200); 
            }else{
                return response()->json(['status'=>'failure','data'=>$record],200); 
            }


             

    }

    public function convTimeToHours($intime, $outtime)
    {
        $time1 = strtotime($intime);
        $time2 = strtotime($outtime);
        $difference = round((abs($time2 - $time1) / 3600),2);
        return $difference;
    } 


    public function Roles(Request $request){
        $roles = Role::all();
        return response($roles);
    }


    //To get the year stored in the database. . . 

    public function GetAllMonths(Request $request){
        $months = Attendance::select('month')->groupBy('month')->get();
        return response($months);
    }

    public function GetAllYears(Request $request){
        $years = Attendance::select('year')->groupBy('year')->get();
        return response()->json(["data"=>$years]);
    }

    public function DisplayUserName(Request $request,$id){
        $q1 = Employee::where('id',$id)
                    ->get();
        if($q1){
            return response()->json(["data"=>$q1],200);
        }else{
             return response()->json(["data"=>"failure"],200);
        }
   
    }

    public function GetAllMonthsSalary(Request $request){
        $months = Salary::select('month')->groupBy('month')->get();
        return response($months);
    }

    public function passwordCheck(Request $request){
        $password = $request->password;
        $bcrypted = bcrypt($password);
        if(password_verify($password, $bcrypted)) {
            return "Matched";
        }else{
            return "Not";
        }
      //  return $password;
    }
}
