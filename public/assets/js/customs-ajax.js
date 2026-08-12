//Delete User role section
$(document).ready(function() {
    //Delete timesheet record
    $('body').on('click', '.delete_timesheet_record', function(event) {
        event.preventDefault();
        //Get data attribute
        var timesheet_id = $(this).data('timesheet_id');    
        //Delete through sweet alert
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this timesheet!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                //Call ajax
                $.ajax({
                    type: 'delete',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: base_url+ '/time-sheets/destroy',  
                    data: { 
                        timesheet_id: timesheet_id 
                    },
                    //Show success message
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Timesheet deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                });
            }
        });
    });

    //Supervisor section
    //Delete timesheet record
    $('body').on('click', '.delete_utimesheet_record', function(event) {
        event.preventDefault();
        //Get data attribute
        var timesheet_id = $(this).data('timesheet_id');    
        //Delete through sweet alert
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this timesheet!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                //Call ajax
                $.ajax({
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: Base_url+ '/suser/destroy/timesheets',  
                    data: { 
                        timesheet_id: timesheet_id 
                    },
                    //Show success message
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Timesheet deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                });
            }
        });
    });

    //Admin section
    //Delete payperiod record
    $('body').on('click', '.delete_payperiod_record', function(event) {
        event.preventDefault();
        //Get data attribute
        var payperiod_id = $(this).data('payperiod_id');    
        //Delete through sweet alert
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this payperiod!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                //Call ajax
                $.ajax({
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: bs_url+ '/payperiod/destroy',  
                    data: { 
                        payperiod_id: payperiod_id 
                    },
                    //Show success message
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Payperiod deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                });
            }
        });
    });
    //Delete vocation record
    $('body').on('click', '.delete_vocation_record', function(event) {
        event.preventDefault();
        //Get data attribute
        var vocation_id = $(this).data('vocation_id');    
        //Delete through sweet alert
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this vocation!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                //Call ajax
                $.ajax({
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: bs_url+ '/vaccation/destroy',  
                    data: { 
                        vocation_id: vocation_id 
                    },
                    //Show success message
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Vocation deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                });
            }
        });
    });
});
