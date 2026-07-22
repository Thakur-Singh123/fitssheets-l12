<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\EmpNotificationRel;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\UserManager;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    public function boot(): void
    {
        URL::forceScheme('https');
		
        //Notification Siderbar
		view()->composer(['user.partials.notification'], function($view) {
			$user_id = Auth::user()->id;
			$data = EmpNotificationRel::with('emp_notfications')->with('users')->where('users_id', '=', $user_id)->get();
            $view->with(compact('data'));
        });


        view()->composer(['user.partials.sidebar'], function($view) {
            $user_id = Auth::user()->id;
            $user_id = Auth::user()->id;
            // $status = Auth::user()->status;
            // if($status == 0){
                // Auth::logout();
          
                // return redirect('/login')->with('status','Your account is not activated, contact to admin');
            // }else{
            $dt = Carbon::now();
            $current_date_time = $dt->toDateString();
            $date    = explode('-', $current_date_time);
            $date = implode("_", $date);
            
            $companies = UserManager::where('musers_id', '=', $user_id)->get();
            $company_id = array();
            if(isset($companies)){
                foreach($companies as $company){
                    $company_id[] = $company->users_id;
                }
            }
            
            if(isset($c_id)){
                $c_id = $company_id[0];
            }else{
                $c_id = 0;
            }
            
            $payperiods_dates = paychecks($c_id);
            
            if(isset($payperiods_dates)){
                 $frm_date  = $payperiods_dates[0]['frm_date'];
                 $t_date = $payperiods_dates[0]['t_date'];
                  $xfrm_date  = $payperiods_dates[0]['xfrm_date'];
                 $xt_date = $payperiods_dates[0]['xt_date'];
            }else{
                $frm_date  = "";
                $t_date = "";
                 $xfrm_date  = "";
                 $xt_date = "";
            }
            if(!empty($frm_dt) && !empty($to_dt)){
                $frm_date = $frm_dt;
                $t_date = $to_dt;
            }
            
            if($frm_date && $t_date){
                $from_date    = explode('-', $frm_date);
                $from_date = implode("_", $from_date);
                $to_date    = explode('-', $t_date);
                $to_date = implode("_", $to_date);
            }
            $last_payperiod = $payperiods_dates[0]['payperiod'];
            $data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', '=', $date)->where('users_id', $user_id)->orderBy('created_at', 'DESC')->get();
            
            
            $last_pay = TimeSheet::with('companies')
                                    ->with('users')
                                    ->with('houses')
                                    ->where('users_id', '=', $user_id)
                                    ->whereBetween('hours_day', array($from_date, $to_date))
                                    ->orderBy('created_at', 'DESC')
                                    ->sum('hours_wrk');
            if($xfrm_date && $xt_date){
                $paydate = date('M d, Y', strtotime($xt_date. ' + 5 days'));
            }
                                    
            $user = User::where('id', '=', $user_id)->first(); 
            $hourley_rate = $user->hourst_rate;
            $total_pay = $last_pay * $hourley_rate;
             $pay_work = "";
            $view->with(compact('data','current_date_time','last_pay','last_payperiod','hourley_rate','total_pay','paydate','pay_work'));
        });
    }
}
