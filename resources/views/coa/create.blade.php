<x-app-layout :title="'Tambah Chart of Account'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('coa.store') }}" onsubmit="return confirmTambahCoa();">
                        @csrf
                            <div class="mb-3">
                                <label for="kode_akun" class="form-label">Kode Akun</label>
                                <input type="text"
                                    class="form-control @error('kode_akun') border border-danger @enderror"
                                    id="kode_akun" name="kode_akun" placeholder="Contoh : 111"
                                    value="{{ old('kode_akun') }}" />
                                @if ($errors->has('kode_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('kode_akun') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="header_akun" class="form-label">Header Akun</label>
                                <input type="text"
                                    class="form-control @error('header_akun') border border-danger @enderror"
                                    id="header_akun" name="header_akun" placeholder="Contoh : 1"
                                    value="{{ old('header_akun') }}" />
                                @if ($errors->has('header_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('header_akun') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="nama_akun" class="form-label">Nama Akun</label>
                                <input type="text"
                                    class="form-control @error('nama_akun') border border-danger @enderror"
                                    id="nama_akun" name="nama_akun" placeholder="Contoh : Kas"
                                    value="{{ old('nama_akun') }}" />
                                @if ($errors->has('nama_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_akun') }}
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary" onclick="showCoaWarning()">Tambah</button>
                            <a href="{{ route('coa.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showCoaWarning() {
        Swal.fire({
            title: 'Peringatan!',
            text: 'Setelah Chart of Account ditambahkan, data TIDAK DAPAT diubah atau dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('form').submit();
            }
        });
    }
</script>


