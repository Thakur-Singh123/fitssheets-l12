<!doctype html>
<html lang="en">
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>Fitssheets</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
      <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}"/>
      <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/ionicons/css/ionicons.css') }}" />
      <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/typicons/src/font/typicons.css') }}" />
      <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}"/>
      <link rel="stylesheet" href="{{ url('public/assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css') }}"/>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.css"/>
      <link rel="stylesheet" href="{{ url('public/assets/css/shared/multi-select.css') }}" />
      <link rel="stylesheet" href="{{ url('public/assets/css/shared/flatpickr.css') }}" />
      <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
      <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}" />
      <link rel="stylesheet" href="{{ url('public/assets/css/shared/style.css') }}" />
      <link rel="stylesheet" href="{{ url('public/assets/css/demo_1/style.css') }}" />
      <link rel="shortcut icon" href="{{ url('public/assets/images/favicon.png') }}" />
    </head>
    <body>
        <div class="container-scroller">
            @include('admin/partials/top-bar')
            <div class="container-fluid page-body-wrapper">
                @include('admin/partials/sidebar')
                <div class="main-panel">
                    @yield('content')
                    @include('admin/partials/footer')
                </div>
            </div>
        </div>
        <script>
            var bs_url = '{{ url("/") }}'; 
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="{{ asset('public/assets/js/customs-ajax.js') }}"></script>
        <script src="{{ asset('public/assets/js/customs.js') }}"></script>
        {{-- <script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script> --}}
        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="{{ url('public/assets/vendors/js/vendor.bundle.base.js') }}"></script>
        <script src="{{url('public/assets/vendors/js/vendor.bundle.addons.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/off-canvas.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/misc.js') }}"></script>
        <script src="https://www.bootstrapdash.com/demo/purple/jquery/template/assets/js/jq.tablesort.js"></script>
        <script src="{{ url('public/assets/js/shared/jquery.form.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/jquery.validate.min.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/additional-methods.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.js"></script>
        <script src="{{ url('public/assets/js/shared/jquery.multi-select.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/flatpickr.js') }}"></script>
        <script src="{{ url('public/assets/js/shared/custom.js') }}"></script>
        <script src="https://cdn.tiny.cloud/1/a5166ixzclg53wc5ewl8thgk6nm896f93te22fkxnixvf4dy/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" defer></script>
        <script src="{{ url('public/assets/js/demo_1/dashboard.js') }}"></script>
        <script>
            tinymce.init({
                selector: "textarea#not_text",
                height: 350,
                menubar: false,
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor textcolor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table paste code help wordcount",
                    "code",
                ],
                toolbar:
                    "save | code| undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help |link image |customCode",
                content_css: [
                    "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
                    "//www.tiny.cloud/css/codepen.min.css",
                ],
                setup: function (editor) {
                    editor.on("change", function () {
                        editor.save();
                    });
                },
            });
            $("#users_idm").multiSelect();
            $("#company_idm").multiSelect();
        </script>
        <script>
            $(function() {
                var current_progress = 40;
                var interval = setInterval(function() {
                current_progress += 10;
                $("#dynamic") .css("width", current_progress + "%") .attr("aria-valuenow", current_progress) .text(current_progress + "% Complete");
                if (current_progress >= 100)
                    clearInterval(interval);
                }, 1000);
            });
        </script>
        <script>
            $(document).ready(function () {
                setTimeout(function () {
                    $('.alert-danger').fadeOut(500);
                }, 500);
            });
        </script>
        <script>
        $(document).on('click','.password-toggle',function() {
            let input = $('#' + $(this).data('target'));
            if(input.attr('type') == 'password') {
            input.attr('type','text');
            $(this).find('i')
            .removeClass('fa-eye')
            .addClass('fa-eye-slash');
            } else {
            input.attr('type','password');
            $(this).find('i')
            .removeClass('fa-eye-slash')
            .addClass('fa-eye');
            }
        });
        </script>
    </body>
</html>
