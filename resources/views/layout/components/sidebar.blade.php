<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="ion-close"></i>
    </button>

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{ route('dashboard.index') }}" class="logo">
                <span class="text-light" style="font-size: 20px;">{{ config('app.brand_name') }}</span>
            </a>
        </div>
    </div>

    <div class="sidebar-inner niceScrollleft">

        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>

                <li>
                    <a href="{{route('dashboard.index')}}" class="waves-effect">
                        <i class="mdi mdi-airplay"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li>
                    <a href="{{route('category.index')}}" class="waves-effect">
                        <i class="fa-solid fa-layer-group"></i>
                        <span> Category </span>
                    </a>
                </li>
                <li>
                    <a href="{{route('teacher.index')}}" class="waves-effect">
                        <i class="fa-solid fa-person-chalkboard"></i>
                        <span> Teacher </span>
                    </a>
                </li>

                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="fa-solid fa-graduation-cap"></i> <span> Courses </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{route('courses.index')}}">Courses list</a></li>
                        <li><a href="{{route('courses.create')}}">Courses create</a></li>
                    </ul>
                </li>
                
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="fa-solid fa-people-line"></i> <span> Admission </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{route('student.index')}}">Student list</a></li>
                        <li><a href="{{route('student.create')}}">Student create</a></li>
                    </ul>
                </li>
                <li class="has_sub" >
                    <a href="javascript:void(0);" id="confirm_student_btn" class="waves-effect"><i class="fa-solid fa-users-rectangle"></i><span> Students </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled" id='confirm_student'>
                    </ul>
                </li>
                <li class="has_sub" >
                    <a href="javascript:void(0);" id="intership_student_btn" class="waves-effect"><i class="fa-solid fa-users-between-lines"></i><span> intern </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled" id='intership_student'>
                    </ul>
                </li>

                <li>
                    <a href="{{route('expense.index')}}" class="waves-effect">
                        <i class="fa-solid fa-sack-dollar"></i>
                        <span> Expense </span>
                    </a>
                </li>

                {{-- <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-layers"></i> <span> Advanced UI </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="list-unstyled">
                        <li><a href="advanced-highlight.html">Highlight</a></li>
                        <li><a href="advanced-rating.html">Rating</a></li>
                        <li><a href="advanced-alertify.html">Alertify</a></li>
                        <li><a href="advanced-rangeslider.html">Range Slider</a></li>
                    </ul>
                </li> --}}

            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>