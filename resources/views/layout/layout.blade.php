<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>{{ config('app.brand_name') }} | Student Tracker</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">


        <link rel="shortcut icon" href="{{asset("assets/images/favicon.ico")}}">

        <!-- jvectormap -->
        <link href="{{asset("assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css")}}" rel="stylesheet">
        <link href="{{asset("assets/plugins/fullcalendar/vanillaCalendar.css")}}" rel="stylesheet" type="text/css"  />
        
        <link href="{{asset("assets/plugins/morris/morris.css")}}" rel="stylesheet">

        <link href="{{asset("assets/css/bootstrap.min.css")}}" rel="stylesheet" type="text/css">
        <link href="{{asset("assets/css/icons.css")}}" rel="stylesheet" type="text/css">
        <link href="{{asset("assets/css/style.css")}}" rel="stylesheet" type="text/css">
        <script src="https://kit.fontawesome.com/f18b623a18.js" crossorigin="anonymous"></script>
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
        @stack('panel.css')
    </head>


    <body class="fixed-left">

        <!-- Loader -->
        {{-- <div id="preloader"><div id="status"><div class="spinner"></div></div></div> --}}

        <!-- Begin page -->
        <div id="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            @include('layout.components.sidebar')
            <!-- Left Sidebar End -->

            <!-- Start right Content here -->

            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    <!-- Top Bar Start -->
                    <div class="topbar">

                        <nav class="navbar-custom">

                            <ul class="list-inline float-right mb-0">

                                <li class="list-inline-item dropdown notification-list">
                                    <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                       aria-haspopup="false" aria-expanded="false">
                                        <img src="assets/images/users/avatar-1.jpg" alt="user" class="rounded-circle">
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <!-- item-->
                                        <div class="dropdown-item noti-title">
                                            <h5>Welcome</h5>
                                        </div>
                                        <a class="dropdown-item" href="#"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                        <a class="dropdown-item" href="#"><i class="mdi mdi-wallet m-r-5 text-muted"></i> My Wallet</a>
                                        <a class="dropdown-item" href="#"><span class="badge badge-success float-right">5</span><i class="mdi mdi-settings m-r-5 text-muted"></i> Settings</a>
                                        <a class="dropdown-item" href="#"><i class="mdi mdi-lock-open-outline m-r-5 text-muted"></i> Lock screen</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{route('auth.logout')}}"><i class="mdi mdi-logout m-r-5 text-muted"></i> Logout</a>
                                    </div>
                                </li>

                            </ul>

                            <ul class="list-inline menu-left mb-0">
                                <li class="float-left">
                                    <button class="button-menu-mobile open-left waves-light waves-effect">
                                        <i class="mdi mdi-menu"></i>
                                    </button>
                                </li>                                
                            </ul>

                            <div class="clearfix"></div>

                        </nav>

                    </div>
                    <!-- Top Bar End -->

                    <div class="page-content-wrapper ">

                        <div class="container-fluid">
                        
                            @yield('panel')

                        </div><!-- container -->

                    </div> <!-- Page content Wrapper -->

                </div> <!-- content -->

                <footer class="footer">
                    © {{ date('Y') }} {{ config('app.brand_name') }}
                </footer>

            </div>
            <!-- End Right content here -->

        </div>
        <!-- END wrapper -->


        <!-- jQuery  -->
        <script src="{{asset("assets/js/jquery.min.js")}}"></script>
        <script src="{{asset("assets/js/popper.min.js")}}"></script>
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --}}
        <script src="{{asset("assets/js/bootstrap.min.js")}}"></script>
        <script src="{{asset("assets/js/modernizr.min.js")}}"></script>
        <script src="{{asset("assets/js/detect.js")}}"></script>
        <script src="{{asset("assets/js/fastclick.js")}}"></script>
        <script src="{{asset("assets/js/jquery.blockUI.js")}}"></script>
        <script src="{{asset("assets/js/waves.js")}}"></script>
        <script src="{{asset("assets/js/jquery.nicescroll.js")}}"></script>

        <script src="{{asset("assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js")}}"></script>
        <script src="{{asset("assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js")}}"></script>
        
        <script src="{{asset("assets/plugins/skycons/skycons.min.js")}}"></script>
        <script src="{{asset("assets/plugins/fullcalendar/vanillaCalendar.js")}}"></script>
        
        <script src="{{asset("assets/plugins/raphael/raphael-min.js")}}"></script>
        <script src="{{asset("assets/plugins/morris/morris.min.js")}}"></script> 
        

        <script src="{{asset("assets/pages/dashborad.js")}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        <!-- App js -->
        <script src="{{asset("assets/js/app.js")}}"></script>
        <script>
            // ajax setup in jquery
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
            $('#confirm_student_btn').on('click',function(){
                $.ajax({
                    type: "GET",
                    url: "{{route('student.category')}}",
                    beforeSend: function(){
                        $("#confirm_student").append(' <li><a href="javascript:void(0);">Loading...</a></li>');
                    },
                    success: function (data) {
                        $("#confirm_student").empty();
                        data.forEach(course => {
                            let url = '{{route('student.conformed',":id")}}'.replace(':id',course.id);
                            $("#confirm_student").append(`
                                <li><a href="${url}">${course.name}</a></li>
                            `); 
                        });
                    }
                });
            });
            $('#intership_student_btn').on('click',function(){
                $.ajax({
                    type: "GET",
                    url: "{{route('student.category')}}",
                    beforeSend: function(){
                        $("#intership_student").append(' <li><a href="javascript:void(0);">Loading...</a></li>');
                    },
                    success: function (data) {
                        $("#intership_student").empty();
                        data.forEach(course => {
                            let url = '{{route('student.Intership',":id")}}'.replace(':id',course.id);
                            $("#intership_student").append(`
                                <li><a href="${url}">${course.name}</a></li>
                            `); 
                        });
                    }
                });
            });
        </script>
        @stack('panel.js')
    </body>
</html>