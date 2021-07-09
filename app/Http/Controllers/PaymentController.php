<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\College;
use App\Models\Employee;
use App\Models\AdvancePayment;
use Carbon\Carbon;
use DB;
use App\Models\Salary;
use App\Models\Attendance;


class PaymentController extends Controller
{
    public function store(Request $request,$id)
    {
    	    $var  = Carbon::now('Asia/Kolkata');
    		$payment = new AdvancePayment();
    		$payment->employee_id = $id;
    		$payment->status = $request->status;
    		if($payment->status === "First"){
    			//return response()->json(['status'=>'success','message' => 'Successfully Added'], 201);
	    		$payment->amount = $request->amount;
	    		$date = $request->paidDate;          
	            $payment->paidDate = Carbon::createFromFormat('d-m-Y', $date)
	                                                    ->format('Y-m-d');

	            $payment->year = Carbon::now()->format('Y');
	            $month = $var->format('F');
	            $payment->month = $month;
	            $payment->status = $request->status;
	            $payment->days = date('t');
	            $payment->pendingAmount = $request->pendingAmount;           
	            $payment->save();           	
	           	if($payment->save()){
	           		return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$payment], 201);
	           	}else{
	           		return response()->json(['status'=>'failure'], 201);
	           	}               
    		}else if($payment->status === "NTime"){

    			$payment->amount = $request->amount;
	    		$date = $request->paidDate;          
	            $payment->paidDate = Carbon::createFromFormat('d-m-Y', $date)
	                                                    ->format('Y-m-d');

	            $payment->year = Carbon::now()->format('Y');
	            $month = $var->format('F');
	            $payment->month = $month;
	            $payment->status = $request->status;
	            $payment->days = date('t');
	            $payment->pendingAmount = $request->pendingAmount;

	            $payment->save();           	
	           	if($payment->save()){
	           		return response()->json(['status'=>'success','message' => 'Successfully Added','data'=>$payment], 201);
	           	}else{
	           		return response()->json(['status'=>'failure'], 201);
	           	}               
    		}
        }

       

        public function checkForAdvanceSalary(Request $request,$id){
        	$q1 = Employee::select('salary')
        					->where("id","=",$id)
        					->get();
        	$q2 = AdvancePayment::select('pendingAmount')
        						->where('employee_id','=',$id)
        						->get();
        	return response()->json(["data"=>$q1,"data2"=>$q2]);
        }

        //For employee advance salary history. . . . 

        public function historyA(Request $request){
        //	$year = Carbon::now()->format('Y');
        //	$month = Carbon::now()->format('m');
        	$q1 = AdvancePayment::join('employees', 'employees.id', '=', 'advancepayment.employee_id')
					            ->select('employees.*', 'advancepayment.*',DB::raw("DATE_FORMAT(advancepayment.paidDate, '%d-%M-%Y') as paid"))
					         //   ->whereYear('paidDate', '=', $year)
					             // ->whereMonth('paidDate', '=', $month)
					              ->get();
    		return response()->json(["data"=>$q1],200);
        	
        }


        public function PendingAmoutForEmployee(Request $request,$id){
        	$q1 = AdvancePayment::where('employee_id','=',$id)->latest('id')->take(1)->get();
        	if ( $q1->isEmpty() ) {
	  						return response(['data' => 'error'], 404);
					}
	        	else{
	                 return response()->json(["data"=>$q1]);
	           }
        }

        public function FindFirst(Request $request,$id){
        	$q1 = AdvancePayment::where('employee_id','=',$id)
        						->where('status','=','First')->latest('id')->take(1)->get();
        //	return $q1;
        	if ( $q1->isEmpty() ) {
	  						return response(['data' => 'error'], 404);
					}
	        	else{
	                 return response()->json(["data"=>$q1]);
	           }
        	//return response()->json(["data"=>$q1]);
        }



         public function historyAEmployee(Request $request,$id,$year,$month){

        	$q1 = AdvancePayment::join('employees', 'employees.id', '=', 'advancepayment.employee_id')
					            ->select('employees.*', 'advancepayment.*',DB::raw("DATE_FORMAT(advancepayment.paidDate, '%d-%m-%Y') as paid"))
					              ->whereYear('paidDate', '=', $year)
					              ->whereMonth('paidDate', '=', $month)
					              ->where('advancepayment.employee_id','=',$id)
					              ->get();
			if ( $q1->isEmpty() ) {
	  				return response(['data' => 'error'], 404);
					}
	        	else{
	               return response()->json(["data"=>$q1],200);
	           }
        	
    		
        	
        }

         public function historyAEmployeee(Request $request,$id,$year,$month){

        	$q1 = AdvancePayment::join('employees', 'employees.id', '=', 'advancepayment.employee_id')
					            ->select('employees.*', 'advancepayment.*',DB::raw("DATE_FORMAT(advancepayment.paidDate, '%d-%m-%Y') as paid"))
					          	  ->where('advancepayment.year','=',$year)
					          	  ->where('advancepayment.month','=',$month)
					              ->where('advancepayment.employee_id','=',$id)
					              ->get();
			if ( $q1->isEmpty() ) {
	  				return response(['data' => 'error'], 404);
					}
	        	else{
	               return response()->json(["data"=>$q1],200);
	           }
        	
    		
        	
        }






        //Salary. . . 


        public function PaySalary(Request $request,$id){

        	$var  = Carbon::now('Asia/Kolkata');
        	$q0 = Employee::select('salary')->where('id','=',$id)->get()->first();
        	$salary = $q0['salary'];
        	$amount = 0;
        	$count = 0;
        	$today = \Carbon\Carbon::now(); //Current Date and Time
			$lastDayofMonth = Carbon::parse($today)->endOfMonth()->toDateString();
			$q1 = Salary::where('DatePayment','=',$lastDayofMonth)
						->where('employee_id','=',$id)
						->get();
			if ($q1->isEmpty()){
	  			//return "Check for advance salary";
	  			$year = Carbon::now()->format('Y');
	            $month = $var->format('F');
	           // return $month;
	  			$q2 = AdvancePayment::select('pendingAmount')
	  								->where('employee_id','=',$id)
	  								->where('year','=',$year)
	  								->where('month','=',$month)
	  								->get()->first();
	  			//return $q2;
	  			//return $q2['pendingAmount']; //working
	  			if(is_null($q2)){
	  				//Setting initial salary. . . 
	  				//return "hello";
	  				$q3 = Employee::select('salary')
	  							->where('id','=',$id)
	  							->get()->first();
	  				//return $q3['salary'];
	  				$amount = $q3['salary'];
	  				//return "hello";
	  				//return $amount;
	  				//retrieving the initial salary
	  			

	  			}
	  			else{
	  				
	  				$amount = $q2['pendingAmount'];
	  			//	return $amount;
	  				//return $amount;
	  				//retrirving the pending amount as the initial salary. . . 
	  			}
	  			//Checking for attendance count as of now. . . . 
	  			$q4 = Attendance::select(DB::raw('sum(count) as total_count'))
	  							->where('employee_id','=',$id)
	  							->where('month','=',$month)
	  							->where('year','=',$year)
	  							->get()->first();
	  			//If there is no attendance. . 
	  			if($q4['total_count']===null){
	  				$count = 0;
	  				return "There is no payment to be done. . ";
	  			}//For attendance count. . . 
	  			else{
	  				$count = $q4['total_count'];
	  				//Obtaining the per day amount . . 

		  			$PerDay = $this->calculatePerDay($amount);
		  			$DatePayment = date('Y-m-d');
		  			
		  			//return $PerDay;
		  			$SalaryPaid = round($PerDay * $count,0);
		  			return response()->json(["q1"=>$count,"q2"=>$amount,"q3"=>$PerDay,"q4"=>$SalaryPaid,"q5"=>$month]);

	  			}
			}
	        else{
	            return response()->json(["status"=>"already"],200);
	        }
        	
        }
        

        public function Paid(Request $request,$id){
        	$salary = new Salary();
        	$var  = Carbon::now('Asia/Kolkata');
        	$perDay = $request->perDay;
        	$count = $request->count;
        	$salary->employee_id = $id;
        	$salary->year = Carbon::now()->format('Y');
	        $month = $var->format('F');
	        $salary->month = $month;
		  	$salary->SalaryPaid = $perDay * $count;
		  	$salary->DatePayment = date('Y-m-d');
		  	$salary->TimePayment = $var->toTimeString();
		  	$salary->status = $request->status;
		  	$salary->save();
		  		if($salary->save()){
	           		return response()->json(['status'=>'success','data'=>$salary], 201);
	           	}else{
	           		return response()->json(['status'=>'failure'], 201);
	           	}       
		  	
        }

         public function calculatePerDay($salary){
        	$noOfdays = date('t');
        	$perDay = round(((float)($salary)/(float)($noOfdays)),2);
        	return $perDay;
        }


        public function DisplaySalary(Request $request){
        	$var  = Carbon::now('Asia/Kolkata');
        	$month = $var->format('F');
        	$q1 = Salary::join('employees', 'employees.id', '=', 'salary.employee_id')
					            ->select('employees.*', 'salary.*',DB::raw("DATE_FORMAT(salary.DatePayment, '%d-%m-%Y') as paidDate"))
					            ->latest()
					             ->get();

			return response()->json(['status'=>'success','data'=>$q1,"month"=>$month], 201);
        }
        

        public function SalaryStatus(Request $request,$id){

        	$q1 = Salary::select('Salary.*',DB::raw("DATE_FORMAT(salary.DatePayment, '%d-%m-%Y') as paidDate"))->where('employee_id',$id)->get();
			return response()->json(['data'=>$q1], 201);
        }


        public function SalaryView(Request $request,$id){
        	
        	$q1 = Salary::select('Salary.*',DB::raw('EXTRACT(MONTH FROM salary.DatePayment) AS monthname'),
					DB::raw("TIME_FORMAT(salary.TimePayment, '%h:%i') AS time"),DB::raw("DATE_FORMAT(salary.DatePayment, '%d-%m-%Y') as paidDate"),DB::raw("DATE_FORMAT(salary.DatePayment, '%M') as month"))
        				->where('employee_id','=',$id)
        				->orderBy('salary.DatePayment','DESC')
	        				->get();
	        return response()->json(['data'=>$q1],201);
        }


        //Advance Pay List. . .

        public function histryAdvanceList(Request $request){
        		$q1 = AdvancePayment::join('employees','employees.id','=','AdvancePayment.employee_id')
                 					->get();
                return response()->json(["data"=>$q1]);
        }
        

        //Obtaining the salary of the employee. . . 

        public function SalaryOfEmployee(Request $request,$id){
        	$q1 = Employee::select('salary')
        					->where('id','=',$id)
        					->get();
        	return response()->json(["data"=>$q1]);
        }


        //Generate the salary of the slip  . . .

        public function SalarySlip(Request $request){
        	$id = $request->id;
        	$var  = Carbon::now('Asia/Kolkata');
        	$q0 = Employee::select('salary')->where('id','=',$id)->get()->first();
        	$salary = $q0['salary'];
        	$amount = 0;

        	$count = 0;
        	$today = \Carbon\Carbon::now(); //Current Date and Time
			$lastDayofMonth = Carbon::parse($today)->endOfMonth()->toDateString();
			$q1 = Salary::where('DatePayment','=',$lastDayofMonth)
						->where('employee_id','=',$id)
						->get();
			if ($q1->isEmpty()){
	  			//return "Check for advance salary";
	  			$year = $request->year;
	  			$month = $request->month;
	           // return $month;
	  			$q2 = AdvancePayment::select('pendingAmount')
	  								->where('employee_id','=',$id)
	  								->where('year','=',$year)
	  								->where('month','=',$month)
	  								->get()->first();
	  			//return $q2;
	  			//return $q2['pendingAmount']; //working
	  			if(is_null($q2)){
	  				//Setting initial salary. . . 
	  				//return "hello";
	  				$q3 = Employee::select('salary')
	  							->where('id','=',$id)
	  							->get()->first();
	  				//return $q3['salary'];
	  				$amount = $q3['salary'];
	  				//return "hello";
	  				//return $amount;
	  				//retrieving the initial salary
	  			

	  			}
	  			else{
	  				
	  				$amount = $q2['pendingAmount'];
	  			//	return $amount;
	  				//return $amount;
	  				//retrirving the pending amount as the initial salary. . . 
	  			}
	  			//Checking for attendance count as of now. . . . 
	  			$q4 = Attendance::select(DB::raw('sum(count) as total_count'))
	  							->where('employee_id','=',$id)
	  							->where('month','=',$month)
	  							->where('year','=',$year)
	  							->get()->first();
	  			//If there is no attendance. . 
	  			if($q4['total_count']===null){
	  				$count = 0;
	  				return "There is no payment to be done. . ";
	  			}//For attendance count. . . 
	  			else{
	  				$count = $q4['total_count'];
	  				//Obtaining the per day amount . . 

		  			$PerDay = $this->calculatePerDay($amount);
		  			$DatePayment = date('Y-m-d');
		  			
		  			//return $PerDay;
		  			$SalaryPaid = round($PerDay * $count,0);
		  			$q6 = Attendance::select(DB::raw('COUNT(count) as FullDay'))
		  						->where('employee_id','=',$id)
		  						->where('count','=',1)
		  						->where('month','=',$month)
	  							->where('year','=',$year)
		  						->get()
		  						->first();
		  			$fullday = $q6['FullDay'];
		  						$q7 = Attendance::select(DB::raw('COUNT(count) as HlafDay'))
		  						->where('employee_id','=',$id)
		  						->where('count','=',0.5)
		  						->where('month','=',$month)
	  							->where('year','=',$year)
		  						->get()
		  						->first();
		  			$HlafDay = $q7['HlafDay'];
		  			$fixedSalary = Employee::select('salary')
		  			->where('id','=',$id)
		  			->get()
		  			->first();
		  			$salary = Salary::select('salaryPaid',DB::raw("DATE_FORMAT(salary.DatePayment, '%d-%m-%Y') as paidDate"),'TimePayment')
		  								->where('employee_id','=',$id)
		  								->where('month','=',$month)
	  							->where('year','=',$year)
		  						->get()
		  						->first();
		  			//return $q7;
       
		  			return response()->json(["q1"=>$count,"q2"=>$amount,"q3"=>$PerDay,"q4"=>$SalaryPaid,"q5"=>$month,"q6"=>$fullday,"q7"=>$HlafDay,"salary"=>$salary,"fixedsalary"=>$fixedSalary]);

	  			}
			}
	        else{
	            return response()->json(["status"=>"already"],200);
	        }
        }
    }
