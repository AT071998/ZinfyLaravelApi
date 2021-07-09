<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Leave;
use Carbon\Carbon;
use DB;

class LeaveController extends Controller
{

    public function store(Request $request,$id)
    {
        //$data = $request->all();
            $document = new Leave();
            $document->employee_id = $id;
            $document->leave_type = $request->leave_type;

            $date = $request->date_from;


            
            $document->date_from = Carbon::createFromFormat('d-m-Y', $date)
                                                    ->format('Y-m-d');
            $dateto = $request->date_to;
            $document->date_to = Carbon::createFromFormat('d-m-Y', $dateto)
                                            ->format('Y-m-d');
            

            $diff = Carbon::parse($date )->diffInDays( $dateto );


            //$date->diffInDays($dateto);
            $document->days = $diff+1;

            $document->reason = $request->reason;
            //$currentDate = 
            //return $currentDate;

            
            $document->applied_on = date('Y-m-d');
            ///return $document->applied_on;
            $document->save();
            if($document->save()){
                    return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$document], 201);
            }else{
                 return response()->json(['status'=>'failure'], 201);
            }
            

        
        
    }

    //Show all leave history to admin

    public function index(Request $request){
       // $leaves = Leave::all();
      //  return response($leaves);


        $leaves = Leave::join('employees', 'Leaves.employee_id','=','employees.id')
    ->select('Leaves.*','Leaves.id as LeaveId','employees.*',DB::raw("DATE_FORMAT(Leaves.date_from, '%d-%m-%Y') as fromdate"),
DB::raw("DATE_FORMAT(Leaves.date_to, '%d-%m-%Y') as todate"),DB::raw("DATE_FORMAT(Leaves.applied_on, '%d-%m-%Y') as applieddate"))
    ->get();

    return response()->json(["data"=>$leaves],200);

    }
    //Get all the details of the pending leaves
    public function pending(Request $request){
        $users=Leave::where('is_approved',0)->get();
     //   $users=DB::table('leaves')
       //         ->where('is_approved',0)
          //          ->get();
        return $users;
    }



    //Count the number of pending leaves. . . . 
    public function count_pending(Request $request,$id){
        $pending=Leave::where('is_approved', 0)
                ->where("employee_id","=",$id)
                ->count();
        return response()->json(["data"=>$pending]);
    }
    //For employee dashboard. . 
    //Leave history

    public function leave_history(Request $request,$id){
        $stud = Leave::where('employee_id',$id)->get();
      //  $stud=DB::table('leaves')
        //        ->where('employee_id',$id)
         //       ->get();
        return response([ 'data' => $stud], 200);
    }


    public function display_leave_status(Request $request,$id){
        $q1 = Leave::where('id',$id)->get();
        return response(["data"=>$q1],200);
    }




    //for admin to approve the leave. . . .
    public function approve(Request $request,$id)
    {
        $leave = Leave::find($id);
        if($leave){
           $leave->is_approved = $request -> is_approved;
           $leave->approved_on = date('Y-m-d');
           $leave->save();
           if($leave->save()){
                return response(["status"=>"success"],200);
           }else{
                return response(["status"=>"failure"],200);
           }
       }else{
            return response(["status"=>"failure"],200);
       }
    }

    public function calculate_noDays_Leave(Request $request,$id){
       $leave = DB::table('leaves')
                ->where('employee_id',$id)
                ->select('employee_id',DB::raw('Sum(days) AS LeaveTotal'))
                ->groupBy('employee_id')
                ->get();
        return $leave;
    }



      public function leave_status(Request $request,$id){
        $leaves = Leave::join('employees', 'Leaves.employee_id','=','employees.id')
            ->select('Leaves.*','Leaves.id as LeaveId','employees.*',DB::raw("DATE_FORMAT(Leaves.date_from, '%d-%m-%Y') as fromdate"),
            DB::raw("DATE_FORMAT(Leaves.date_to, '%d-%m-%Y') as todate"),DB::raw("DATE_FORMAT(Leaves.applied_on, '%d-%m-%Y') as applieddate"))
                ->where('Leaves.id','=',$id)
            ->get();

        return response()->json(["data"=>$leaves],200);
    }


    //For emplyeee....
     public function leave_history_employee(Request $request,$id){
        $leaves = Leave::join('employees', 'Leaves.employee_id','=','employees.id')
            ->select('Leaves.*','employees.*',DB::raw("DATE_FORMAT(Leaves.date_from, '%d-%m-%Y') as fromdate"),
            DB::raw("DATE_FORMAT(Leaves.date_to, '%d-%m-%Y') as todate"),DB::raw("DATE_FORMAT(Leaves.applied_on, '%d-%m-%Y') as applieddate"))
                ->where('employees.id','=',$id)
            ->orderBy('applieddate','DESC')
            ->get();
        if($leaves->isEmpty()){
            return response()->json(["data"=>"404"],200);
        }else{
             return response()->json(["status"=>"data","data"=>$leaves],200);
        }

       
    }


    //Dashboard to fill the icon .. ..  . 

    public function pendingLeaveCount(Request $request){
        $q1  = Leave::where('is_approved', 0)
                ->count();
        return response()->json(["data"=>$q1]);
       
    }


    //Check leave notification. . . . 

    public function notificationLeave(Request $request,$id){
      //  $id = $request->id;
        //Id of the employee. . . .. 
        $q1 = Leave::select('*',DB::raw("DATE_FORMAT(applied_on, '%d-%m-%Y') as todate"))
                    ->Where('employee_id','=',$id)
                     ->where('is_approved','=','1')
                    ->get();
        $q2 = Leave::select('*',DB::raw("DATE_FORMAT(applied_on, '%d-%m-%Y') as todate"))
                    ->Where('employee_id','=',$id)
                     ->where('is_approved','=','2')
                    ->get();
        if($q1->isEmpty() and $q2->isEmpty()){
           
            return response()->json(["data"=>"No"]);
                
        }else if($q1->isEmpty()){
            return response()->json(["status"=>"data","data"=>$q2]);
        }else if($q2->isEmpty()){
             return response()->json(["status"=>"data","data"=>$q1]);
        }

    }
}
