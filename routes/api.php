<?php

use Illuminate\Http\Request;

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
//Login
Route::post('/login', [\App\Http\Controllers\Api\Auth\LoginController::class, 'login']);
//Forgot pswrd
Route::post('/forgot-password', [\App\Http\Controllers\Api\Auth\LoginController::class, 'forgot']);
//Reset pwrd
Route::post('/reset-password', [\App\Http\Controllers\Api\Auth\LoginController::class, 'reset']);
//Logout
Route::post('/logout', [\App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);

//Middleware
Route::middleware(['api.response.auth'])->group(function () {
    //Admin
    Route::middleware('role:admin')->group(function () {
        //Dashboard
        Route::get('admin-dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'dashboard']);
        //User
        Route::get('admin-users', 'Api\Admin\AdminController@all_users');
        //Companies
        Route::get('admin-companies', 'Api\Admin\DashboardController@companies');
        Route::get('admin-company-service', 'Api\Admin\DashboardController@company_services');
        //Send sms
        Route::post('admin-send-sms-notification', 'Api\Admin\AdminController@send_sms');
        //Department
        Route::get('admin-department', 'Api\Admin\DashboardController@department');
        //User
        Route::post('admin-user-search', 'Api\Admin\UserController@user_search');
        Route::get('admin-all-users', 'Api\Admin\UserController@index');
        Route::get('admin-users-filter', 'Api\Admin\UserController@users_filter');
        Route::post('admin-user-create', 'Api\Admin\UserController@store');
        Route::post('admin-user-update/{id}', 'Api\Admin\UserController@update');
        Route::post('admin-user-change-password/{id}', 'Api\Admin\UserController@update_password');
        Route::delete('admin-user-delete/{id}', 'Api\Admin\UserController@destroy');
        Route::post('admin-usearch-payperiod', 'Api\Admin\UserController@usearch_payperiod');
        Route::post('admin-user-srexport-payperiod', 'Api\Admin\UserController@allexport_data');
        //Timesheets
        Route::post('admin-user-timesheets/{id}', 'Api\Admin\TimesheetController@timesheets');
        Route::post('admin-user-update-timesheet/{id}', 'Api\Admin\TimesheetController@update');
        
    });  
    //Supervisor 
    Route::middleware('role:supervisor')->group(function () {
        //Dashboard
        Route::get('supervisor-dashboard', 'Api\Supervisor\DashboarController@dashboard');
        //Companies
        Route::get('supervisor-companies', 'Api\Supervisor\TimesheetController@companies');
        Route::get('supervisor-company-service', 'Api\Supervisor\TimesheetController@company_services');
        //Users
        Route::get('susers', 'Api\Supervisor\UserController@index');
        Route::get('susers-filter', 'Api\Supervisor\UserController@user_filter');
        //Timesheets 
        Route::get('susers-timesheets/{id}', 'Api\Supervisor\UserController@timesheets');
        Route::post('susers-search-payperiod', 'Api\Supervisor\TimesheetController@search_payperiod');
        Route::post('susers-time-search-payperiod', 'Api\Supervisor\TimesheetController@nsearch_payperiod');
        Route::post('susers-timesheet-update/{id}', 'Api\Supervisor\TimesheetController@update');
        Route::delete('susers-timesheet-destroy/{id}', 'Api\Supervisor\TimesheetController@destroy');
        Route::post('susers-timesheet-approve', 'Api\Supervisor\TimesheetController@approve_time');
        Route::post('susers-timesheet-decline', 'Api\Supervisor\TimesheetController@decline_time');
        Route::post('susers-timesheet-delete', 'Api\Supervisor\TimesheetController@delete_time');
        Route::get('susers-time', 'Api\Supervisor\UserController@time_index');
        Route::post('susers-time-approve', 'Api\Supervisor\UserController@utimesheets');
        //Vocations
        Route::get('suser-vaccation-hours', 'Api\Supervisor\VaccationController@vocations_hours');
        Route::post('suser-vaccation-approve/{id}', 'Api\Supervisor\VaccationController@vacc_approve');
        Route::post('suser-vaccation-decline/{id}', 'Api\Supervisor\VaccationController@vacc_decline');
        //Issues
        Route::get('suser-issues', 'Api\Supervisor\ListProblemController@index');
        Route::post('suser-issue-create', 'Api\Supervisor\ListProblemController@store');
        Route::post('suser-issue-checked/{id}', 'Api\Supervisor\ListProblemController@issue_approve');
        Route::post('suser-issue-unchecked/{id}', 'Api\Supervisor\ListProblemController@decline');
        //Reports
        Route::get('all-susers', 'Api\Supervisor\ReportController@all_users');
        Route::get('susers-report', 'Api\Supervisor\ReportController@all_users_report');
        Route::get('susers-sign-signout', 'Api\Supervisor\ReportController@sign_signout_users');
        Route::get('susers-sign-signout-report', 'Api\Supervisor\ReportController@sign_signout_users_report');
        //
        Route::get('susers-payroll-filter', 'Api\Supervisor\ReportController@payroll_filter');
        Route::post('susers-payroll-report', 'Api\Supervisor\ReportController@payroll_report');
        Route::post('susers-payperiod-report', 'Api\Supervisor\ReportController@search_by_payperiod_report');
        //
        Route::get('susers-payperiod-search', 'Api\Supervisor\ReportController@search_by_payperiod');
        Route::post('susers-payperiod-search-postdata', 'Api\Supervisor\ReportController@post_data');
    }); 
    //User
    Route::middleware('role:user')->group(function () {
        //Dashboard
        Route::get('/user-dashboard', 'Api\User\DashboardController@dashboard');
        //Companies
        Route::get('/user-companies', 'Api\User\TimesheetController@companies');
        Route::get('/user-company-service', 'Api\User\TimesheetController@company_services');
        //Timesheets
        Route::get('/user-time-sheets', 'Api\User\TimesheetController@index');
        Route::post('/user-time-sheets-create', 'Api\User\TimesheetController@store');
        Route::post('/user-time-sheets-update/{id}', 'Api\User\TimesheetController@update');
        Route::delete('/user-time-sheets-delete/{id}', 'Api\User\TimesheetController@destroy');
        //Vocations
        Route::get('/user-vaccations', 'Api\User\VaccationController@index');
        Route::post('/user-vaccations-create', 'Api\User\VaccationController@store');
        Route::post('/user-vaccations-update/{id}', 'Api\User\VaccationController@update');
        Route::delete('/user-vaccations-delete/{id}', 'Api\User\VaccationController@destroy');
        //Issues
        Route::get('/user-issues', 'Api\User\ListProblemController@index');
        Route::post('/user-issues-create', 'Api\User\ListProblemController@store');
    });

    //Common for admin user and supervisor
    //Profile update
    Route::post('/profile-update', 'Api\ProfileController@profile_update');
    //Change password
    Route::post('/change-password', 'Api\ProfileController@change_password');
});



















