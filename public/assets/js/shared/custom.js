(function($) {
  'use strict';
  $(function() {
    if ($('#sortable-table-1').length) {
      $('#sortable-table-1').tablesort();
    }
    if ($('#sortable-table-2').length) {
      $('#sortable-table-2').tablesort();
    }
  });
  
})(jQuery);



jQuery(document).ready(function(){
	jQuery('.alert-success').delay(2000).fadeOut('slow');
	 var fp = flatpickr("#hours_day", {
			dateFormat: "Y-m-d",
			maxDate: "today",
    })
		 var fp = flatpickr("#frm_dt", {
			dateFormat: "Y-m-d",
			maxDate: "today",
    })
		 var fp = flatpickr("#to_dt", {
			dateFormat: "Y-m-d",
			maxDate: "today",
    })
	 var fp = flatpickr("#vfrm_dt", {
			dateFormat: "Y-m-d",
    })
		 var fp = flatpickr("#vto_dt", {
			dateFormat: "Y-m-d",
    })
	 var fp = flatpickr("#report_by", {
			dateFormat: "Y-m-d",
    })
	$('#time_in').timepicker();
    $('#datatable').DataTable();
	$("#.modal").addClass("show");
	 

	
});
$(document).ready(function() {
    $('#daterange').datepicker({ format: "yyyy/mm/dd" });
}); 
 


$(document).on('change','#vto_dt', function(e){
	e.preventDefault();
	const second = 1000,
          minute = second * 60,
          hour = minute * 60,
          day = hour * 24;
	let vacc_frm = $("#vfrm_dt").val();
	    vto_dt = $("#vto_dt").val();
	    vacc_frm = new Date(vacc_frm).getTime();
	 	 vto_dt = new Date(vto_dt).getTime();
	let	distance = vto_dt - vacc_frm;
	let	days_info = Math.floor(distance / (day));
	let hours_info = days_info*8;
	$("#hrs_re").show();
	$("#hours_req").text(hours_info);
});

$(document).on('change','#all_userb', function(e){
	e.preventDefault();
	if(this.checked) {
            var returnVal = confirm("Are you sure?");
            $(this).prop("checked", returnVal);

            $("#all_u_c").hide();
            $("#all_u_u").hide();
        }else{
        	 $("#all_u_c").show();
            $("#all_u_u").show();
        }
});
$(document).on('change','#time_in', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	$('#time_out').timepicker({ minTime: time_val});
});
$(document).on('change','#role', function(e){
	e.preventDefault();
	var role = $(this).val();
	if(role == 'user'){
		$("#single_comp").show();
		$("#multiple_comp").hide();
	}else{
		$("#single_comp").hide();
		$("#multiple_comp").show();
	}

});

$(document).on('change','#time_out', function(e){
    var time_in_val =  $("#time_in").val();
    var time_out_val =  $("#time_out").val();
    var baseUrl = $(this).attr('data-baseURL');
		$.ajax({
			url: baseUrl+"/get/totalhours/"+time_in_val+"/"+time_out_val,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
			    $(".loding").hide();
				$("#total_hours").val(data);
			}
		});
});

