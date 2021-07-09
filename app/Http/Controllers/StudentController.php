<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\College;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function showDropdown(Request $request)
    {
        $emps = Course::all();
        return response($emps);
       
    }




    public function DisplayAllStudents(Request $request){
       $users = Student::join('courses', 'students.course_id', '=', 'courses.id')
            ->join('college', 'college.id', '=', 'students.college_id')
            ->select('students.*', 'college.college_name', 'courses.course_name')
            ->get();
    return $users;
    }

    public function store(Request $request)
    {
       
        //Performing individual validation. . . 
        $email_status = Student::where("email",$request->email)->first();
        $phone_status = Student::where("phone",$request->phone)->first();
        if(!is_null($email_status)){
            return response()->json(["message"=>"Email already exists!"]);
        }
        else if(!is_null($phone_status)){
            return response()->json(["message"=>"Phone number already exists"]);
        }else{
            $document = new Student();
            $document->studentName = $request->studentName;
            $document->college_id = $request->college_id;
            $document->regId = $request->regId;
            $document->phone = $request->phone;
            $document->course_id = $request->course_id;
            $document->email = $request->email;
            $document->batchYear = $request->batchYear;
            $document->paid_fee = $request->paid_fee;
            $document->Total_fee = $request->Total_fee;
            $document->due_fee = $document->Total_fee - $document->paid_fee;
            $document->final_status=$request->final_status;
            $document->save();
            if($document->save()){
                return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$document], 201);
            }
            else{
                return response()->json(['status'=>'failure'], 201);
            }
        }
        
    }

    function CourseName(Request $request,$id){
        //return "hello";
        $userRole = Student::where('students.id', $id)
        ->leftJoin('courses', 'students.course_id', '=', 'courses.id')
        ->select(
            'courses.course_name'
    )
    ->first();
    return $userRole;
    }

    function GenerateTotal(Request $request){
        $var1 = $request->get('paid_fee');
        $var2 = $request->get('Total_fee');
        $total = $var2 - $var1;
        return response()->json(['data'=>$total]);
    }


    public function update(Request $request, $id)
    {
        $id_status = Student::where("id",$request->id)->first();
        //return $id_status;
        if(is_null($id_status)){
            return response()->json(['message' => 'Student Not Found'], 200);
        }else{
            $document = Student::find($id);   //create a object of model crud

            $document->studentName = $request->get('studentName');
            $document->collegeName = $request->get('collegeName');
            $document->regId = $request->get('regId');
            $document->phone = $request->get('phone');
            $document->course_id = $request->get('course_id');
            $document->email = $request->get('email');
            $document->batchYear = $request->get('batchYear');
            //For registration date formatting
            $date = $request->get('regDate');
            $document->regDate = Carbon::createFromFormat('d-m-Y', $date)
                                            ->format('Y-m-d');
            $document->projectname = $request->get('projectname');
            $document->language = $request->get('language');
            $document->duration = $request->get('duration');
            $document->paid_fee = $request->get('paid_fee');
            
            $document->Total_Fee = $request->get('Total_fee');
            $document->due_fee = $document->Total_Fee - $document->paid_fee;
            $document->final_status=$request->get('final_status');
            $document->save();
            return response()->json(['message' => 'Successfully Updated','data'=>$document], 201);
        }
    }
    public function index()
    {
        $stud = Student::all();
        if(is_null($stud)){
            return response()->json(["msg"=>"No records"]);
        }else{
            return response()->json(["data"=>$stud]); 
        }
    }

    public function show(Request $request,Student $student,$id)
    {
        $id_status = Student::where("id",$request->id)->first();
        if(is_null($id_status)){
            return response()->json(['message' => 'Student Not Found For Particular ID'], 200);
        }else{
            $stud = Student::find($id);
            return response([ 'data' => $stud], 200);
        }
       
    }

    public function destroy(Request $request,Student $student,$id)
    {
        $emp=Student::find($id);  
        if($emp->delete()){
             return response()->json([ 'data' => 'deleted'], 200);
        } else{
             return response()->json([ 'data' => 'wrong'], 290);
        }
        
        }


}
