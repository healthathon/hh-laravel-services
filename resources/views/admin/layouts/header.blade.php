<!--**********************************
Nav header start
***********************************-->
<div class="nav-header">
    <div class="brand-logo">
        <a href="{{ route("admin.home") }}"><b><img src="{{asset('images/hhlogo.png')}}" height="60" width="60" alt="">
            </b></a>
    </div>
    <div class="nav-control">
        <div class="hamburger"><span class="line"></span> <span class="line"></span> <span class="line"></span>
        </div>
    </div>
</div>
<!--**********************************
    Nav header end
***********************************-->

<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <div class="header-right">
            <ul>
                <li class="icons">
                    <a href="javascript:void(0)" class="log-user">
                        <img src="{{asset('images/hhlogo.png')}}" alt=""> <span>Admin</span> <i
                                class="fa fa-caret-down f-s-14" aria-hidden="true"></i>
                    </a>
                    <div class="drop-down dropdown-profile animated bounceInDown">
                        <div class="dropdown-content-body">
                            <ul>
                                <li><a href="{{ route('admin.logout') }}"><i class="icon-power"></i> <span>Logout</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->