//Delete User
$(document).on('click','.delete_user', function(){
	var id = $(this).attr('data-id');
	// alert(id);
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this User?')){
		$.ajax({
			url: baseUrl+"/usersd/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				// $(".del_alert").text("User Deleted Successfully!!");
				// $("#delete-alert").show().delay(2000).fadeOut();
				alert("User Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/users";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Vacc
$(document).on('click','.delete_vaccations', function(){
	var id = $(this).attr('data-id');
	alert(id);
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Entry?')){
		$.ajax({
			url: baseUrl+"/vaccations/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				// $(".del_alert").text("User Deleted Successfully!!");
				// $("#delete-alert").show().delay(2000).fadeOut();
				alert("entry Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/vaccations";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Notes
$(document).on('click','.delete_issue', function(){
	var id = $(this).attr('data-id');
	alert(id);
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Entry?')){
		$.ajax({
			url: baseUrl+"/lists-issue/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				// $(".del_alert").text("User Deleted Successfully!!");
				// $("#delete-alert").show().delay(2000).fadeOut();
				alert("Note Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/lists-issue";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Company
$(document).on('click','.delete_compnay', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Compnay?')){
		$.ajax({
			url: baseUrl+"/companies/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Company Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/companies";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Time Sheet
$(document).on('click','.delete_ts', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Time?')){
		$.ajax({
			url: baseUrl+"/time-sheets/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/time-sheets";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});
$(document).on('click','.delete_ats', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Time?')){
		$.ajax({
			url: baseUrl+"/user/destroy/timesheets/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					location.reload();
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});
$(document).on('click','.delete_mts', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Time?')){
		$.ajax({
			url: baseUrl+"/cmuser/destroy/timesheets/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/time-sheets";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});
$(document).on('click','.delete_sts', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Time?')){
		$.ajax({
			url: baseUrl+"/suser/destroy/timesheets/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/time-sheets";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete House
$(document).on('click','.delete_house', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this House?')){
		$.ajax({
			url: baseUrl+"/houses/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/houses";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Department
$(document).on('click','.delete_department', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Department?')){
		$.ajax({
			url: baseUrl+"/department/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Department Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/department";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

//Delete Master User
$(document).on('click','.delete_masteruser', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this User for this manager?')){
		$.ajax({
			url: baseUrl+"/users/musers/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("User for this manager Deleted Successfully!!")
				setTimeout(function () {
					window.location.href = baseUrl+"/managers";
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

$(document).ready(function(){
$.validator.addMethod("time24", function(value, element) { 
    return /^([01]?[0-9]|2[0-3])(:[0-5][0-9]){2}$/.test(value);
}, "Invalid time format.");

$(function() {
    $('#time_sheet').validate({
        rules: {
			company_id: {required: true},
			house_id: {required: true},
			hours_day: {required: true},
			time_in: {required: true},
			time_out: {required: true},
        }
    });
});
});


//Search Data

//TimeSheet Between Date
$(document).on('click','#submit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var ur = baseUrl+'/export/timesheet/srchdata/'+frmdate+'/'+todate;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

//TimeSheet Between Date
$(document).on('click','#asubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	var ur = baseUrl+'/user/export/timesheet/srchdata/'+frmdate+'/'+todate+'/'+user_id;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('click','#aasubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_pay_ts =$("#search_by_pay_ts").val();
	var result = search_by_pay_ts.split('-');
    var frmdate1 = result[0];
	var frmdate = frmdate1.split('_');
	var frmdate  = frmdate[0] + '-' + frmdate[1] + '-' + frmdate[2];
	var todate1 = result[1];
	var todate = todate1.split('_');
	var todate  = todate[0] + '-' + todate[1] + '-' + todate[2];
	//var frmdate = $("#frm_dt").val();
	//var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	var ur = baseUrl+'/user/export/timesheet/srchdata/'+frmdate+'/'+todate+'/'+user_id;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/times",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

//TimeSheet Between Date
$(document).on('click','#msubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	var ur = baseUrl+'/muser/export/timesheet/srchdata/'+frmdate+'/'+todate+'/'+user_id;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/muser/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

//TimeSheet Between Date
$(document).on('click','#ssubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	var ur = baseUrl+'/suser/export/timesheet/srchdata/'+frmdate+'/'+todate+'/'+user_id;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('click','#cmsubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/cmuser/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$('#mySelect').on('change', function() {
  var value = $(this).val();
  alert(value);
});
//Search User
/*
$(document).on('click','#submit_users', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_users = $("#srch_users").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/searchs",
		method: "GET",
		data: {srch_users:srch_users,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#srch_result").html(data);
		}
	})		
	
		
});*/
$(document).on('click','#submit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#srch_user").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});
$(document).on('click','#nsubmit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#srch_user").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/nsearch",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('change','#aap_status', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	var baseUrl = $(this).attr('data-baseURL');
	var aap_status = $("#aap_status").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/app_status/search",
		method: "GET",
		data: {aap_status:aap_status,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});

$(document).on('change','#naap_status', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	var baseUrl = $(this).attr('data-baseURL');
	var aap_status = $("#naap_status").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/napp_status/search",
		method: "GET",
		data: {aap_status:aap_status,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});
//Search User
$(document).on('click','#ssubmit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#ssrch_user").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('click','#nssubmit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#ssrch_user").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/nsearch",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

//Search User
$(document).on('click','#msubmit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#srch_user").val();

	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/muser/search",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

 $(document).on('submit','#time_sheet',function(){
   $(".submit_data").prop("disabled", true);
});

$(document).on('change','#to_dt', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var ur = baseUrl+'/suser/exportall/timesheet/'+frmdate+'/'+todate;
	$("#export_all_user").attr("href", ur);
});
//TimeSheet Between Date
$(document).on('click','#search_payperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var ssearch_by_comp = $("#ssearch_by_comp").val();
	var ur = baseUrl+'/suser/exportall/timesheet/'+frmdate+'/'+todate+'/'+ssearch_by_comp;
	$("#export_all_user").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search/payperiod",
		method: "GET",
		data: {ssearch_by_comp:ssearch_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});
$(document).on('click','#suser_sspayperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_payu =$("#search_by_payu").val();
	var result = search_by_payu.split('-');
    var frmdate = result[0];
	// var frmdate = frmdate1.split('_');
	// var frmdate  = frmdate[0] + '-' + frmdate[1] + '-' + frmdate[2];
	var todate = result[1];
	// var todate = todate1.split('_');
	// var todate  = todate[0] + '-' + todate[1] + '-' + todate[2];
	var search_by_compp = $("#search_by_compp").val();
	var ur = baseUrl+'/suser/exportall/timesheet/'+frmdate+'/'+todate+'/'+search_by_compp;
	console.log(frmdate+" "+todate)
	$("#export_all_user").attr("href", ur);
	
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search/payperiod",
		method: "GET",
		data: {ssearch_by_comp:search_by_compp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		// data: {search_by_compp:search_by_compp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});
$(document).on('click','#nsearch_payperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var ssearch_by_comp = $("#ssearch_by_comp").val();
	var ur = baseUrl+'/suser/exportall/timesheet/'+frmdate+'/'+todate+'/'+ssearch_by_comp;
	$("#export_all_user").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/nsearch/payperiod",
		method: "GET",
		data: {ssearch_by_comp:ssearch_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});

//all delete
$(document).on('click','#spapprove_all', function(event){
	event.preventDefault();
	jQuery('#dec_msg').fadeOut('slow');
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_id"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Approve timesheets?')){
			$.ajax({
				url: baseUrl+"/suser/approve/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data);
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});


//all delete
$(document).on('click','#cmapprove_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_id"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Approve timesheets?')){
			$.ajax({
				url: baseUrl+"/cmuser/approve/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data);
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_approve', function(){
    $('.time_id').prop('checked',this.checked);
});

//decline
$(document).on('click','#spdecline_all', function(event){
	event.preventDefault();
	jQuery('#dec_msg').fadeIn('slow');
    var ids = [];
	var baseUrl = $(this).attr('data-baseURL');
	$('input[class="time_idd"]:checked').each(function() {
	   ids.push(this.value); 
	});
	var dataString = JSON.stringify(ids);
	$("#ids_value").val(dataString);

});
$(document).on('click','#decl_mess', function(event){
	event.preventDefault();
	jQuery('#dec_msg').fadeIn('slow');
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		var dataString = $("#ids_value").val();
		var decline_message = $("#decline_message").val();
		if(confirm('Are you sure you want to Decline timesheets?')){
			$.ajax({
				url: baseUrl+"/suser/decline/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "dec_msg": decline_message, "ids_value": dataString},
				success:function(data){
					alert(data);
					alert("Time Declined Successfully!!")
					// setTimeout(function () {
					// 	location.reload();
					// }, 2000);
				}
			})		
		}else{
			return false;
		}

});

$(document).on('click','#cmdecline_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_idd"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Decline timesheets?')){
			$.ajax({
				url: baseUrl+"/cmuser/decline/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert("Time Declined Successfully!!")
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_decline', function(){
    $('.time_idd').prop('checked',this.checked);
});

$(document).on('click','#spdelete_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_idde"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Delete timesheets?')){
			$.ajax({
				url: baseUrl+"/suser/delete/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert("Time Delete Successfully!!")
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#cmdelete_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_idde"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Delete timesheets?')){
			$.ajax({
				url: baseUrl+"/cmuser/delete/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert("Time Delete Successfully!!")
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_delete', function(){
    $('.time_idde').prop('checked',this.checked);
});



/////////  Supervisor ///////////////
$(document).on('click','.delete_sats', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Time?')){
		$.ajax({
			url: baseUrl+"/user/suser/destroy/timesheets/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Time Deleted Successfully!!")
				setTimeout(function () {
					location.reload();
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});
//TimeSheet Between Date
$(document).on('click','#sasubmit_date', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var user_id = $("#user_id").val();
	var ur = baseUrl+'/user/suser/export/timesheet/srchdata/'+frmdate+'/'+todate+'/'+user_id;
	$("#saexport").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/suser/search/time",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,user_id:user_id,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});
//Search User
$(document).on('click','#ssasubmit_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var srch_user = $("#sasrch_user").val();
	// alert(srch_user+" "+baseUrl);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/suser/all/search",
		method: "GET",
		data: {srch_user:srch_user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('change','#to_dt', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var user = $(this).attr('data-user');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var ur = baseUrl+'/user/suser/exportall/timesheet/'+frmdate+'/'+todate+'/'+user;
	$("#saexport_all_user").attr("href", ur);
});
//TimeSheet Between Date
$(document).on('click','#sasearch_payperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var user = $(this).attr('data-user');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var sasearch_by_comp = $("#sasearch_by_comp").val();
	
	var ur = baseUrl+'/user/suser/exportall/timesheet/'+frmdate+'/'+todate+'/'+user+'/'+sasearch_by_comp;
	$("#saexport_all_user").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/suser/search/payperiod",
		method: "GET",
		data: {sasearch_by_comp:sasearch_by_comp,frmdate:frmdate,todate:todate,user:user,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();

			$("#result").html(data);
		}
	})		
	
		
});


//all delete
$(document).on('click','#approve_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_id"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Approve timesheets?')){
			$.ajax({
				url: baseUrl+"/user/suser/approve/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data)
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_approve', function(){
    $('.time_id').prop('checked',this.checked);
});

//decline
$(document).on('click','#decline_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_idd"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Decline timesheets?')){
			$.ajax({
				url: baseUrl+"/user/suser/decline/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data)
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_decline', function(){
    $('.time_idd').prop('checked',this.checked);
});

$(document).on('click','#delete_all', function(event){
	event.preventDefault();
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="time_idde"]:checked').each(function() {
		   ids.push(this.value); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Delete timesheets?')){
			$.ajax({
				url: baseUrl+"/user/suser/delete/timesheet",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data)
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});
$(document).on('click','#time_delete', function(){
    $('.time_idde').prop('checked',this.checked);
});



$(document).on('click','#user_payperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var search_by_pay =$("#search_by_pay").val();
	var search_by_comp = $("#search_by_comp").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_comp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/payperiod",
		method: "GET",
		data: {search_by_pay:search_by_pay,search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});

$(document).on('click','#user_spayperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_payu =$("#search_by_payu").val();
	var result = search_by_payu.split('-');
    var frmdate1 = result[0];
	var frmdate = frmdate1.split('_');
	var frmdate  = frmdate[0] + '-' + frmdate[1] + '-' + frmdate[2];
	var todate1 = result[1];
	var todate = todate1.split('_');
	var todate  = todate[0] + '-' + todate[1] + '-' + todate[2];
	var search_by_compp = $("#search_by_compp").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_compp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/upayperiod",
		method: "GET",
		data: {search_by_payu:search_by_payu,search_by_compp:search_by_compp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});


$(document).on('click','#user_sspayperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_payu =$("#search_by_payu").val();
	var result = search_by_payu.split('-');
    var frmdate1 = result[0];
	var frmdate = frmdate1.split('_');
	var frmdate  = frmdate[0] + '-' + frmdate[1] + '-' + frmdate[2];
	var todate1 = result[1];
	var todate = todate1.split('_');
	var todate  = todate[0] + '-' + todate[1] + '-' + todate[2];
	var search_by_compp = $("#search_by_compp").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_compp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search/supayperiod",
		method: "GET",
		data: {search_by_payu:search_by_payu,search_by_compp:search_by_compp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});
$(document).on('click','#user_payperiods', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var search_by_pay =$("#search_by_pay").val();

	var search_by_comp = $("#search_by_comp").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_comp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/payperiods",
		method: "GET",
		dataType: "json",
		data: {search_by_pay:search_by_pay,search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
		//	$('#data-main').append("<h5>"Summary+frmdate+"</h5>");
			 // $('#data-main').append("<h3>Your message has been submitted successfully!</h3>");
			$("#result").html(data.tabledata);
			$("#data-main").html(data.empsummary);
		}
	})		
	
		
});
$("#user_payperiods").click(function(){
  $("#data-main").show();
});
$(document).ready(function () {
   $("#data-main").hide();
});

$(document).on('click','#user_fpayperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	//var frmdate = $("#frm_dt").val();
	//var todate = $("#to_dt").val();
	var search_by_pays =$("#search_by_pays").val();
	var result = search_by_pays.split('-');
    var frmdate1 = result[0];
	var frmdate = frmdate1.split('_');
	var frmdate  = frmdate[0] + '-' + frmdate[1] + '-' + frmdate[2];
	var todate1 = result[1];
	var todate = todate1.split('_');
	var todate  = todate[0] + '-' + todate[1] + '-' + todate[2];

 //console.log(frmdate, "Hello, world!");

	var search_by_compf = $("#search_by_compf").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_compf;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/search/fpayperiods",
		method: "GET",
		dataType: "json",
		data: {search_by_pays:search_by_pays,search_by_compf:search_by_compf,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		//data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		
		beforeSend:function(){
			$(".loding").show();
		
		},
		success:function(data){
			$(".loding").hide();
			$("#result").html(data.tabledata);
			$("#data-main").html(data.empsummary);
		}
	})		
	
		
});
$("#user_fpayperiod").click(function(){
  $("#data-main").show();
});
$(document).ready(function () {
   $("#data-main").hide();
});

$(document).on('click','#nuser_payperiod', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	var search_by_comp = $("#search_by_comp").val();
	var ur = baseUrl+'/user/aexport/payperiod/'+frmdate+'/'+todate+'/'+search_by_comp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/nsearch/payperiod",
		method: "GET",
		data: {search_by_comp:search_by_comp,frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});

/////////  Notifications ///////////////
$(document).on('click','.delete_notification', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Notifications?')){
		$.ajax({
			url: baseUrl+"/notifications/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Notification Deleted Successfully!!")
				setTimeout(function () {
					location.reload();
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});

/////////  Notifications ///////////////
$(document).on('click','.delete_payperiod', function(){
	var id = $(this).attr('data-ID');
	var baseUrl = $(this).attr('data-baseURL');
	if(confirm('Are you sure you want to delete this Notifications?')){
		$.ajax({
			url: baseUrl+"/payperiods/destroy/"+id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				alert("Notification Deleted Successfully!!")
				setTimeout(function () {
					location.reload();
				}, 2000);
			}
		})		
	}else{
		return false;
	}
		
});


$(document).on('change','#company_idu', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var company_id = $(this).val();
	$.ajax({
			url: baseUrl+"/house/compnay/"+company_id,
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				$(".loding").hide();
				$("#house_id").html(data);
			}
		})		
});


$(document).on('click','#all_user_app', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var frmdate = $("#frm_dt").val();
	var todate = $("#to_dt").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/all/users/all_app_timesheet/search",
		method: "GET",
		data: {frmdate:frmdate,todate:todate,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})			
});


$(document).on('click','#payroll', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var payperiod = $("#payperiod").val();
	var search_by_comp = $("#search_by_comp").val();
	var ur = baseUrl+'/user/payroll/search/postdata/'+payperiod+'/'+search_by_comp;
	var hur = baseUrl+'/user/payroll/search/hpostdata/'+payperiod+'/'+search_by_comp;
	var csvur = baseUrl+'/user/payroll/search/csvpostdata/'+payperiod+'/'+search_by_comp;
 	//alert(csvur);
	$("#export").attr("href", ur);
	$("#hexport").attr("href", hur);
	$("#ecsv").attr("href", csvur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/search/payroll",
		method: "GET",
		data: {payperiod:payperiod,search_by_comp:search_by_comp,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});
$(document).on('click','#payroll', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var payperiod = $("#payperiod").val();
	var search_by_comp = $("#search_by_comp").val();
	var csvur = baseUrl+'/user/payroll/search/csvpostdata/'+payperiod+'/'+search_by_comp;
 	//alert(csvur);
	$("#ecsv").attr("href", csvur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/search/payroll",
		method: "GET",
		data: {payperiod:payperiod,search_by_comp:search_by_comp,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#results").html(data);
		}
	})		
	
		
});



$(document).on('click','#spayroll', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var payperiod = $("#payperiod").val();
	var search_by_comp = $("#search_by_comp").val();
	var ur = baseUrl+'/suser/payroll/search/postdata/'+payperiod+'/'+search_by_comp;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/suser/search/payroll",
		method: "GET",
		data: {payperiod:payperiod,search_by_comp:search_by_comp,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})		
	
		
});


$(document).on('change','#user_status', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	var baseUrl = $(this).attr('data-baseURL');
	var user_status = $("#user_status").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/user_status/search",
		method: "GET",
		data: {user_status:user_status,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});

$(document).on('change','#nuser_status', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	var baseUrl = $(this).attr('data-baseURL');
	var user_status = $("#user_status").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/user_status/search",
		method: "GET",
		data: {user_status:user_status,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});
$(document).on('change','#vaccine_status', function(e){
	e.preventDefault();
	var time_val =  $(this).val();
	var baseUrl = $(this).attr('data-baseURL');
	var vaccine_status = $("#vaccine_status").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/vaccine_status/search",
		method: "GET",
		data: {vaccine_status:vaccine_status,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});


$(document).on('change','#search_by_super', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_super = $("#search_by_super").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/user_color/search",
		method: "GET",
		data: {search_by_super:search_by_super,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});
$(document).on('change','#nsearch_by_super', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var search_by_super = $("#search_by_super").val();
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/user/user_color/search",
		method: "GET",
		data: {search_by_super:search_by_super,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			$("#result").html(data);
		}
	})		
});

$(document).on('click','#submit_dt', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var aap_month = $("#aap_month").val();
	var aap_year = $("#aap_year").val();
	var ur = baseUrl+'/all/new-applicants/month/'+aap_month+'/'+aap_year;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/all/new-applicants/search",
		method: "GET",
		data: {aap_month:aap_month,aap_year:aap_year,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})			
});



$(document).on('click','#inactive_user', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var from_month = $("#frm_dt").val();
	var to_month = $("#to_dt").val();
	var ur = baseUrl+'/inactive/employees/month/'+from_month+'/'+to_month;
	$("#export").attr("href", ur);
	$(".loding").hide();
	$.ajax({
		url: baseUrl+"/inactive/employees/search",
		method: "GET",
		data: {from_month:from_month,to_month:to_month,_token: '{{csrf_token()}}'},
		beforeSend:function(){
			$(".loding").show();
		},
		success:function(data){
			$(".loding").hide();
			// alert(data);
			// console.log(data);
			$("#result").html(data);
		}
	})			
});

$(document).on('click','#nttime_approve', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var from_month = $(this).attr('data-frmdt');
	var to_month = $(this).attr('data-todt');
	var uid = $(this).attr('data-uid');
	var ur = baseUrl+'/susers/time/'+from_month+'/'+to_month;

	// $("#export").attr("href", ur);
	if(confirm('Are you sure you want to approve timesheet for this User?')){
		$(".loding").hide();
		$.ajax({
			url: baseUrl+"/suser/ntapprove/timesheet",
			method: "GET",
			data: {uid:uid,from_month:from_month,to_month:to_month,_token: '{{csrf_token()}}'},
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				$(".loding").hide();
				if(data == "1"){
					alert("Approved Successfully!");
					 window.location = ur;
				}
	           
				
				// console.log(data);
				// $("#result").html(data);
			}
		});
	}else{
		return false;
	}		
});


$(document).on('click','#nttime_decline', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var from_month = $(this).attr('data-frmdt');
	var to_month = $(this).attr('data-todt');
	var uid = $(this).attr('data-uid');
	var ur = baseUrl+'/susers/time/'+from_month+'/'+to_month;
	// $("#export").attr("href", ur);
	if(confirm('Are you sure you want to decline timesheet for this User?')){
		$(".loding").hide();
		$.ajax({
			url: baseUrl+"/suser/ntdecline/timesheet",
			method: "GET",
			data: {uid:uid,from_month:from_month,to_month:to_month,_token: '{{csrf_token()}}'},
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				$(".loding").hide();
	           if(data == "1"){
					alert("Declined Successfully!");
					 window.location = ur;
				}
				// alert(data);
				// console.log(data);
				// $("#result").html(data);
			}
		});
	}else{
		return false;
	}			
});

$(document).on('click','#nttime_delete', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var from_month = $(this).attr('data-frmdt');
	var to_month = $(this).attr('data-todt');
	var uid = $(this).attr('data-uid');
	var ur = baseUrl+'/susers/time/'+from_month+'/'+to_month;
	// $("#export").attr("href", ur);
	if(confirm('Are you sure you want to delete timesheet for this User?')){
		$(".loding").hide();
		$.ajax({
			url: baseUrl+"/suser/ntdelete/timesheet",
			method: "GET",
			data: {uid:uid,from_month:from_month,to_month:to_month,_token: '{{csrf_token()}}'},
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				$(".loding").hide();
	           if(data == "1"){
					alert("Deleted Successfully!");
					 window.location = ur;
				}
				// alert(data);
				// console.log(data);
				// $("#result").html(data);
			}
		});
	}else{
		return false;
	}			
});

$(document).on('click','#nttime_view', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var from_month = $(this).attr('data-frmdt');
	var to_month = $(this).attr('data-todt');
	var uid = $(this).attr('data-uid');
	var ur = baseUrl+'/susers/time/'+from_month+'/'+to_month;
	// $("#export").attr("href", ur);
		$(".loding").hide();
		$.ajax({
			url: baseUrl+"/suser/nsearch/time",
			method: "GET",
			data: {uid:uid,from_month:from_month,to_month:to_month,_token: '{{csrf_token()}}'},
			beforeSend:function(){
				$(".loding").show();
			},
			success:function(data){
				$(".loding").hide();
	            // window.location = ur;
				// alert(data);
				// console.log(data);
				$("#result2").html(data);
			}
		});		
});

$(document).on('click', '#vacc_aaprove', function(e) {
    e.preventDefault();
    var baseUrl = $(this).attr('data-baseURL');
    var vacc_id = $(this).attr('data-uid');
    var app_id  = $(this).attr('data-uaid');

    Swal.fire({
        title: 'Approve Vacation?',
        text: 'Are you sure you want to approve this vacation?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        $.ajax({
            url: baseUrl + "/user/vaccation/approve",
            type: "GET",
            data: {
                vacc_id: vacc_id,
                app_id: app_id
            },
            beforeSend: function() {
                $(".loding").show();
            },
            success: function(response) {
                $(".loding").hide();
                if (response.status === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Already Processed',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
        });
    });
});

$(document).on('click', '#vacc_decline', function(e) {
    e.preventDefault();
    var baseUrl = $(this).attr('data-baseURL');
    var vacc_id = $(this).attr('data-uid');
    var app_id  = $(this).attr('data-uaid');

    Swal.fire({
        title: 'Decline Vacation?',
        text: 'Are you sure you want to decline this vacation?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Decline',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        $.ajax({
            url: baseUrl + "/user/vaccation/decline",
            type: "GET",
            data: {
                vacc_id: vacc_id,
                app_id: app_id
            },

            beforeSend: function() {
                $(".loding").show();
            },
            success: function(response) {
                $(".loding").hide();
                if (response.status === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Declined!',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Already Processed',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
        });
    });
});

$(document).on('click','#svacc_view',function(){
    var id=$(this).data('uid');
    var baseUrl=$(this).data('baseurl');

    $.ajax({
        url: baseUrl+'/suser/vaccation/view',
        type:'GET',
        data:{id:id},
        success:function(response){
            $('#myModal .modal-body').html(response);
        }
    });
});

$(document).on('click','#vacc_view',function(){
    var id=$(this).data('uid');
    var baseUrl=$(this).data('baseurl');

    $.ajax({
        url: baseUrl+'/auser/vaccation/view',
        type:'GET',
        data:{id:id},
        success:function(response){
            $('#myModal .modal-body').html(response);
        }
    });
});

$(document).on('click','#svacc_aaprove', function(e){
    e.preventDefault();

    var baseUrl = $(this).attr('data-baseURL');
    var vacc_id = $(this).attr('data-uid');
    var app_id = $(this).attr('data-uaid');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to approve vacation for this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve it!'
    }).then((result) => {

        if(result.isConfirmed){

            $(".loding").hide();

            $.ajax({
                url: baseUrl + "/suser/vaccation/approve",
                method: "GET",
                data: {
                    vacc_id: vacc_id,
                    app_id: app_id,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend:function(){
                    $(".loding").show();
                },
                success:function(data){
                    $(".loding").hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: data,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // agar reload nahi chahiye to ye line hata do
                    });
                },
                error:function(){
                    $(".loding").hide();

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                }
            });

        }

    });

});

$(document).on('click','#svacc_decline', function(e){
    e.preventDefault();

    var baseUrl = $(this).attr('data-baseURL');
    var vacc_id = $(this).attr('data-uid');
    var app_id = $(this).attr('data-uaid');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to decline vacation for this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Decline it!'
    }).then((result) => {

        if(result.isConfirmed){

            $(".loding").hide();

            $.ajax({
                url: baseUrl + "/suser/vaccation/decline",
                method: "GET",
                data: {
                    vacc_id: vacc_id,
                    app_id: app_id,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend:function(){
                    $(".loding").show();
                },
                success:function(data){
                    $(".loding").hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Declined!',
                        text: data,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error:function(){
                    $(".loding").hide();

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                }
            });

        }

    });

});

//// Staf List ////

$(document).on('change','#search_by_stafflist', function(e){
	e.preventDefault();
	var baseUrl = $(this).attr('data-baseURL');
	var company = $(this).val();
	var ur = baseUrl+'/userss/staff-list/'+company;
	$("#export_s").attr("href", ur);
});

// $(document).on('click','.app_all', function(){
// 	// e.preventDefault();
// 	// alert($(this).val());
// 	var baseUrl = $(this).attr('data-baseurl');
// 	var from_dt = $(this).attr('data-frmdt');
// 	var to_dt = $(this).attr('data-todt');
// 	var uid = $(this).attr('data-uid');
// 	let val_add = $(this).attr('data-val_add');
// 	let ttime = $(this).attr('data-ttime');
// 	if(val_add == 1){
// 		val_add = 0;
// 		$(this).attr('data-val_add',0);
// 		ttime = 0;
// 	}else if(val_add == 0){
// 		val_add = 1;
// 		$(this).attr('data-val_add',1);
// 	}
// 	if ( $("#checkbox_"+uid).attr('checked')) {
// 		$("#checkbox_"+uid).attr('checked', true);
//     } else {
//     	$("#checkbox_"+uid).attr('checked', false);
//     }
// 	// alert(val_add+"-"+baseUrl+"-"+from_dt+"-"+to_dt+"-"+uid);
// 	if(confirm('Are you sure you want to approve timesheet for this User?')){
// 		$(".loding").hide();
// 		$.ajax({
// 			url: baseUrl+"/suser/atime/all",
// 			method: "GET",
// 			data: {val_addd:val_add,id:uid,frm_dt:from_dt,to_dt:to_dt,_token: '{{csrf_token()}}'},
// 			beforeSend:function(){
// 				$(".loding").show();
// 			},
// 			success:function(data){
// 				$(".loding").hide();
// 				$("#ap_t_"+uid).text(ttime);
// 	            // window.location = ur;
// 				alert(data);
// 				// console.log(data);
// 				// $("#result2").html(data);
// 			}
// 		});
// 	}else{
// 		return false;
// 	}			
// });

jQuery(document).ready(function(){
	$('th').click(function(){
	    var table = $(this).parents('table').eq(0)
	    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
	    this.asc = !this.asc
	    if (!this.asc){rows = rows.reverse()}
	    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
	})
	function comparer(index) {
	    return function(a, b) {
	        var valA = getCellValue(a, index), valB = getCellValue(b, index)
	        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
	    }
	}
	function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
});

$(document).on('click','#tpapprove_all', function(event){
	event.preventDefault();
	jQuery('#dec_msg').fadeOut('slow');
	    var ids = [];
		var baseUrl = $(this).attr('data-baseURL');
		$('input[class="astime_id"]:checked').each(function() {
			let val_add = $(this).attr('data-val_add');
			if(val_add == 1){
				val_add = 0;
				$(this).attr('data-val_add',0);
				ttime = 0;
			}else if(val_add == 0){
				val_add = 1;
				$(this).attr('data-val_add',1);
			}
			let pair1 = { id: $(this).attr('data-uid'),from_dt: $(this).attr('data-frmdt'), to_dt: $(this).attr('data-todt'), vall_add: $(this).attr('data-val_add'),ttime: $(this).attr('data-ttime') };
		   ids.push(pair1); 
		});
		var dataString = JSON.stringify(ids);
		if(confirm('Are you sure you want to Approve timesheets?')){
			$.ajax({
				url: baseUrl+"/suser/atime/all",
				method: "GET",
				data: {"_token": $('meta[name="csrf-token"]').attr('content'), "ids_value": dataString},
				success:function(data){
					alert(data);
					setTimeout(function () {
						location.reload();
					}, 2000);
				}
			})		
		}else{
			return false;
		}

});


//Disabled 
$(document).ready(function () {
    function toggleMonths() {
        var currentYear = new Date().getFullYear();
        var currentMonth = new Date().getMonth() + 1;
        var selectedYear = parseInt($("#aap_year").val());

        $("#aap_month option").prop("disabled", false);
        if (selectedYear == currentYear) {
            $("#aap_month option").each(function () {
                var month = parseInt($(this).val());
                if (month > currentMonth) {
                    $(this).prop("disabled", true);
                }
            });
            if (parseInt($("#aap_month").val()) > currentMonth) {
                $("#aap_month").val("0");
            }
        }
    }
    $("#aap_year").change(function () {
        toggleMonths();
    });
    toggleMonths();
});