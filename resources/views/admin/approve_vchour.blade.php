@extends('layouts.master')
@section('content') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php use App\Http\Controllers\Admin\AdminController; ?>
<style>
td {
    text-align: center;
}
ul.imp_actions {
    display: flex;
    list-style: none;
    gap: 10px;
}
ul.imp_actions a {
    cursor: pointer;
}
.img-ss, .table td img:not(.thumb-image), .table th img:not(.thumb-image) {
    width: 25px !important;
    min-width: 25px !important;
    height: 25px !important;
}
</style>
<div class="content-wrapper">
@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
    <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        


      <table id="sortable-table-1" class="table dataTable table-striped table-responsive">
        <thead>
        <tr>
          <th> # </th>
          <th>User</th>
           <th> From</th>
           <th> To </th>
            <th> Reporting </th>
          <th> Created </th>
           <th>Actions</th>
           <th>Status</th>
        </tr>
        
        </thead>
        <tbody id="result">
        <?php $count = 1; ?>
        @if($approve_vchour->count() != 0)
        @foreach ($approve_vchour as $datas)
          <tr>
            <td><?php echo $count; ?></td>
             <td><?php $user_info = AdminController::user_info($datas->user_id); 
              echo $user_info->name;
          ?></td>
           <td>

            <?php $vacc_frm    = explode('_', $datas->vacc_start);
                $vacc_frm = implode("-", $vacc_frm); 
                  echo date('M d, Y', strtotime($vacc_frm));
            ?>
            </td>
           <td><?php $vacc_end    = explode('_', $datas->vacc_end);
                $vacc_end = implode("-", $vacc_end); 
                  echo date('M d, Y', strtotime($vacc_end));
            ?></td>
           <td><?php $vacc_rbu    = explode('_', $datas->vacc_rbu);
                $vacc_rbu = implode("-", $vacc_rbu); 
                  echo date('M d, Y', strtotime($vacc_rbu));
            ?></td>
             <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
             <td>
              <ul class="imp_actions">
              
                <li>
                <a data-toggle="modal" data-target="#myModal" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" data-uaid="{{ Auth::user()->id }}" id="vacc_view" name="vacc_view"> <img src="{{ url('/') }}/public/assets/images/view.png"></a>
                </li>
                <li><a data-uaid="{{ Auth::user()->id }}" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" id="vacc_aaprove" name="vacc_aaprove"><img src="{{ url('/') }}/public/assets/images/check.png"></a></li>
                <li><a data-uaid="{{ Auth::user()->id }}" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" id="vacc_decline" name="vacc_decline"><img src="{{ url('/') }}/public/assets/images/decline.png"></a></li>
              </ul>

            </td>
              
            <td>
            <a  style="display:none" href="{{ route('enter-vaccation.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
            <a style="margin-left: 5px;display:none;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_vaccations" title="Delete"><i class="fa fa-trash-o"></i></a>
            </td>
            <td>
              <?php if($datas->vacc_status==0){echo "<p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p>";}elseif($datas->vacc_status==1){echo "<p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: green;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Approved</p>";}elseif($datas->vacc_status==2){echo "<p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: red;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Decline</p>";}else{echo "<p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p>";} ?>
            </td>
          </tr>
        <?php $count++; ?>
        @endforeach
        
        @else
          <p>Sorry No Data!!</p>
        @endif
        </tbody>
      </table>
      <br>

      </div>
    </div>
    </div>
  </div>
  <div class="loding"></div>
   <div class="modal fade" id="myModal" role="dialog">
    <div style="max-width: 95%;" class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div style="    display: flex;
    flex-flow: column;" class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Vaccation Info</h4>
        </div>
        <div class="modal-body">
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
          </div>

@endsection