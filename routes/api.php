<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/roles','App\Http\Controllers\LoginController@Roles');
Route::post('/login/{id}','App\Http\Controllers\LoginController@Login');
Route::post('/logout/{id}','App\Http\Controllers\LoginController@Logout');



Route::post('/check','App\Http\Controllers\LoginController@Check');
//Employee Routes. . . 


Route::post('/insertEmployee','App\Http\Controllers\EmployeeController@store');
Route::get('/showAllEmployee','App\Http\Controllers\EmployeeController@index');
Route::post('/editEmployee/{id}','App\Http\Controllers\EmployeeController@update');
Route::get('/viewEmployee/{id}','App\Http\Controllers\EmployeeController@show');
Route::delete('/deleteEmployee/{id}','App\Http\Controllers\EmployeeController@destroy');




//Add student routes. . . 
Route::get('/showcourses','App\Http\Controllers\StudentController@showDropdown');
Route::get('/showcolleges','App\Http\Controllers\GeneralController@showDropdownCollege');


Route::post('/addstudent','App\Http\Controllers\StudentController@store');
Route::post('/editstudent/{id}','App\Http\Controllers\StudentController@update');
Route::get('/showAllStudent','App\Http\Controllers\StudentController@index');
Route::get('/AllStudent','App\Http\Controllers\StudentController@DisplayAllStudents');
Route::get('/showStudent/{id}','App\Http\Controllers\StudentController@show');
Route::delete('/deleteStudent/{id}','App\Http\Controllers\StudentController@destroy');
Route::get('/show/{id}','App\Http\Controllers\StudentController@CourseName');



Route::get('/months','App\Http\Controllers\LoginController@GetAllMonths');
Route::get('/years','App\Http\Controllers\LoginController@GetAllYears');
Route::get('/monthSalary','App\Http\Controllers\LoginController@GetAllMonthsSalary');


Route::post('/attendanceview','App\Http\Controllers\EmployeeController@attendanceViewForWorkingDays');
Route::post('/attendancePresent','App\Http\Controllers\EmployeeController@attendanceViewForPresent');

Route::post('/attendanceHalf','App\Http\Controllers\EmployeeController@attendanceViewForHalf');

Route::get('/attendancedate/{id}','App\Http\Controllers\EmployeeController@attendanceViewDatewise');



//Generating total fees to be paid. . . 
Route::post('/fee','App\Http\Controllers\StudentController@GenerateTotal');


//EmployeeLeave. . . 

Route::post('/leaveApply/{id}','App\Http\Controllers\LeaveController@store');
//Show all leave history to admin
Route::get('/showLeaveRequests','App\Http\Controllers\LeaveController@index');
//Show only approved leaves. . . .


Route::get('/leavereq/{id}','App\Http\Controllers\LeaveController@leave_status');
Route::get('/leaveemp/{id}','App\Http\Controllers\LeaveController@leave_history_employee');


Route::get('/showPending','App\Http\Controllers\LeaveController@pending');
//For Dashboard. . 
Route::get('/countPending/{id}','App\Http\Controllers\LeaveController@count_pending');
//Leave history for particular employee. . .
Route::get('/viewLeave/{id}','App\Http\Controllers\LeaveController@leave_history');
//Approve the leave by admin. . . . 
Route::post('/approve/{id}','App\Http\Controllers\LeaveController@approve');
//NoofdaysLeaveTaken
Route::get('/noofDays/{id}','App\Http\Controllers\LeaveController@calculate_noDays_Leave');



//Employee Attendance. . 
Route::get('/Displayempattendance','App\Http\Controllers\EmpAttendanceController@DisplayEmployee');
Route::post('/empattendance','App\Http\Controllers\EmpAttendanceController@insertAttendance');



//For dashboard. . .
Route::get('/dashboard/{id}','App\Http\Controllers\LoginController@DisplayUserName');

//AdvancePayment

Route::post('advancepay/{id}','App\Http\Controllers\PaymentController@store');
Route::get('perday/{id}/{salary}','App\Http\Controllers\PaymentController@calculatePerDay');
Route::get('checksalary/{id}','App\Http\Controllers\PaymentController@checkForAdvanceSalary');
Route::get('/pending/{id}','App\Http\Controllers\PaymentController@PendingAmoutForEmployee');
Route::get('/findfirst/{id}','App\Http\Controllers\PaymentController@FindFirst');

Route::get('/advancehistory/','App\Http\Controllers\PaymentController@historyA');
Route::get('/advancehistoryEmp/{id}/{year}/{month}','App\Http\Controllers\PaymentController@historyAEmployee');
Route::get('/advancehistoryEmps/{id}/{year}/{month}','App\Http\Controllers\PaymentController@historyAEmployeee');

Route::get('advanceList','App\Http\Controllers\PaymentController@histryAdvanceList');


Route::get('/salaryData/{id}','App\Http\Controllers\PaymentController@paysalary');
Route::post('/paysalary/{id}','App\Http\Controllers\PaymentController@Paid');
Route::get('/displaySalary/','App\Http\Controllers\PaymentController@displaySalary'); //For Admin
Route::get('/SalaryStatus/{id}/','App\Http\Controllers\PaymentController@SalaryStatus'); 
Route::get('/SalaryView/{id}/','App\Http\Controllers\PaymentController@SalaryView'); //For Employee


//Admin Dashboard. . 
Route::get('/AllData','App\Http\Controllers\GeneralController@AllData');
//Admin Profile Data. . . 
Route::get('/ManagerData/{id}','App\Http\Controllers\GeneralController@Profile');
//Verify Admin Password. . 
Route::get('/VerifyPassword/','App\Http\Controllers\GeneralController@verifyPass');
//For Password Change. . . 
Route::post('/passwordChange/{role}','App\Http\Controllers\GeneralController@passwordChange');



//Admin profile details
Route::get('/adminprofile/','App\Http\Controllers\GeneralController@adminDetails');
//Edit admin details. . 
Route::post('/editdetails/','App\Http\Controllers\GeneralController@editAdmin');


//ForgotPassword


 Route::post('/password/create/', 'App\Http\Controllers\PasswordResetController@create');
 Route::get('/password/check/{email}','App\Http\Controllers\PasswordResetController@checkOTP');
Route::post('/password/change/{email}','App\Http\Controllers\PasswordResetController@changePasswordLogin');
Route::post('/password/success/','App\Http\Controllers\PasswordResetController@reset');



//Reports. . . 

Route::post('/report/attendance','App\Http\Controllers\GeneralController@attendanceReportByMonth');
Route::post('/report/salary','App\Http\Controllers\GeneralController@salaryReportByMonth');


//Dashboard. . . 

Route::get('/getPendingLeave','App\Http\Controllers\LeaveController@pendingLeaveCount');


//For obtaining salary of employeee. . .
Route::get('/salaryEmployee/{id}','App\Http\Controllers\PaymentController@SalaryOfEmployee');

Route::post('/passwordChange/','App\Http\Controllers\PasswordResetController@changePassword');


Route::get('/salarySlip/{id}/{month}/{year}','App\Http\Controllers\PaymentController@SalarySlip');

Route::get('/notificationArea/{id}','App\Http\Controllers\LeaveController@notificationLeave');

Route::get('/profileData/{id}','App\Http\Controllers\GeneralController@profileData');

//For editing the employee. . . 
Route::post('editprofile/{id}','App\Http\Controllers\GeneralController@EditProfileOfEmployee');
Route::get('showdata/{id}','App\Http\Controllers\GeneralController@showdata');


Route::post('/passwordcheck/','App\Http\Controllers\LoginController@passwordCheck');