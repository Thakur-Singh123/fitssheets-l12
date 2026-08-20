@extends('layouts.master')
<?php use App\Http\Controllers\Admin\ListsProblemController;?>
@section('content')
<style>
.notes_list a.btn.btn-success, .notes_list a.btn.btn-danger {
    background: none !important;
    border: none !important;
    padding: 0;
}
.notes_l h4 {
	margin: 0;
	font-size: 16px;
	font-weight: 500;
	padding-bottom: 2px;
}
.notes_l {
    padding: 6px 0px;
}
.notes_list {
    display: flex;
    width: 100%;
    justify-content: flex-start;
    align-items: center;
    padding-top: 5px;
    gap: 5px;
}
.round {
	position: relative;
	width: 22px;
}
.notes_list p {
	margin: 0;
}
.round label {
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 50%;
  cursor: pointer;
  height: 22px;
  left: 0;
  position: absolute;
  top: 0;
  width: 22px;
}
.round label:after {
 border: 2px solid #fff;
    border-top: none;
    border-right: none;
    content: "";
    height: 5px;
    left: 5px;
    opacity: 0;
    position: absolute;
    top: 6px;
    transform: rotate(-45deg);
    width: 10px;
}
.round input[type="checkbox"] {
  visibility: hidden;
}
.round .btn-danger label {
  background-color: #66bb6a;
  border-color: #66bb6a;
}
.round .btn-danger label:after {
  opacity: 1;
}
</style>
<div class="content-wrapper">
    @if(session('success'))
    <div class="alert alert-success">
        <h4>{{ session('success') }}</h4>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
        <h4>{{ session('error') }}</h4>
    </div>
    @endif
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Issues</h4>
					{{-- <p class="card-description"><a href="{{ route('lists-issue.create') }}"> Add Note <i class="fa fa-plus-circle"></i></a> </p> --}}
					<div class="d-flex justify-content-end align-items-center mb-3">
						<a href="{{ route('lists-issue.create') }}" class="btn btn-primarys" style="background-color:#1c45ef; color: white; border-radius: 6px; padding: 10px 15px; font-size: 14px;">
							<i class="fa fa-plus mr-1"></i>
							Add Issue
						</a>
					</div>
				<?php if(isset($issue_arr)){ 
					foreach ($issue_arr as $key => $value) {
						foreach ($value as $keyy => $valuee) {
							$keyy = explode('_',$keyy);
							$keyy = implode('-',$keyy);
							echo "<div class='notes_l'><h4>".date('M d Y',strtotime($keyy))."</h4>";
							foreach($valuee as $valuees){
								echo '<div class="notes_list">';
								echo '<div class="round">';
								if($valuees['status'] == "0") {
									echo '<a href="'.route('admin.approve', $valuees["id"]).'" class="btn btn-success approve-btn">';
								} else {
									echo '<a href="'.route('admin.decline', $valuees["id"]).'" class="btn btn-danger decline-btn">';
								}
									echo '<label for="checkbox-'.$valuees["id"].'"></label>';
									echo '</a>';
									echo '</div>';
								if($valuees['issue'] != null) {
									echo '<p >';
									if($valuees['name'] != null) { 
										echo $valuees['name'];
									} 
									if($valuees['ssn'] != null) { 
										echo ' / '. substr($valuees['ssn'], -4);
									} 
									if($valuees['company'] != null) { 
										echo ' / '. ListsProblemController::company($valuees['company']);
									} 
									echo ' / '.$valuees['issue'];
									echo '</p>';
								}
								if($valuees['resolution'] != null) {
									echo '<p style="color:purple">';
									echo '-> <b>(Resolution : </b><b style="color:green">'.$valuees['resolution'].')</b>';
									echo '</p>';
								}
									echo '<a  href="'.route('lists-issue.edit', $valuees["id"]).'" title="Edit"><i class="fa fa-pencil"></i></a>';
									//echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-ID="'.$valuees["id"].'" class="delete_issue" title="Delete"><i class="fa fa-trash-o"></i></a>';
									echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-issue_id="'.$valuees["id"].'" class="is_delete_issue" title="Delete"><i class="fa fa-trash-o"></i></a>';
									echo '</div>';
							}
							        echo '</div>';
						}
					}
				}?>
				{{ $data->links('pagination::bootstrap-5') }}			
				<br>
				</div>
			</div>
		</div>
	</div>
<div class="loding"></div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--sweetalert2 script-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
$(function () {
    $('body').on('click', '.approve-btn', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
		Swal.fire({
			title: 'Are you sure?',
			text: 'You want to approve this issue!',
			icon: 'success',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, Approve it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = url;
			}
		});
	});

    $('body').on('click', '.decline-btn', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
			title: 'Are you sure?',
			text: 'You want to decline this issue!',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#f25961',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, Decline it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = url;
			}
		});
    });
});
</script>
@endsection		
