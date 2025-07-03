<x-app-layout :title="'Edit Diskon'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('diskon.update', $diskon->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="kode_diskon" class="form-label">Kode Diskon</label>
                                <input type="text"
                                    class="form-control @error('kode_diskon') border border-danger @enderror"
                                    id="kode_diskon" name="kode_diskon" value="{{ $diskon->kode_diskon }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_diskon" class="form-label">Nama Diskon</label>
                                <input type="text"
                                    class="form-control @error('nama_diskon') border border-danger @enderror"
                                    id="nama_diskon" name="nama_diskon" placeholder="Contoh : Diskon Akhir Tahun"
                                    value="{{ $diskon->nama_diskon }}" />
                                @if ($errors->has('nama_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_diskon') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="min_transaksi_display" class="form-label">Minimal Diskon</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('min_transaksi') border border-danger @enderror"
                                        id="min_transaksi_display" placeholder="Contoh : 100.000"
                                        value="{{ number_format($diskon->min_transaksi, 0, ',', '.') }}" />
                                    <input type="hidden" name="min_transaksi" id="min_transaksi"
                                        value="{{ $diskon->min_transaksi }}" />
                                </div>
                                @if ($errors->has('min_transaksi'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('min_transaksi') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="persentase_diskon_display" class="form-label">Persentase Diskon</label>
                                <div class="input-group">
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('persentase_diskon') border border-danger @enderror"
                                        id="persentase_diskon_display" placeholder="Contoh : 10"
                                        value="{{ $diskon->persentase_diskon }}" />
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="hidden" name="persentase_diskon" id="persentase_diskon"
                                    value="{{ $diskon->persentase_diskon }}" />
                                @if ($errors->has('persentase_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('persentase_diskon') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="max_diskon_display" class="form-label">Maksimal Diskon</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('max_diskon') border border-danger @enderror"
                                        id="max_diskon_display" placeholder="Contoh : 50.000"
                                        value="{{ number_format($diskon->max_diskon, 0, ',', '.') }}" />
                                    <input type="hidden" name="max_diskon" id="max_diskon"
                                        value="{{ $diskon->max_diskon }}" />
                                </div>
                                @if ($errors->has('max_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('max_diskon') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_barang" class="form-label">Barang</label>
                                <select class="form-select @error('id_barang') border border-danger @enderror"
                                    id="id_barang" name="id_barang">
                                    <option value="" disabled> ==>> Pilih Barang <<== </option>
                                            @foreach ($barang as $b)
                                    <option value="{{ $b->id }}"
                                        {{ $diskon->id_barang == $b->id ? 'selected' : '' }}>{{ $b->nama_barang }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_barang'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_barang') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Simpan" />
                            <a href="{{ route('diskon.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->

    @push('scripts')
        <script>
            // Format angka dengan titik sebagai pemisah ribuan
            function formatNumber(number) {
                return number.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Menghapus format dan mengembalikan angka saja
            function unformatNumber(formattedNumber) {
                return formattedNumber.toString().replace(/\./g, "");
            }

            // Minimal Transaksi
            document.getElementById('min_transaksi_display').addEventListener('input', function(e) {
                let value = unformatNumber(this.value);
                this.value = formatNumber(value);
                document.getElementById('min_transaksi').value = value;
            });

            // Maksimal Diskon
            document.getElementById('max_diskon_display').addEventListener('input', function(e) {
                let value = unformatNumber(this.value);
                this.value = formatNumber(value);
                document.getElementById('max_diskon').value = value;
            });

            // Persentase Diskon
            document.getElementById('persentase_diskon_display').addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, "");
                if (value > 100) value = 100;
                this.value = value;
                document.getElementById('persentase_diskon').value = value;
            });

            // Submit form
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('min_transaksi').value = unformatNumber(document.getElementById(
                    'min_transaksi_display').value);
                document.getElementById('max_diskon').value = unformatNumber(document.getElementById(
                    'max_diskon_display').value);
                document.getElementById('persentase_diskon').value = document.getElementById(
                    'persentase_diskon_display').value;
            });
        </script>
    @endpush
</x-app-layout>
