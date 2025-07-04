@props(['title'])
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>{{ $title ?? 'Laravel' }}</title>
    <!-- Tambahkan meta tag CSRF di bagian head layout utama -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- third party css end -->

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<!-- body start -->

<body class="loading" data-layout-color="White" data-layout-mode="default" data-layout-size="fluid"
    data-topbar-color="White" data-leftbar-position="fixed" data-leftbar-color="White" data-leftbar-size='default'
    data-sidebar-user='true'>

    <!-- Begin page -->
    <div id="wrapper">


        <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topnav-menu float-end mb-0">
                <li class="dropdown notification-list">
                    <a href="{{ url('/home') }}" class="nav-link waves-effect">
                        <i class="fe-home"></i>
                        <span>Home</span>
                    </a>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('assets/images/profile/blank-profile.png') }}" alt="user-image"
                            class="rounded-circle">
                        <span class="pro-user-name ms-1">
                            {{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome, {{ Auth::user()->name }}! </h6>
                        </div>

                        <!-- item-->
                        <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>My Account</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                            <i class="fe-log-out"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </li>
            </ul>




            <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
                <li>
                    <button class="button-menu-mobile disable-btn waves-effect">
                        <i class="fe-menu"></i>
                    </button>
                </li>

                <li>
                    <h4 class="page-title-main">
                        @if (Request::url() == route('dashboard'))
                            Dashboard
                        @elseif (Request::url() == route('coa.index'))
                            Data Akun
                        @elseif (Request::url() == route('pelanggan.index'))
                            Data Pelanggan
                        @elseif (Request::url() == route('pegawai.index'))
                            Data Pegawai
                        @elseif (Request::url() == route('vendor.index'))
                            Data Vendor
                        @elseif (Request::url() == route('barang.index'))
                            Data Barang
                        @elseif (Request::url() == route('layanan.index'))
                            Data Layanan
                        @elseif (Request::url() == route('diskon.index'))
                            Data Diskon
                        @elseif (Request::url() == route('reservasi.index'))
                            Reservasi
                        @elseif (Request::url() == route('pembelian.index'))
                            Pembelian Produk
                        @elseif (Request::url() == route('penjualan.index'))
                            Penjualan Produk
                        @elseif (Request::url() == route('pendapatan.index'))
                            Pendapatan Jasa
                        @elseif (Request::url() == route('pengeluaran.index'))
                            Pengeluaran
                        @elseif (Request::url() == route('laporan.jurnalumum'))
                            Jurnal
                        @elseif (Request::url() == route('laporan.bukubesar'))
                            Buku Besar
                        @elseif (Request::url() == route('laporan.pembelian'))
                            Pembelian
                        @elseif (Request::url() == route('laporan.penjualan'))
                            Penjualan
                        @elseif (Request::url() == route('laporan.labarugi'))
                            Laba Rugi
                        @elseif (Request::url() == route('laporan.kartustok'))
                            Kartu Stok
                        @endif

                    </h4>
                </li>
            </ul>

            <div class="clearfix"></div>

        </div>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">

            <div class="h-100" data-simplebar>

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul id="side-menu">

                        <li class="menu-title">Navigation</li>

                        <li>
                            <a href="{{ route('dashboard') }}">
                                <i class="fa fa-tachometer-alt"></i> <!-- Ikon Font Awesome Dashboard -->
                                <span> Dashboard </span>
                            </a>
                        </li>

                        <li>
                            <a href="#masterdata" data-bs-toggle="collapse">
                                <i class="fa fa-database"></i> <!-- Ikon Font Awesome Database -->
                                <span> Master Data </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="masterdata">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="{{ route('coa.index') }}">Akun</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pelanggan.index') }}">Pelanggan</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pegawai.index') }}">Pegawai</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('vendor.index') }}">Vendor</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('barang.index') }}">Barang</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('layanan.index') }}">Layanan</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('diskon.index') }}">Diskon</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#transaksi" data-bs-toggle="collapse">
                                <i class="fa fa-exchange-alt"></i> <!-- Ikon Font Awesome Transaksi -->
                                <span> Transaksi </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="collapse" id="transaksi">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="{{ route('reservasi.index') }}">Reservasi</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pembelian.index') }}">Pembelian Produk</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('penjualan.index') }}">Penjualan Produk</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pendapatan.index') }}">Pendapatan Jasa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pengeluaran.index') }}">Pengeluaran</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#laporan" data-bs-toggle="collapse">
                                <i class="fa fa-chart-bar"></i> <!-- Ikon Font Awesome Laporan -->
                                <span> Laporan </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="collapse" id="laporan">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="{{ route('laporan.jurnalumum') }}">Jurnal</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.bukubesar') }}">Buku Besar</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.pembelian') }}">Laporan Pembelian</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.penjualan') }}">Laporan Penjualan</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.labarugi') }}">Laporan Laba Rugi</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.pengeluarankas') }}">Laporan Pengeluaran Kas</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.kartustok') }}">Kartu Stok</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->


        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                {{ $slot }}

            </div> <!-- content -->

            <!-- Footer Start -->
            <footer>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 mx-auto text-center">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> &copy; ZENITHA BEAUTY CARE
                        </div>
                    </div>
                </div>
            </footer>

            <!-- end Footer -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->

    <!-- Vendor -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- third party js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <!-- third party js ends -->

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Datatables init -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <!-- knob plugin -->
    <script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>

    <!-- Morris Chart -->
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <script src="{{ asset('assets/libs/raphael/raphael.min.js') }}"></script>

    <!-- App js-->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <script>
        document.querySelectorAll("input[inputmode='numeric']").forEach(function(input) {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
