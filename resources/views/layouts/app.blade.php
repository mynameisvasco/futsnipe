<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/line-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="la la-soccer-ball-o"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>{{ env('APP_NAME') }}<br></span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="nav navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Dashboard') active @endif"" href="/dashboard"><i class="fas fa-rocket"></i><span>&nbsp; Dashboard</span></a></li>
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        <p class="mb-0">Auto Buyer</p>
                    </div>
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Items') active @endif" href="/items"><i class="fas fa-sd-card"></i><span>&nbsp;&nbsp;Items</span></a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Transactions') active @endif" href="/transactions"><i class="fas fa-history"></i><span>&nbsp;&nbsp;Transactions</span></a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Configurations') active @endif" href="/configurations"><i class="fas fa-cog"></i><span>&nbsp;&nbsp;Configurations</span></a></li>
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        <p class="mb-0">Miscellaneous</p>
                    </div>
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Accounts') active @endif" href="/accounts"><i class="fas fa-address-book"></i><span>&nbsp;&nbsp;&nbsp;Accounts</span></a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link @if($pageName == 'Logs') active @endif" href="/logs"><i class="fas fa-terminal"></i><span>&nbsp;Logs</span></a></li>
                    <hr class="sidebar-divider">
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="nav navbar-nav flex-nowrap ml-auto">
                            <li class="nav-item dropdown no-arrow" role="presentation">
                                <li class="nav-item dropdown no-arrow">
                                    <a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#">
                                        <span class="d-none d-lg-inline mr-2 text-gray-600 small">{{ auth()->user()->name }}</span>
                                        <img class="border rounded-circle img-profile" src="https://i.imgur.com/xXlyX8w.png">
                                    </a>
                                    <div class="dropdown-menu shadow dropdown-menu-right animated--grow-in" role="menu">
                                        <a class="dropdown-item" role="presentation" href="/logout"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Logout</a>
                                </li>
                            </li>
                        </ul>
                    </div>
                </nav>
                @yield('content')
                </div>
                <footer class="bg-white sticky-footer">
                    <div class="container my-auto">
                        <div class="text-center my-auto copyright"><span>Copyright © FUTSnipe 2019</span></div>
                    </div>
                </footer>
            </div>
            <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
    </div>
    @if(session('notify'))
        <script type="text/javascript">
        new Noty({
            type: '{{ session('notify')['type'] }}',
            layout: 'topRight',
            theme: 'metroui',
            timeout: '2500',
            text: '<em class="{{ session('notify')['icon'] }}"></em> {{ session('notify')['message'] }}'
        }).show();
        </script>
    @endif
    @if ($errors->any())
        @foreach($errors->all() as $error)
            <script type="text/javascript">
                new Noty({
                    type: 'error',
                    layout: 'topRight',
                    theme: 'metroui',
                    timeout: '2500',
                    text: '<em class="icon-close"></em> {!! $error !!}'
                }).show();
            </script>
        @endforeach
    @endif
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/bs-charts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
    </script>
</body>

</html>