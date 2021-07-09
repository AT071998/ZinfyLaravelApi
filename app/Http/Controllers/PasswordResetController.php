<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use App\Models\Employee;

class PasswordResetController extends Controller
{
    
    
    public function create(Request $request)
    {
        $otp = mt_rand(1000, 99999);
        $user = User::where('email', $request->email)->first();
        $emp = Employee::where('email',$request->email)->first();
        if (!$user and !$emp){
            return response()->json(['message' => 'We cant find a user with that e-mail address.'], 404);
        }
        else if(!$emp)
        {
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Str::random(40),
                    'otp' => $otp,
                 ]
            );

            $otp1 = PasswordReset::where('email',$request->email)
                                    ->select('otp')
                                    ->get()
                                    ->first();
           // return $otp1['otp'];
            if ($user && $passwordReset)
            {
                $user->notify(
                    new PasswordResetRequest($otp1['otp'])
                );
                return response()->json(['success'=>'success',
                'message' => 'We have e-mailed your password reset link!'
            ]);
            }
        }
            else if(!$user){
                 $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $emp->email],
                [
                    'email' => $emp->email,
                    'token' => Str::random(40),
                    'otp' => $otp,
                 ]
            );

            $otp1 = PasswordReset::where('email',$request->email)
                                    ->select('otp')
                                    ->get()
                                    ->first();
           // return $otp1['otp'];
            if ($emp && $passwordReset){
                $emp->notify(
                    new PasswordResetRequest($otp1['otp'])
                );
                return response()->json(['success'=>'success',
                'message' => 'We have e-mailed your password reset link!'
            ]);
            }
        }



            
       
    }


    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return response()->json($passwordReset);
    }
   public function reset(Request $request)
    { 

       // return "Hello";
        
        $passwordReset = PasswordReset::where([
            ['email', $request->email]
        ])->first();
        
        $user = User::where('email', $passwordReset->email)->first();
        if($user->count()>0){
               // $passwordReset->delete();
                $user->notify(new PasswordResetSuccess($passwordReset));
        }
        else{
            $emp = Employee::where('email', $passwordReset->email)->first();
            $emp->notify(new PasswordResetSuccess($passwordReset));
        }
        
        return response()->json(["data"=>"success"]);
   
    }


    public function checkOTP(Request $request,$email){
         $otp1 = PasswordReset::where('email','=',$email)
                                ->select('otp')
                                ->get()
                                ->first();
         return response()->json(['data'=>$otp1]);
    }

    //changePassword in login section. . .

    public function changePasswordLogin(Request $request,$email){

        $q1 = User::where('email','=',$email);
        $q2 = Employee::where('email','=',$email);
    //    return $q1->count();
        if($q1->count() > 0){
                $password =bcrypt($request->password);
                $affectedRows = User::where("email", '=',$email)->update(["password" => $password]);
                if($affectedRows > 0){
                    return response()->json(["Status"=>"success"]);
                }
        }
        else if($q2->count() >0){
                $password = bcrypt($request->password);
                $affectedRows = Employee::where("email", '=',$email)->update(["password" => $password]);
                if($affectedRows > 0){
                    return response()->json(["Status"=>"success"]);
                }
        }else{
            return response()->json(["status"=>"failure"]);
        }
        

    }

    public function changePassword(Request $request){
       $password =$request->password;
       $affectedRows = User::where("id", '=',1)->update(["password" => bcrypt($password)]);
                if($affectedRows > 0){
                    return response()->json(["Status"=>"success"]);
                } 
    }
}
