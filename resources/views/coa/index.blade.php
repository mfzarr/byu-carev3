<x-app-layout :title="'Chart of Account'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body table-responsive pt-0">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h4 class="my-auto header-title">Data {{ $title }}</h4>

                            <a href="{{ route('coa.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                                <span class="icon text-white-50">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Data</span>
                            </a>
                        </div>
                        <!-- Alert success -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <!-- Akhir alert success -->

                        <!-- Alert success -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-warning">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <!-- Akhir alert success -->
                        <hr class="mt-0">
                        <table id="responsive-datatable"
                            class="table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Header Akun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coa as $d)
                                    <tr>
                                        <td>{{ $d->kode_akun }}</td>
                                        <td>{{ $d->nama_akun }}</td>
                                        <td>{{ $d->header_akun }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Header Akun</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div> <!-- end row -->

    </div> <!-- container-fluid -->
</x-app-layout>