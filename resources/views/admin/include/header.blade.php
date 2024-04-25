<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        @php
            $admin=Auth::guard('admin')->user();
            $rs_fetch = Illuminate\Support\Facades\DB::select(DB::raw("SELECT `name` from `roles` where `id` = $admin->role_id limit 1;"));
        @endphp
        <li class="nav-item">
            <strong style="margin-top: 10px">Welcome : {{$admin->user_name}} :: {{@$rs_fetch[0]->name}}</strong>
            <a class="btn btn-lg" title="Logout" id="btn_logout" href="{{ route('admin.logout.get') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"> Logout</i>
            </a>
            <form id="logout-form" action="{{ route('admin.logout.get') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
