<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Sistem CRUD')</title>

    <link rel="stylesheet" href="{{ asset('assets-template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets-template/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('assets-template/plugins/chart.js/Chart.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets-template/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <script src="{{ asset('assets-template/plugins/jquery/jquery.min.js') }}"></script>

    <style>
        /* Tampilan tabel sebagai kartu di layar mobile (max 767.98px) */
        @media (max-width: 767.98px) {
            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive tr,
            .table-responsive td {
                display: block;
                width: 100%;
            }

            .table-responsive thead {
                display: none;
            }

            .table-responsive tr {
                margin-bottom: 15px;
                padding: 8px;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                background: #ffffff;
            }

            .table-responsive td {
                position: relative;
                padding: 8px 10px 8px 45%;
                border: none;
                text-align: right;
                border-bottom: 1px dashed #e9ecef;
            }

            .table-responsive td:last-child {
                border-bottom: none;
            }

            .table-responsive td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                top: 50%;
                transform: translateY(-50%);
                font-weight: 700;
                text-align: left;
            }

            .table-responsive td.text-center {
                text-align: left;
                padding-left: 10px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 5px;
            }

            .table-responsive td.text-center::before {
                position: static;
                transform: none;
                width: 100%;
                margin-bottom: 4px;
            }

            .table-responsive td.text-center form.d-inline {
                display: inline-block;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('assets-template/dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Admin</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('assets-template/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::user()->nama ?? 'User' }}</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">Daftar Menu</li>

                    @if(in_array((string) Auth::user()->level, ['1', '2'], true))
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Data Barang</p>
                        </a>
                    </li>
                    @endif

                    @if(in_array((string) Auth::user()->level, ['1', '3'], true))
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Data Mahasiswa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pegawai.index') }}" class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-id-badge"></i>
                            <p>Data Pegawai</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('email.index') }}" class="nav-link {{ request()->routeIs('email.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Kirim Email</p>
                        </a>
                    </li>
                    @endif

                    @if((string) Auth::user()->level === '1')
                    <li class="nav-item">
                        <a href="{{ route('akun.index') }}" class="nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Data Akun</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger m-3">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <footer class="main-footer">
        <strong>Copyright &copy; 2026 Sistem CRUD.</strong> All rights reserved.
    </footer>
</div>

<script src="{{ asset('assets-template/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets-template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets-template/dist/js/adminlte.js') }}"></script>
<script src="{{ asset('assets-template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets-template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets-template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets-template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
$(function () {
    // Isi label kolom (data-label) untuk tampilan kartu di layar mobile
    $('.table-responsive table').each(function () {
        var $table = $(this);
        var $headers = $table.find('thead th');
        $table.find('tbody tr').each(function () {
            var $tds = $(this).find('td');
            $tds.each(function (i) {
                var label = $headers.eq(i).text().trim();
                if (label) $(this).attr('data-label', label);
            });
        });
    });

    if (document.getElementById('barangTable')) $('#barangTable').DataTable();
    if (document.getElementById('pegawaiTable')) $('#pegawaiTable').DataTable();
    if (document.getElementById('mahasiswaTable')) $('#mahasiswaTable').DataTable();
    if (document.getElementById('akunTable')) $('#akunTable').DataTable();
});
</script>
@stack('scripts')
</body>
</html>
