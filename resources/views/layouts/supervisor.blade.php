<!DOCTYPE html>
<html lang="en">
  <head>
    <!--Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fitssheets</title>
    <!--plugins:css-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/ionicons/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/typicons/src/font/typicons.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.css">
    <link rel="stylesheet" href="{{ url('public/assets/css/shared/flatpickr.css') }}">
    <!--endinject-->
    <!--inject:css-->
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/fonts/Noveo/stylesheet.css') }}">
    <!--endinject-->
    <!--layout styles-->
    <link rel="stylesheet" href="{{ url('public/assets/css/demo_1/style.css') }}">
    <!--end layout styles-->
    <link rel="shortcut icon" href="{{ url('public/assets/images/favicon.png') }}" />
  </head>
  <body>
    <div class="container-scroller">
      <!--partial:partials/_navbar.html -->
      @include('supervisor/partials/top-bar')   
      <!--partial-->
      <div class="container-fluid page-body-wrapper">
        <!--partial:partials/_sidebar.html-->
        @include('supervisor/partials/sidebar')
        <!--partial-->
        <div class="main-panel">
          @yield('content')
          <!--partial:partials/_footer.html-->
          @include('supervisor/partials/footer')   
          <!--partial-->
        </div>
      </div>
      <!--page-body-wrapper ends-->
    </div>
    <!--base url for js-->
    <script>
      var Base_url = '{{ url("/") }}'; 
    </script>
    <!--jquery script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--sweetalert2 script-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!--core js files-->
    <script src="{{ asset('public/assets/js/customs-ajax.js') }}"></script>
    <script src="{{ asset('public/assets/js/customs.js') }}"></script>
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
    <!--endinject-->
    <!--custom js for this page-->
    <script src="{{ url('public/assets/js/demo_1/dashboard.js') }}"></script>
    <!--end custom js for this page-->
    <script>
    $(function() {
      var current_progress = 40;
      var interval = setInterval(function() {
      current_progress += 10;
      $("#dynamic")
      .css("width", current_progress + "%")
      .attr("aria-valuenow", current_progress)
      .text(current_progress + "% Complete");
      if (current_progress >= 100)
        clearInterval(interval);
      }, 1000);
    });
      //Error Alert Auto Hide
      setTimeout(function () {
        $('.error-alert').fadeOut(500);
      }, 4000);
    </script>
  </body>
</html>