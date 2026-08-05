<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fitssheets</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/ionicons/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/typicons/src/font/typicons.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css') }}" />
		 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.css">
		 <link rel="stylesheet" href="{{ url('public/assets/css/shared/flatpickr.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('public/assets/css/shared/style.css') }}">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ url('public/assets/css/demo_1/style.css') }}">
    <!-- End Layout styles -->
    <link rel="shortcut icon" href="{{ url('public/assets/images/favicon.png') }}" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
		@include('casemanager/partials/top-bar')   
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
      
        <!-- partial:partials/_sidebar.html -->
			@include('casemanager/partials/sidebar')
        <!-- partial -->
        <div class="main-panel">
			@yield('content')
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
			@include('casemanager/partials/footer')   
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
           <script>
            var base_url = '{{ url("/") }}'; 
        </script>
	<script src="{{ url('public/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ url('public/assets/vendors/js/vendor.bundle.addons.js') }}"></script>
    <script src="{{ url('public/assets/js/shared/off-canvas.js') }}"></script>
    <script src="{{ url('public/assets/js/shared/misc.js') }}"></script>
	<script src="{{ url('public/assets/js/shared/jquery.form.js') }}"></script>
	<script src="{{ url('public/assets/js/shared/jquery.validate.min.js') }}"></script>
		<script src="https://www.bootstrapdash.com/demo/purple/jquery/template/assets/js/jq.tablesort.js"></script>
	<script src="{{ url('public/assets/js/shared/additional-methods.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.js"></script>
	<script src="{{ url('public/assets/js/shared/flatpickr.js') }}"></script>
	<script src="{{ url('public/assets/js/shared/custom.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ url('public/assets/js/demo_1/dashboard.js') }}"></script>
    <!-- End custom js for this page-->
  </body>
</html>