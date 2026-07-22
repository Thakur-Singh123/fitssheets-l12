@extends('layouts.supervisor')
@section('content') 
<?php
   use App\Http\Controllers\Supervisor\UserssController;
   use App\Models\User;
   use App\Models\UserManager;
   use App\Models\Company;
   ?>
<div class="content-wrapper">
   @if(\Session::has('success'))
   <div class="alert alert-success">
      <h4>{{\Session::get('success')}}</h4>
   </div>
   @endif
   <?php /*echo'hiii'; die();*/?>
   <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
         <div class="card">
            <div class="card-body">
               <table class="table table-striped table-responsive">
                  <thead>
                     <tr>
                        <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
                        <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
                        <th> Actions </th>
                        <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
                        <th class="sortStyle unsortStyle"> Supervisor<i class="mdi mdi-chevron-down"></i> </th>
                        <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
                        <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
                        <th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
                        <th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
                        <th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i></th>
                        <th> Status </th>
                        <th class="sortStyle unsortStyle"> Total Hours<i class="mdi mdi-chevron-down"></i></th>
                        <th class="sortStyle unsortStyle"> Approved Hours<i class="mdi mdi-chevron-down"></i></th>
                        <th class="sortStyle unsortStyle"> Declined Hours<i class="mdi mdi-chevron-down"></i></th>
                     </tr>
                  </thead>
                  <tbody id="srch_result">
                     <?php $count = 1; ?>
                     @if(isset($data))
                     @if($data->count() != 0)
                     @foreach ($data as $datas)
                     <td><?php echo $count; ?></td>
                     <td><?php echo $datas->emp_id; ?></td>
                     <td>
                        <?php if(isset($ssearch_by_comp)){ ?>
                        <a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}/{{ $ssearch_by_comp }}" title="Time Sheets"><i class="fa fa-book"></i></a>
                        <?php }else{ ?>
                        <a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}" title="Time Sheets"><i class="fa fa-book"></i></a>
                        <?php } ?>
                     </td>
                     <td> {{ $datas->last_name  }} {{ $datas->first_name  }}</td>
                     <td> {{ $user_f_name  }} </td>
                     <td><?php echo $datas->dept; ?></td>
                     <td><?php
                        $user_companies = UserssController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
                     <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
                     <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
                     <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
                     <td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>
                     <td><?php $total_time = UserssController::total_time($datas->id,$frm_date,$t_date); 
                        // echo $datas->id;
                         // echo $frm_date;
                        // echo $t_date;
                        // echo $total_time;
                        if($total_time <=  79){
                        echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
                        }elseif($total_time ==  80){
                        echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
                        }else{
                         echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
                        }
                        
                        ?></td>
                     <td><?php $approved_time = UserssController::approved_time($datas->id,$frm_date,$t_date); 
                        if($approved_time <=  79){
                        	echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
                        }elseif($approved_time ==  80){
                        	echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
                        }else{
                          echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
                        }
                         
                         ?></td>
                     <td><?php $denied_time = UserssController::denied_time($datas->id,$frm_date,$t_date); 
                        if($denied_time <=  80){
                        		echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
                        }elseif($denied_time ==  80){
                        		echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
                        }else{
                           echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
                        }
                        ?></td>
                     </tr>
                     <?php $count++; ?>
                     @endforeach
                     @endif
                     @else
                     <p>Sorry No Data!!</p>
                     @endif
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="loding"></div>
</div>
@endsection