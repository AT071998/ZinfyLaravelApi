<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Leave;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\Course;
use App\Models\College;
use App\Models\Attendance;
use App\Models\Salary;
use DB;

class GeneralController extends Controller
{
    //To display the employees. . . 
    public function index()
    {
        $emps = Employee::all();
        if(is_null($emps)){
            return response()->json(["msg"=>"No records"]);
        }else{
            return response()->json(["data"=>$emps]); 
        }
        
    }

    public function employee_add(Request $request)
    {
        //$data = $request->all();
        $validator = Validator::make($data, [
            'employee_name' => 'required|max:255|string',
            'designation' =>'required|max:255|string',
            'email' => 'required|string|email|unique:employees',
            'phone' => 'required|unique:employees',
            'password' => 'required|string|confirmed', //dint specified the valdation for password
            'fixed_salary' => 'required',
        ]);
        if($validator->fails()){
            return response(['error' => $validator->errors(),'Validation Error']);
        }
        //Performing individual validation. . . 
        $email_status = Employee::where("email",$request->email)->first();
        $phone_status = Employee::where("phone",$request->phone)->first();
        if(is_null($email_status)){
            return response()->json(["message"=>"Email already exists!"]);
        }
        else if(is_null($phone_status)){
            return response()->json(["message"=>"Phone number already exists"]);
        }else {
            $emp = new Employee([
                'employee_name' => $request->employee_name,
                'designation' => $request->designation,
                'email'=>$request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'fixed_salary'=>$request->fixed_salary,
                'employee_uuid'=>$this->generateRegistrationId(),
            ]);
            if($emp->save()){
                return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$emp], 201);
            }
            return response()->json(['status'=>'failure','message' => 'Something went wrong. . '], 200);

        }
        
    }

     public function showDropdownCollege(Request $request)
    {
       $res = College::all();
       return response($res);
    }




    //Admin Dashboard  . . . 

    public function AllData(Request $request){
    	$date = Carbon::now();
    	$format = $date->toDateString();
    	$lastDayofMonth = Carbon::parse($date)->endOfMonth()->toDateString();
    	//return $format;
    	$regDate = Carbon::createFromFormat('Y-m-d', $lastDayofMonth)
                                            ->format('d M Y');
    	$q1 = Employee::select(DB::raw('count(employee_uuid)as uuid'))
    				   ->get();
    	$q2 = Leave::select(DB::raw('count(is_approved) as pendingLeave'))
    				->where('is_approved','=',0)
    				->get();
    	$q3 = Attendance::select(DB::raw('count(id) as count'))
    					->groupBy('employee_id')
    					->where('date','=',$format)
    					->where('status','=',1)
    					->get();
    	$q4 = User::select('companyName')
    				->get();
    	return response()->json(["q1"=>$q1,"q2"=>$q2,"q3"=>$q3,"q4"=>$q4,"lastDay"=>$regDate]);
    	
    	

    }

    //Admin Profile Details. . . 
    public function Profile(Request $request,$id)
    {
    	$q1 = User::where('id','=',$id)
    			->get();
    	return response()->json(["data"=>$q1]);
    }

    //Verify User Password. . . 
    public function verifyPass(Request $request){
    	$q1 = User::select('password as token')->where('id','=',1)->get();
    	if(is_null($q1)){
    		return response()->json(["data"=>"No"]);
    	}else{
    		
    		return response()->json(["data"=>$q1]);
    	}
    }

    //Password change.. . .
    public function passwordChange(Request $request,$role){
    	//Role is 1 for manager
    	if($role==1){
    			
    			$password = $request->password;
    			$affectedRows = User::where("id", 1)->update(["password" => $password]);
    			if($affectedRows > 0){
    				return response()->json(["Status"=>"success"]);
    			}
    	}
    	//Role is 2 for Employee
    	else if($role==2){
    		$id = $request->id;
    		$emp = Employee::find($id);
    		$emp->password = $request->password;
    		$emp->save();
    		if($emp->save()){
    			return response()->json(["Status"=>"success"]);
    		}else{
    			return response()->json(["Status"=>"failure"]);
    		}

    	}
    }

    //Editing the admin profile. . . .
    public function adminDetails(Request $request){
    	$user = User::all();
    	return $user;
    }

    public function editAdmin(Request $request){
    		$companyName = $request->companyName;
    		$phoneNumber = $request->phone;
    		$alternateNumber = $request->alternateNumber;
    		$email = $request->email;
    		$alternateEmail = $request->alternateEmail;
    		$Address = $request->Address;
    		$accountHandler = $request->accountHandler;
    		$GSITNumber= $request->gsit;
    		$affectedRows = User::where("id", 1)->update(["companyName"=>$companyName,"phoneNumber"=>$phoneNumber,"alternateEmail"=>$alternateEmail,"alternateNumber"=>$alternateNumber,"email"=>$email,"Address"=>$Address,"accountHandler"=>$accountHandler,"GSITNumber"=>$GSITNumber]);
    		if($affectedRows > 0){
    				return response()->json(["Status"=>"success"]);
    		}
    }

    //Reports. . . .

    //for the attendance report of selected month and year



    public function attendanceReportByMonth(Request $request){

        $month = $request->month;
        $year = $request->year;
        $days = $this->get_weekdays(5,2021);
      //  return $days;
        $q1  = Attendance::where('attendance.month','=',$month)
                        ->where('attendance.year','=',$year)
                        ->join('employees', 'attendance.employee_id','=','employees.id')
                    //    ->join('leaves','leaves.employee_id','=','employees.id')
                        ->select('employees.employee_name','attendance.employee_id','employees.employee_uuid',DB::raw("SUM(count) as count"),
                            DB::raw("round($days/SUM(count),2) as percentage"))
                        ->groupBy('employees.employee_name','attendance.employee_id','employees.employee_uuid')
                        ->get();  
        if($q1->isEmpty()){
            return response()->json(["status"=>"failure"]);
        }else{
            return response()->json(["status"=>"success","data"=>$q1]);

        }
        
    }

   function get_weekdays($m,$y) {
    $lastday = date("t",mktime(0,0,0,$m,1,$y));
    $weekdays=0;
    for($d=29;$d<=$lastday;$d++) {
        $wd = date("w",mktime(0,0,0,$m,$d,$y));
        if($wd > 0 && $wd < 6) $weekdays++;
        }
    return $weekdays+20;
}
    //for salary report. . .  
    
    public function salaryReportByMonth(Request $request){
        $month = $request->month;
        $year = $request->year;
        $q1 = Salary::join('employees','employees.id','=','Salary.employee_id')
                    ->select('salary.salaryPaid','employees.employee_name','employees.employee_uuid')
                    ->where('month','=',$month)
                    ->where('year','=',$year)
            ->get();

        $q2 = Salary::select(DB::raw('SUM(salary.salaryPaid) as TotalSalary'))
                    ->where('month','=',$month)
                    ->where('year','=',$year)
                    ->get();
        if($q1->isEmpty()){
            return response()->json(["data"=>"no record found"]);
        }else{
            return response()->json(["data"=>$q1,"total"=>$q2,"status"=>"paid"]);
        }
    }


    public function profileData(Request $request,$id){
        $q1 = Employee::where('id','=',$id)->get();
        if($q1->isEmpty()){
            return response()->json(["data"=>"404"]);
        }else{
            return response()->json(["data"=>$q1]);
        }
        
    }

    //Update the profile record. . . 
    public function EditProfileOfEmployee(Request $request,$id){
       // $q1 = Employee::find($id);
        $employee_name = $request->employee_name;
        $email = $request->email;
        $phone = $request->phone;
       // return $q1;

        $affectedRows = Employee::where("id", $id)->update(["employee_name"=>$employee_name,"email"=>$email,"phone"=>$phone]);
            if($affectedRows > 0){
                    return response()->json(["status"=>"success"]);
            }else{
                return response()->json(["status"=>"failure"]);
            }
        
    }

       public function showdata(Request $request,$id){
          $q1 = Employee::select('employee_name','email','phone')
                        ->where('id','=',$id)
                        ->get();
        return response()->json(["data"=>$q1,"status"=>"success"]);
    }
}
