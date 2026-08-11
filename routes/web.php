<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

//Home Page
Route::get('/', function () {
    return view('welcome');
});
//Term & conditions
Route::get('/terms-conditions', function () {
    return view('terms');
});

//Login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

//Register
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

//Password reset
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

//Forget password
Route::get('forget-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');

//Reset password
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset.password.get');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('reset.password.post');

//Admin login
Route::get('/admin-login', 'Admin\LoginController@index');
Route::post('/admin-submit', 'Admin\LoginController@dologin')->name('admin.login');

//Manager login
Route::get('/manager-login', 'Manager\LoginController@index');
Route::post('/manager-submit', 'Manager\LoginController@dologin')->name('manager.login');

//User login
Route::get('/user-login', 'User\LoginController@index');
Route::post('/user-submit', 'User\LoginController@dologin')->name('user.login');

//Middleware
Route::group(['middleware' => 'auth'], function () {
	//Admin Routes
	Route::group(['middleware' => 'admin'], function () {
		//Dasbobard
		Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');
		Route::get('/admin-logout', [\App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('admin.logout');
		//Profile
		Route::get('/admin/edit-profile', 'Admin\AdminController@edit_profile');
		Route::post('/admin/update-profile', 'Admin\AdminController@update_profile');
		//Reset password
		Route::get('/admin/change-password', 'Admin\AdminController@resetpassword');
		Route::post('/admin/updatepassword', 'Admin\AdminController@updatepassword');
		//Twillio
		Route::get('/smsnotifications', [\App\Http\Controllers\Admin\AdminController::class, 'sendSmsview']);
		Route::post('/smsnotification', [\App\Http\Controllers\Admin\AdminController::class, 'sendSms']);
		//Timesheet 
		Route::get('timesheetapproval', 'Admin\UserInfoController@timesheetapproval');
		//Users
		Route::resource('users', 'Admin\UserInfoController');
		Route::get('users/{frm_dt}/{to_dt}/', 'Admin\UserInfoController@user_with_date');
		Route::get('users/{frm_dt}/{to_dt}/{search_by_comp}', 'Admin\UserInfoController@user_with_com');
		Route::post('users/store', 'Admin\UserInfoController@store')->name('users.store');
		Route::post('users/update', 'Admin\UserInfoController@update')->name('users.update');
		Route::get('usersd/destroy/{id}', 'Admin\UserInfoController@destroy');
		//User exports
		Route::get('user/export/all', 'Admin\UserInfoController@exort_user');
		Route::get('user/test/export', 'Admin\UserInfoController@exort_user_new');
		//Driver license
		Route::get('user/driver/license/{id}', 'Admin\UserInfoController@driver_license');
		//Change password
		Route::get('user/changepassword/{id}', 'Admin\UserInfoController@resetpassword');
		Route::post('user/updatepassword', 'Admin\UserInfoController@updatepassword');
		//Bill rates
		Route::get('user/add_billrate', 'Admin\AdminController@add_billrate');
		Route::post('user/update_billrate', 'Admin\AdminController@update_billrate');
		//Timesheets
		Route::get('user/timesheets/{id}', 'Admin\UserInfoController@timesheets');
		Route::get('user/timesheets/{id}/{frm_dt}/{to_dt}/{search_by_comp}', 'Admin\UserInfoController@timesheets_company');
		Route::get('user/search_user', 'Admin\UserInfoController@search_user_by_comp');
		Route::get('user/musers/{id}', 'Admin\UserInfoController@musers');
		Route::get('user/musers/create/{id}', 'Admin\UserInfoController@mcreate');
		Route::post('user/musers/store', 'Admin\UserInfoController@mstore');
		Route::get('users/musers/destroy/{id}', 'Admin\UserInfoController@mdestroy');
		Route::get('user/create/timesheets/{id}', 'Admin\TimesheetaController@create');
		Route::post('user/store/timesheets', 'Admin\TimesheetaController@store');
		Route::get('user/edit/timesheets/{id}', 'Admin\TimesheetaController@edit');
		Route::get('user/destroy/timesheets/{id}', 'Admin\TimesheetaController@destroy');
		Route::post('user/update/timesheets', 'Admin\TimesheetaController@update');
		Route::get('user/search/time', 'Admin\TimesheetaController@srch_time');
		Route::get('user/search/times', 'Admin\TimesheetaController@srch_times');
		Route::get('user/search', 'Admin\UserInfoController@user_search');
		Route::any('user/searchs', 'Admin\UserInfoController@user_searchs');
		Route::get('user/nsearch', 'Admin\UserInfoController@nuser_search');
		Route::get('user/app_status/search', 'Admin\UserInfoController@app_status');
		Route::get('user/napp_status/search', 'Admin\UserInfoController@napp_status');
		Route::get('user/user_status/search', 'Admin\UserInfoController@user_status');
		Route::get('user/user_color/search', 'Admin\UserInfoController@user_color');
		Route::get('user/export/timesheet/data/{id}', 'Admin\TimesheetaController@export_data')->name('user.export.time-sheet');
		Route::get('user/export/timesheet/company/{comp}', 'Admin\UserInfoController@export_comp_data');
		Route::get('user/export/timesheet/srchdata/{frmdate}/{todate}/{id}', 'Admin\TimesheetaController@srcexport_data');
		Route::get('user/search/payperiod', 'Admin\UserInfoController@search_payperiod');
		Route::get('user/search/payperiods', 'Admin\UserInfoController@finace_search_payperiod');
		Route::get('user/search/fpayperiods', 'Admin\UserInfoController@finace_payperiod');
		//Payperiods
		Route::get('user/search/upayperiod', 'Admin\UserInfoController@usearch_payperiod');
		Route::get('user/nsearch/payperiod', 'Admin\UserInfoController@nsearch_payperiod');
		Route::get('user/aexport/payperiod/{frmdate}/{todate}/{search_by_comp}', 'Admin\UserInfoController@allexport_data');
		Route::any('/all/payperiod/paysearch/postdata', 'Admin\UserInfoController@post_paydata');
	    //Route::get('user/aexports/test/{frmdate1}/{todate1}/{search_by_comp1}', 'Admin\UserInfoController@allexport_data_new');
		//Route::get('time-sheets/destroy/{id}', 'User\TimesheetController@destroy');
		//Payperiod 
		Route::resource('payperiods', 'Admin\PayperiodsController');
		Route::post('payperiods/store', 'Admin\PayperiodsController@store')->name('payperiods.store');
		Route::post('payperiods/astore', 'Admin\PayperiodsController@astore')->name('payperiods.astore');
		Route::post('payperiods/update', 'Admin\PayperiodsController@update')->name('payperiods.update');
		Route::get('payperiod/destroy', 'Admin\PayperiodsController@destroy');
		
		//Manager
		Route::resource('casemanagers', 'Admin\CaseManagerInfoController');
		//Supervisor
		Route::resource('supervisors', 'Admin\SupervisorInfoController');
		//List problems
		Route::resource('lists-issue', 'Admin\ListsProblemController');
		Route::post('lists-issue/store', 'Admin\ListsProblemController@store')->name('lists-issue.store');
		Route::post('lists-issue/update', 'Admin\ListsProblemController@update')->name('lists-issue.update');
		Route::get('lists-issue/destroy/{id}', 'Admin\ListsProblemController@destroy');
		Route::get('lists-issue/approve/{id}', 'Admin\ListsProblemController@approve')->name('admin.approve');
		Route::get('lists-issue/decline/{id}', 'Admin\ListsProblemController@decline')->name('admin.decline');
		//Company
		Route::resource('companies', 'Admin\CompanyController');
		Route::post('companies/store', 'Admin\CompanyController@store')->name('compnay.store');
		Route::post('companies/update', 'Admin\CompanyController@update')->name('compnay.update');
		Route::get('companies/destroy/{id}', 'Admin\CompanyController@destroy');
		//Holiday 
		Route::resource('holidays', 'Admin\HolidayController');
		Route::post('holidays/store', 'Admin\HolidayController@store')->name('holiday.store');
		Route::post('holidays/update', 'Admin\HolidayController@update')->name('holiday.update');
		Route::get('holidays/destroy/{id}', 'Admin\HolidayController@destroy');
		//House
		Route::resource('houses', 'Admin\HouseController');
		Route::post('houses/store', 'Admin\HouseController@store')->name('houses.store');
		Route::post('houses/update', 'Admin\HouseController@update')->name('houses.update');
		Route::get('houses/destroy/{id}', 'Admin\HouseController@destroy');
		//Vaccation
		Route::resource('vaccations', 'Admin\VaccationController');
		Route::post('vaccations/store', 'Admin\VaccationController@store')->name('vaccations.store');
		Route::post('vaccations/update', 'Admin\VaccationController@update')->name('vaccations.update');
		Route::get('vaccation/destroy', 'Admin\VaccationController@destroy');
		//Approve Vacc Hours
		Route::get('auser/vaccation/view', 'Admin\AdminController@vaccation_view');
		Route::get('approve/vaccation/hours', 'Admin\AdminController@approve_vchour');
		Route::get('user/vaccation/approve', 'Admin\AdminController@vacc_approve');
		Route::get('user/vaccation/decline', 'Admin\AdminController@vacc_decline');
		//Department Routes
		Route::resource('department', 'Admin\DepartmentController');
		Route::post('department/store', 'Admin\DepartmentController@store')->name('department.store');
		Route::post('department/update', 'Admin\DepartmentController@update')->name('department.update');
		Route::get('department/destroy/{id}', 'Admin\DepartmentController@destroy');
		//Notifictaions Routes
		Route::resource('notifications', 'Admin\EmpNotificationController');
		Route::post('notifications/store', 'Admin\EmpNotificationController@store')->name('notifications.store');
		Route::post('notifications/update', 'Admin\EmpNotificationController@update')->name('notifications.update');
		Route::get('notifications/destroy/{id}', 'Admin\EmpNotificationController@destroy');
		//Musers
		Route::resource('musers', 'Admin\UserManagerController');
		Route::post('musers/store', 'Admin\UserManagerController@store')->name('musers.store');
		Route::post('musers/update', 'Admin\UserManagerController@update')->name('musers.update');
		Route::get('musers/destroy/{id}', 'Admin\UserManagerController@destroy');
		//User timesheets
		Route::get('user/suser/{id}/{frm_dt}/{to_dt}', 'Admin\UserssaController@index');
		Route::get('user/suser/timesheets/{sv}/{id}/{frm_dt}/{to_dt}', 'Admin\UserssaController@timesheets');
		Route::get('user/suser/edit/timesheets/{id}', 'Admin\TimesheetssaController@edit');
		Route::post('user/suser/update/timesheets', 'Admin\TimesheetssaController@update');
		Route::get('user/suser/destroy/timesheets/{id}', 'Admin\TimesheetssaController@destroy');
		Route::get('user/suser/search/time', 'Admin\TimesheetssaController@srch_time');
		Route::get('user/suser/all/search', 'Admin\UserssaController@user_ssearch');
		Route::get('user/ssearch_user', 'Admin\UserssaController@search_suser_by_comp');
		Route::get('user/suser/export/timesheet/data/{id}', 'Admin\TimesheetssaController@export_data')->name('sasuser.export.time-sheet');
		Route::get('user/suser/export/timesheet/srchdata/{frmdate}/{todate}/{id}', 'Admin\TimesheetssaController@srcexport_data');
		Route::get('user/suser/search/payperiod', 'Admin\TimesheetssaController@search_payperiod');
		Route::get('user/suser/exportall/timesheet/{frmdate}/{todate}/{user}/{sasearch_by_comp}', 'Admin\TimesheetssaController@allexport_data');
		Route::get('user/suser/approve/timesheet', 'Admin\TimesheetssaController@approve_time');
		Route::get('user/suser/export/timesheet/company/{comp_id}', 'Admin\UserssaController@export_comp_data');
		Route::get('user/suser/decline/timesheet', 'Admin\TimesheetssaController@decline_time');
		Route::get('user/suser/delete/timesheet', 'Admin\TimesheetssaController@delete_time');
		//Admin reports
		Route::get('/all/users/vaccine/report', 'Admin\AdminController@all_users_vaccine_report');
		Route::get('user/vaccine_status/search', 'Admin\AdminController@vaccine_status');
		Route::get('/all/users/view', 'Admin\AdminController@all_users_view');
		Route::get('/all/users/id/view', 'Admin\AdminController@all_users_with_id_view');
		Route::get('/all/users/sign_signout/view', 'Admin\AdminController@sign_signout_view');
		Route::get('/all/users/all_app_timesheet/view', 'Admin\AdminController@all_app_timesheet_view');
		Route::get('/all/supervisor/users/view', 'Admin\AdminController@all_supervisor_assign_user_view');
		Route::get('/all/applicants/view', 'Admin\AdminController@all_applicants_view');
		Route::get('/all/new-applicants/view', 'Admin\AdminController@all_new_applicants_view');
		Route::get('/all/applicants-without_id/view', 'Admin\AdminController@all_applicants_without_id_view');
		Route::get('/all/user-lst_login_logout/view', 'Admin\AdminController@all_user_lst_login_logout_view');
		Route::get('/inactive/employees/view', 'Admin\AdminController@inactive_employees_view');
		//Admin reports
		Route::get('/all/users', 'Admin\AdminController@all_users');
		Route::get('/all/users/id', 'Admin\AdminController@all_users_with_id');
		Route::get('/all/users/sign_signout', 'Admin\AdminController@sign_signout');
		Route::get('/all/users/all_app_timesheet', 'Admin\AdminController@all_app_timesheet');
		Route::get('/all/users/all_app_timesheet/search', 'Admin\AdminController@all_app_search_timesheet');
		Route::get('/all/supervisor/users', 'Admin\AdminController@all_supervisor_assign_user');
		Route::get('/all/payperiod/search', 'Admin\AdminController@search_by_payperiod');
		Route::post('/all/payperiod/search/postdata', 'Admin\AdminController@post_data');
		Route::get('/all/applicants', 'Admin\AdminController@all_applicants');
		Route::get('/all/new-applicants', 'Admin\AdminController@all_new_applicants');
		Route::get('/all/new-applicants/month/{aap_month}/{aap_year}', 'Admin\AdminController@all_new_applicants_by_month');
		Route::get('/all/new-applicants/search', 'Admin\AdminController@search_all_new_applicants');
		Route::get('/all/applicants-without_id', 'Admin\AdminController@all_applicants_without_id');
		Route::get('/all/user-lst_login_logout', 'Admin\AdminController@all_user_lst_login_logout');
		Route::get('/inactive/employees', 'Admin\AdminController@inactive_employees');
		Route::get('/inactive/employees/search', 'Admin\AdminController@inactive_employees_search');
		Route::get('/inactive/employees/month/{from_month}/{to_month}', 'Admin\AdminController@inactive_employees_by_month');
		Route::get('/userss/staff-list/{id}', 'Admin\AdminController@staff_list');
		Route::get('/userss/payroll-file/{payperiod}/{cid}', 'Admin\AdminController@payroll_file');
		//Payroll
		Route::get('user/payroll/report', 'Admin\AdminController@payrollreport');
		Route::get('user/payroll/search/postdata/{payperiod}/{search_by_comp}', 'Admin\AdminController@post_ddata');
		Route::get('user/payroll/search/hpostdata/{payperiod}/{search_by_comp}', 'Admin\AdminController@hpost_ddata');
		Route::get('user/payroll/search/csvpostdata/{payperiod}/{search_by_comp}', 'Admin\AdminController@ecsvpost_ddata');
		/*Route::get('user/payroll/search/csvpostdata/{payperiod}/{search_by_comp}', 'Admin\AdminController@csv_post_ddata');*/
		Route::get('/search/payroll', 'Admin\AdminController@serach_payroll');
		Route::get('/test/route', 'Admin\AdminController@test_route');
		Route::get('user/finace/report', 'Admin\AdminController@finacereport');
	});
	//Supervisor Routes
	Route::group(['middleware' => 'supervisor'], function () {
		//Dashboard
		Route::get('supervisor-dashboard', 'Supervisor\SupervisorController@index')->name('supervisor.dashboard');
		Route::get('/supervisor-logout', 'Supervisor\SupervisorController@logout')->name('supervisor.logout');
		//Edit profile
		Route::get('/supervisor/edit-profile', 'Supervisor\SupervisorController@edit_profile');
		Route::post('/supervisor/update-profile', 'Supervisor\SupervisorController@update_profile');
		//Reset Password
		Route::get('/supervisor/change-password', 'Supervisor\SupervisorController@resetpassword');
		Route::post('/supervisor/profile/updatepassword', 'Supervisor\SupervisorController@updatepassword');
		//List issues
		Route::resource('susers/list-issues', 'Supervisor\ListProblemsController');
		Route::get('susers/list-issues/create', 'Supervisor\ListProblemsController@create');
		Route::post('susers/list-issues/store', 'Supervisor\ListProblemsController@store');
		Route::get('susers/list-issues/edit/{id}', 'Supervisor\ListProblemsController@edit');
		Route::post('susers/list-issues/update', 'Supervisor\ListProblemsController@update')->name('susers.list-issues.update');
		Route::get('susers/list-issues/approve/{id}', 'Supervisor\ListProblemsController@approve')->name('supervisor.approve');
		Route::get('susers/list-issues/decline/{id}', 'Supervisor\ListProblemsController@decline')->name('supervisor.decline');
		//Route::resource('susers', 'Supervisor\UserssController');
		Route::get('susers/{frm_dt}/{to_dt}', 'Supervisor\UserssController@index');
		Route::get('susers/time/{frm_dt}/{to_dt}', 'Supervisor\UserssController@time_index');
		Route::get('susers/{frm_dt}/{to_dt}/{ssearch_by_comp}', 'Supervisor\UserssController@user_with_com');
		Route::get('suser/timesheets/{id}/{frm_dt}/{to_dt}', 'Supervisor\UserssController@timesheets');
		Route::get('suser/timesheets/{id}/{frm_dt}/{to_dt}/{ssearch_by_comp}', 'Supervisor\UserssController@timesheets_with_com');
		Route::get('suser/edit/timesheets/{id}', 'Supervisor\TimesheetssController@edit');
		Route::post('suser/update/timesheets', 'Supervisor\TimesheetssController@update');
		Route::get('suser/destroy/timesheets', 'Supervisor\TimesheetssController@destroy');
		Route::get('suser/search/time', 'Supervisor\TimesheetssController@srch_time');
		Route::get('suser/nsearch/time', 'Supervisor\TimesheetssController@nsrch_time');
		Route::any('suser/search', 'Supervisor\UserssController@user_msearch');
		Route::get('suser/nsearch', 'Supervisor\UserssController@user_nmsearch');
		Route::any('suser/searchs', 'Supervisor\UserssController@suser_searchs');
		Route::get('suser/export/timesheet/data/{id}', 'Supervisor\TimesheetssController@export_data')->name('suser.export.time-sheet');
		Route::get('suser/export/timesheet/srchdata/{frmdate}/{todate}/{id}', 'Supervisor\TimesheetssController@srcexport_data');
		Route::get('suser/search/payperiod', 'Supervisor\TimesheetssController@search_payperiod');
		Route::get('suser/nsearch/payperiod', 'Supervisor\TimesheetssController@nsearch_payperiod');
		Route::get('suser/exportall/timesheet/{frmdate}/{todate}/{ssearch_by_comp}', 'Supervisor\TimesheetssController@allexport_data');
		Route::get('suser/approve/timesheet', 'Supervisor\TimesheetssController@approve_time');
		Route::get('suser/decline/timesheet', 'Supervisor\TimesheetssController@decline_time');
		Route::get('suser/delete/timesheet', 'Supervisor\TimesheetssController@delete_time');
		Route::get('suser/ntapprove/timesheet', 'Supervisor\TimesheetssController@ntapprove_time');
		Route::get('suser/ntdecline/timesheet', 'Supervisor\TimesheetssController@ntdecline_time');
		Route::get('suser/ntdelete/timesheet', 'Supervisor\TimesheetssController@ntdelete_time');
		Route::get('suser/ntview/timesheet', 'Supervisor\TimesheetssController@ntview_time');
		Route::get('suser/billing/report', 'Supervisor\UserssController@billingreport');
		Route::get('suser/export/billing/report', 'Supervisor\UserssController@exort_user');
		Route::get('suser/payroll/report', 'Supervisor\SupervisorController@payrollreport');
		Route::get('suser/ssearch_user', 'Supervisor\UserssController@search_suser_by_comp');
		Route::get('suser/export/timesheet/company/{comp_id}', 'Supervisor\UserssController@export_comp_data');
		Route::get('suser/search/supayperiod', 'Supervisor\TimesheetssController@susearch_payperiod');
		Route::get('suser/atime/all', 'Supervisor\UserssController@utimesheets');
		//Admin reports view
		Route::get('/all/suser/view', 'Supervisor\SupervisorController@all_users_view');
		Route::get('/all/suser/timesheet','Supervisor\SupervisorController@timesheet');
		Route::get('/all/suser/sign_signout/view', 'Supervisor\SupervisorController@sign_signout_view');
		//Approve Vacc Hours
		Route::get('suser/approve/vaccation/hours', 'Supervisor\SupervisorController@approve_vchour');
		Route::get('suser/vaccation/view', 'Supervisor\SupervisorController@vaccation_view');
		Route::get('suser/vaccation/approve', 'Supervisor\SupervisorController@vacc_approve');
		Route::get('suser/vaccation/decline', 'Supervisor\SupervisorController@vacc_decline');
		//Admin Reports
		Route::get('/all/suser', 'Supervisor\SupervisorController@all_users');
		Route::get('/all/suser/sign_signout', 'Supervisor\SupervisorController@sign_signout');
		Route::get('/all/suser/payperiod/search', 'Supervisor\SupervisorController@search_by_payperiod');
		Route::post('/all/suser/payperiod/search/postdata', 'Supervisor\SupervisorController@post_data');
		Route::post('/billing/payperiod/search/postdata', 'Supervisor\SupervisorController@post_ddata');
		//Payroll
		Route::get('suser/payroll/search/postdata/{payperiod}/{search_by_comp}', 'Supervisor\SupervisorController@post_ddata');
		Route::get('suser/search/payroll', 'Supervisor\SupervisorController@serach_payroll');
	});
	//User Routes
	Route::group(['middleware' => 'user'], function () {
		//User dashboard
		Route::get('user-dashboard', 'User\UserController@index')->name('user.dashboard');
		Route::get('user-logout', 'User\UserController@logout')->name('user.logout');
		//Reset password
		Route::get('/change-password', 'User\UserController@resetpassword');
		Route::post('/my/profile/updatepassword', 'User\UserController@updatepassword');
		//List issues
		Route::resource('list-issue', 'User\ListProblemController');
		Route::post('list-issue/store', 'User\ListProblemController@store')->name('list-issue.store');
		Route::post('list-issue/update', 'User\ListProblemController@update')->name('list-issue.update');
		//TimeSheet
		Route::resource('time-sheets', 'User\TimesheetController');
		Route::post('time-sheets/store', 'User\TimesheetController@store')->name('time-sheets.store');
		Route::post('time-sheets/update', 'User\TimesheetController@update')->name('time-sheets.update');
		Route::get('time-sheets/destroy', 'User\TimesheetController@destroy');
		Route::get('search/timesheets', 'User\TimesheetController@srch_time_view');
		Route::get('search/time', 'User\TimesheetController@srch_time');
		Route::get('export/timesheet/data', 'User\TimesheetController@export_data')->name('export.time-sheet');
		Route::get('export/timesheet/srchdata/{frmdate}/{todate}', 'User\TimesheetController@srcexport_data');
		Route::get('house/compnay/{company_id}', 'User\TimesheetController@house_com');
		//Driving license
		Route::get('/my/driving-license/upload', 'User\UserController@uploaddl');
		Route::post('/my/driving-license/submit', 'User\UserController@submitdl');
		//Covid report
		Route::get('/my/covid-report/upload', 'User\UserController@uploacor');
		Route::post('/my/covid-report/submit', 'User\UserController@submitcor');
		Route::get('/my/covid-report/update_ne', 'User\UserController@update_ncor');
		//Edit profile
		Route::get('/edit-profile', 'User\UserController@nameedit');
		Route::post('/my/name/update', 'User\UserController@nameupdate');
		//Get Hours
		Route::get('get/totalhours/{time_in}/{time_out}','User\TimesheetController@totalhours');
		//Vaccations
		Route::resource('enter-vaccation', 'User\VaccationController');
		Route::post('enter-vaccation/store', 'User\VaccationController@store')->name('enter-vaccation.store');
		Route::post('enter-vaccation/update', 'User\VaccationController@update')->name('enter-vaccation.update');
		Route::get('enter-vaccation/destroy/{id}', 'User\VaccationController@destroy');
	});
	//CaseManager Routes
	Route::group(['middleware' => 'casemanager'], function () {
		//Dashboard
		Route::get('casemanager-dashboard', 'CaseManager\CaseManagerController@index')->name('casemanager.dashboard');
		Route::get('/casemanager-logout', 'CaseManager\CaseManagerController@logout')->name('casemanager.logout');
		//Reset password
		Route::get('/casemanager/profile/resetpassword', 'CaseManager\CaseManagerController@resetpassword');
		Route::post('/casemanager/profile/updatepassword', 'CaseManager\CaseManagerController@updatepassword');
		//Case manager
		Route::resource('cmusers', 'CaseManager\UsermController');
		Route::get('cmuser/timesheets/{id}', 'CaseManager\UsermController@timesheets');
		Route::get('cmuser/timesheets/{id}/{frm_dt}/{to_dt}', 'CaseManager\UsermController@timesheets_wt_dates');
		//Route::get('cmuser/create/timesheets/{id}', 'CaseManager\TimesheetmController@create');
		//Route::post('cmuser/store/timesheets', 'CaseManager\TimesheetmController@store');
		Route::get('cmuser/edit/timesheets/{id}/{frm_dt}/{to_dt}', 'CaseManager\TimesheetmController@edit');
		Route::post('cmuser/update/timesheets', 'CaseManager\TimesheetmController@update');
		Route::get('cmuser/approve/timesheet', 'CaseManager\TimesheetmController@approve_time');
		Route::get('cmuser/decline/timesheet', 'CaseManager\TimesheetmController@decline_time');
		Route::get('cmuser/delete/timesheet', 'CaseManager\TimesheetmController@delete_time');
		Route::get('cmuser/destroy/timesheets/{id}', 'CaseManager\TimesheetmController@destroy');
		Route::get('cmuser/search/time', 'CaseManager\TimesheetmController@srch_time');
		//Route::get('cmuser/search', 'CaseManager\UsermController@user_msearch');
		//Route::get('cmuser/export/timesheet/data/{id}', 'CaseManager\TimesheetmController@export_data')->name('muser.export.time-sheet');
		//Route::get('cmuser/export/timesheet/srchdata/{frmdate}/{todate}/{id}', 'CaseManager\TimesheetmController@srcexport_data');	
	});
});

