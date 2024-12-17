
<!DOCTYPE html>
<html>
<head>
@include('admin.dashboard.component.head')
</head>
<body>
    <div id="wrapper">
        @include('admin.dashboard.component.sidebar')

        <div id="page-wrapper" class="gray-bg">
        @include('admin.dashboard.component.nav')
        <div class="row mt20">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        @include('admin.dashboard.component.footer')
        </div>
    </div>
    @include('admin.dashboard.component.scripts')
</body>
</html>
