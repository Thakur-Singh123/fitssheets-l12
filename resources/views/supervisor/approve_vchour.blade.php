@extends('layouts.supervisor')
@section('content') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php use App\Http\Controllers\Supervisor\SupervisorController; ?>
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
.custom-modal{
  width:850px !important;
  max-width:850px;
  margin:35px auto;
}
.modal-content{
  border:none;
  border-radius:15px;
  overflow:hidden;
  box-shadow:0 15px 40px rgba(0,0,0,.15);
}
.custom-header{
  background:#000;
  border-bottom:1px solid #ececec;
  padding:18px 25px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.custom-header .modal-title {
  color: #cbcbb1;
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}
.custom-header .modal-title i{
  color:#0d6efd;
  margin-right:8px;
}
.custom-close {
  background: none;
  border: none;
  color: #f30f2517 !important;
  font-size: 30px;
  font-weight: bold;
  opacity: 1;
  line-height: 1;
  cursor: pointer;
}
.modal-body {
  background:#fafafa;
  padding:25px 30px;
}
.custom-footer {
  border-top:1px solid #ececec;
  background:#fff;
  padding:15px 25px;
}
.custom-footer .btn {
  min-width: 30px;
  border-radius: 8px;
  font-weight: 600;
}
.btns-primary {
  color: #fff;
  background-color: #6c757d;
  border-color: #6c757d;
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
          <h4 class="card-title">All Vaccations</h4>
          <div class="table-responsive">
            <table id="sortable-table-1" class="table table-striped">
                <thead>
                  <tr>
                    <th class="sortStyle unsortStyle">Sr. No<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">User<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">From<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">To<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">Reporting<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">Created<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">Action<i class="mdi mdi-chevron-down"></i> </th>
                    <th class="sortStyle unsortStyle">Status<i class="mdi mdi-chevron-down"></i> </th>
                  </tr>
                </thead>
                <tbody id="result">
                  @if($approve_vchour->count() != 0)
                  @foreach ($approve_vchour as $datas)
                  <tr>
                      <td>{{ $approve_vchour->firstItem() + $loop->index }}.</td>
                      <td><?php $user_info = SupervisorController::user_info($datas->user_id); 
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
                              <a data-toggle="modal" data-target="#myModal" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" data-uaid="{{ Auth::user()->id }}" id="svacc_view" name="svacc_view"> <img src="https://fitssheets.com/assets/images/view.png"></a>
                            </li>
                            <li><a data-uaid="{{ Auth::user()->id }}" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" id="svacc_aaprove" name="svacc_aaprove"><img src="https://fitssheets.com/assets/images/check.png"></a></li>
                            <li><a data-uaid="{{ Auth::user()->id }}" data-uid="<?php echo $datas->id; ?>"  data-baseurl="{{ url('/') }}" id="svacc_decline" name="svacc_decline"><img src="https://fitssheets.com/assets/images/decline.png"></a></li>
                        </ul>
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
                  @endforeach
                  @else
                  <tr>
                      <td colspan="8" class="no-data">
                        Sorry, No data found!
                      </td>
                  </tr>
                  @endif
                </tbody>
            </table>
            <div class="mt-3">
                {{ $approve_vchour->links('pagination::bootstrap-5') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="loding"></div>
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg custom-modal">
      <div class="modal-content">
        <div class="modal-header custom-header">
          <h4 class="modal-title">
            Vacation Details
          </h4>
          <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer custom-footer">
          <button type="button" class="btn btns-primary px-4" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection