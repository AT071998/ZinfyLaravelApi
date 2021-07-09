<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Attendance;
use Response;
use Illuminate\Support\Facades\Crypt;


class EmployeeController extends Controller
{

    public function index()
    {
        $emps = Employee::all();
        if(is_null($emps)){
            return response()->json(["msg"=>"No records","code"=>"404"]);
        }else{
            return response()->json(["data"=>$emps]); 
        }
        
    }


    public function store(Request $request)
    {
        //$data = $request->all();
        //Performing individual validation. . . 
        $email_status = Employee::where("email",$request->email)->first();
        $phone_status = Employee::where("phone",$request->phone)->first();
        if(!is_null($email_status)){
            return response()->json(["message"=>"Email already exists!"]);
        }
        else if(!is_null($phone_status)){
            return response()->json(["message"=>"Phone number already exists"]);
        }else{
            $emp = new Employee();
            $emp->employee_name = $request->employee_name;
            $emp->designation = $request->designation;
            $emp->email = $request->email;
            $emp->salary = $request->salary;
            $emp->phone = $request->phone;
            $emp->employee_uuid = $this->generateRegistrationId();
         
         /*   $emp = new Employee([
                'employee_name' => $request->employee_name,
                'designation' => $request->designation,
                'email'=>$request->email,
                'phone' => $request->phone,
               // 'password' =>Crypt::encryptString(str::random(6)),
                'salary'=>$request->salary,
                'employee_uuid'=>$this->generateRegistrationId(),
            ]);
            */
            $emp->password = Crypt::encryptString(str::random(6));
            $decrypt= Crypt::decryptString($emp->password);
            //return $decrypt;
            $emp->save();
            if($emp->save()){
                    return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$emp,"token"=>$decrypt], 201);
            }
            else{
                return response()->json(['status'=>'failure','message' => 'Something went wrong'], 200);
            }
            

        }
        
    }

    function generateRegistrationId() {
        $id = 'ZINFY' . mt_rand(1000, 99999); // better than rand()
    
        // call the same function if the id exists already
        if ($this->registrationIdExists($id)) {
            return $this->generateRegistrationId();
        }
        // otherwise, it's valid and can be used
        return $id;
    }
    
    function registrationIdExists($id) {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return Employee::where('employee_uuid', $id)->exists();
    }


    function generatePassword(Request $request){
        $hashed_random_password = Hash::make(str_random(5));
        return $hashed_random_password;
    }

    public function update(Request $request, $id)
    {
        $id_status = Employee::where("id",$request->id)->first();
        //return $id_status;
        if(is_null($id_status)){
            return response()->json(['message' => 'Employee Not Found'], 200);
        }else{
            $emp = Employee::find($id);   //create a object of model crud
            $emp->employee_name = $request->get('employee_name');
            $emp->designation = $request->get('designation');
            $emp->email = $request->get('email');
            $emp->phone = $request->get('phone');
            $emp->salary= $request->get('salary');
            $emp->save();
            return response()->json(['message' => 'Successfully Updated','data'=>$emp], 201);
        }
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


    public function show(Request $request,Employee $employee,$id)
    {
        $id_status = Employee::where("id",$request->id)->first();
        if(is_null($id_status)){
            return response()->json(['message' => 'Employee Not Found For Particular ID'], 200);
        }else{
            $emp = Employee::find($id);
            return response()->json([ 'data' => $emp], 200);
        }
       
    }

    public function destroy(Request $request,$id)
    {
        $emp=Employee::find($id);  
        if($emp->delete()){
             return response()->json([ 'data' => 'deleted'], 200);
        } else{
             return response()->json([ 'data' => 'wrong'], 290);
        }
        
    }


    //To view the attendance of single employee...

        public function attendanceViewForWorkingDays(Request $request){

            $id = $request->get('id');
            $month = $request->get('month');
            $year = $request->get('year');

            $workingquery = Attendance::select(DB::raw('sum(count) as working'))
                                ->where("employee_id", "=", $id)
                                ->where("year", "=", $year)
                                ->where("month", "=", $month)
                                ->get()
                                ->first();

             $fulldayquery = Attendance::select(DB::raw('sum(count) as fullday'))
                                ->where("count",">",0.5)
                                ->where("employee_id", "=", $id)
                                ->where("year", "=", $year)
                                ->where("month", "=", $month)
                                
                                ->get()
                                ->first();

             $halfdayquery = Attendance::select(DB::raw('count(count) as halfday'))
                                ->where("count","=",0.5)
                                ->where("employee_id", "=", $id)
                                ->where("year", "=", $year)
                                ->where("month", "=", $month)
                              
                                ->get()
                                ->first();
           // return $halfdayquery;
            
            if($workingquery['working']=='' and $fulldayquery['fullday'] =='' and $halfdayquery['halfday'] == 0){
                return response()->json(['status'=>'404']);
            }else{
                return Response::json(['status'=>'data','q1' => $workingquery, 'q2' => $fulldayquery, 'q3' => $halfdayquery]);
            }
            
            
        }

        public function attendanceViewForPresent(Request $request){

            $id = $request->get('id');
            $month = $request->get('month');
            $year = $request->get('year');

            $query = Attendance::select(DB::raw('sum(count) as total'))
                                ->where("count",">",0.5)
                                ->where("employee_id", "=", $id)
                                ->where("year", "=", $year)
                                ->where("month", "=", $month)
                                ->get();
            if($query->isEmpty()){
                //return response()->json([ 'data' => $query], 200);
                return response()->json(['data'=>'No records found']);
            }else{
                //return response()->json(['data'=>'No records found']);
                return response($query);
            }
        }

         public function attendanceViewForHalf(Request $request){

            $id = $request->get('id');
            $month = $request->get('month');
            $year = $request->get('year');

            $query = Attendance::select(DB::raw('count(count) as total'))
                                ->where("count","=",0.5)
                                ->where("employee_id", "=", $id)
                                ->where("year", "=", $year)
                                ->where("month", "=", $month)
                                ->get();
            if($query->isEmpty()){
                //return response()->json([ 'data' => $query], 200);
                return response()->json(['data'=>'No records found']);
            }else{
                //return response()->json(['data'=>'No records found']);
                return response()->json([ 'data' => $query], 200);
            }
        }


        public function attendanceViewDatewise(Request $request,$id){
       //     $id = $request->get('employee_id');
          
            $results = DB::table('attendance as att')
                    ->where('att.employee_id',$id)
                    ->select("att.*", DB::raw("DATE_FORMAT(att.date, '%d') as formatted_dob"),DB::raw("TIME_FORMAT(login_time, '%h:%i') as login_time"),DB::raw("TIME_FORMAT(logout_time, '%h:%i') as logout_time"))
                    ->orderBy('id','DESC')
                    ->take(10)
                    ->get();

            if($results){
                return response()->json(['data'=>$results]); 
            }else{
                 return response()->json(['data'=>'No records found']);
            }
            
            
        }

    
    

    
}
