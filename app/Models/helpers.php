<?php
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\AdminMeta;
use App\Models\Company;
use App\Models\Department;
use App\Models\UserManager;
// use DateTime;
use App\Models\Payperiods;

if (! function_exists('payperiods')) {
    function payperiods()
    {

		$payperiods = Payperiods::orderBy('created_at', 'DESC')->get();
		$TodayDate = new DateTime();		
		if(isset($payperiods)){
			foreach($payperiods as $payperiod){
				$bet_dates = explode('-',$payperiod->payperiod_value);
				if(isset($bet_dates)){
					$from_date    = $bet_dates[0];
					$to_date    = $bet_dates[1];
				}
				$xto_date = explode('_',$to_date);
				$xto_date = implode('-',$xto_date);
				$xfrom_date = explode('_',$from_date);
				$xfrom_date = implode('-',$xfrom_date);
				
				$xto_date = new DateTime($xto_date);
				$xfrom_date  = new DateTime($xfrom_date);
				
				// echo $TodayDate->getTimestamp();
				// echo "---";
				// echo $TodayDate->format('m/d/Y');
				// echo "---";
				// echo $xfrom_date->getTimestamp();
				// echo "---";
				// echo $xfrom_date->format('m/d/Y');
				// echo "---";
				// echo $xto_date->getTimestamp();
				// echo "---";
				// echo $xto_date->format('m/d/Y');
				// echo "---";
				// echo "<br>";
				 if (
				  $TodayDate->format('y-m-d') >= $xfrom_date->format('y-m-d') && 
				  $TodayDate->format('y-m-d') <= $xto_date->format('y-m-d')){
				  $frm_date  = $xfrom_date->format('Y-m-d');
				  $t_date = $xto_date->format('Y-m-d');
				  $sfrm_date  = $xfrom_date->format('Y_m_d');
				  $st_date = $xto_date->format('Y_m_d');
				}
			}
			
		}
// 		$origin = new DateTime('2020-12-21');
//         $interval = $origin->diff($TodayDate);
//         $date_diff =  $interval->format('%a');
//         if($date_diff == 0){
//             	$frm_date = "2020_12_21";
// 		        $t_date = "2021_01_03";
//         }
		
		// echo $frm_date;
				// echo $t_date;
		// die;
		$arr_dates = array();
		if(!empty($frm_date) && !empty($t_date)){
			$arr_dates[] = array('frm_date' => $frm_date, 't_date' => $t_date);
			// $frm_date  = $frm_date;
			// $t_date = $t_date;
		}
		else{
			$arr_dates[] = array('frm_date' => "", 't_date' => "");
			// $frm_date  = "";
			// $t_date = "";
		}
		if(!empty($sfrm_date) && !empty($st_date)){
			$arr_dates[] = array('sfrm_date' => $sfrm_date, 'st_date' => $st_date);
			// $sfrm_date  = $sfrm_date;
			// $st_date = $st_date;
		}
		else{
			$arr_dates[] = array('sfrm_date' => "", 'st_date' => "");
			// $sfrm_date  = "";
			// $st_date = "";
		}

        return $arr_dates;
    }
	
	
	function paychecks($c_id)
    {
    	$last_pay_id = 13;
		if($c_id != 0){
			$payperiods = Payperiods::where("companies_id", "=", $c_id)->orderBy('created_at', 'DESC')->get();
		}else{
			$payperiods = Payperiods::orderBy('created_at', 'DESC')->get();
		}
		
		$TodayDate = new DateTime();		
		if(isset($payperiods)){
			foreach($payperiods as $payperiod){
				$bet_dates = explode('-',$payperiod->payperiod_value);
				if(isset($bet_dates)){
					$from_date    = $bet_dates[0];
					$to_date    = $bet_dates[1];
				}
				$xto_date = explode('_',$to_date);
				$xto_date = implode('-',$xto_date);
				$xfrom_date = explode('_',$from_date);
				$xfrom_date = implode('-',$xfrom_date);
				
				$xto_date = new DateTime($xto_date);
				$xfrom_date  = new DateTime($xfrom_date);
				
				// echo $TodayDate->getTimestamp();
				// echo "---";
				// echo $TodayDate->format('m/d/Y');
				// echo "---";
				// echo $xfrom_date->getTimestamp();
				// echo "---";
				// echo $xfrom_date->format('m/d/Y');
				// echo "---";
				// echo $xto_date->getTimestamp();
				// echo "---";
				// echo $xto_date->format('m/d/Y');
				// echo "---";
				// echo "<br>";
				 if (
				  $TodayDate->format('y-m-d') >= $xfrom_date->format('y-m-d') && 
				  $TodayDate->format('y-m-d') <= $xto_date->format('y-m-d')){
				  // $frm_date  = $xfrom_date->format('Y-m-d');
				  // $t_date = $xto_date->format('Y-m-d');
				  // $sfrm_date  = $xfrom_date->format('Y_m_d');
				  // $st_date = $xto_date->format('Y_m_d');
				  
				  $last_pay_id = $payperiod->id;
				}
			}
			
		}

		// echo $last_pay_id;
		// die;
		// if($last_pay_id == 0 ){
		// 	$last_pay_id = 1;
		// }else{
		// 	if($last_pay_id == 13 ){
		// 		$last_pay_id = 13;
		// 	}elseif($last_pay_id == 15){
		// 		$last_pay_id = 15;
		// 	}
		// }
		$last_payperiods = Payperiods::where('id', '=', $last_pay_id)->orderBy('created_at', 'DESC')->first();
		$las_bet_dates =explode('-',$last_payperiods->payperiod_value);
		if(isset($las_bet_dates)){
			$from_date    = $las_bet_dates[0];
			$to_date    = $las_bet_dates[1];
			$xto_date = explode('_',$to_date);
			$xto_date = implode('-',$xto_date);
			$xfrom_date = explode('_',$from_date);
			$xfrom_date = implode('-',$xfrom_date);
		}
		$arr_dates = array();
		if(!empty($from_date) && !empty($to_date) && !empty($xfrom_date) && !empty($xto_date) && !empty($last_payperiods->payperiod)){

			$arr_dates[] = array('frm_date' => $from_date, 't_date' => $to_date,'xfrm_date' => $xfrom_date, 'xt_date' => $xto_date, 'payperiod' => $last_payperiods->payperiod);
		}
		else{
			$arr_dates[] = array('frm_date' => "", 't_date' => "",'xfrm_date' => "", 'xt_date' => "", 'payperiod' => "");
		}
		return $arr_dates;

    }
}