<x-app-layout :title="'Tambah Barang'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text"
                                    class="form-control @error('kode_barang') border border-danger @enderror"
                                    id="kode_barang" name="kode_barang" value="{{ $kode_barang }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text"
                                    class="form-control @error('nama_barang') border border-danger @enderror"
                                    id="nama_barang" name="nama_barang" placeholder="Contoh : Body Lotion"
                                    value="{{ old('nama_barang') }}" />
                                @if ($errors->has('nama_barang'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_barang') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" 
                                        class="form-control @error('harga_satuan') border border-danger @enderror"
                                        id="harga_satuan" name="harga_satuan" placeholder="Contoh : 10.000"
                                        value="{{ old('harga_satuan') }}" />
                                </div>
                                @if ($errors->has('harga_satuan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('harga_satuan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="persentase" class="form-label">Persentase Keuntungan (%)</label>
                                <div class="input-group">
                                    <input type="number" 
                                        class="form-control"
                                        id="persentase" name="persentase" placeholder="Contoh : 20"/>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" 
                                        class="form-control @error('harga_jual') border border-danger @enderror"
                                        id="harga_jual" name="harga_jual" placeholder="Akan terisi otomatis"
                                        value="{{ old('harga_jual') }}" readonly />
                                </div>
                                @if ($errors->has('harga_jual'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('harga_jual') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <img src="{{ asset('assets/images/barang/dummy-image.png') }}" alt="dummy-image.png"
                                    id="gambar_barang_preview" class="img-fluid" style="max-width: 289px" />
                            </div>
                            <div class="mb-3">
                                <label for="gambar_barang" class="form-label">Gambar Barang</label>
                                <input type="file"
                                    class="form-control @error('gambar_barang') border border-danger @enderror"
                                    id="gambar_barang" name="gambar_barang" value="{{ old('gambar_barang') }}" />
                                @if ($errors->has('gambar_barang'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('gambar_barang') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_vendor" class="form-label">Vendor</label>
                                <select class="form-select @error('id_vendor') border border-danger @enderror"
                                    id="id_vendor" name="id_vendor">
                                    <option value="" disabled selected> ==>> Pilih Vendor <<== </option>
                                            @foreach ($vendor as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_vendor'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_vendor') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hargaSatuan = document.getElementById('harga_satuan');
            const persentase = document.getElementById('persentase');
            const hargaJual = document.getElementById('harga_jual');
            const form = document.querySelector('form');

            // Format number with thousand separators
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Parse formatted number back to integer
            function parseNumber(str) {
                return parseInt(str.replace(/\./g, '')) || 0;
            }

            // Format on initial load
            if (hargaSatuan.value) {
                hargaSatuan.value = formatNumber(parseNumber(hargaSatuan.value));
            }
            if (hargaJual.value) {
                hargaJual.value = formatNumber(parseNumber(hargaJual.value));
            }

            // Format harga satuan as user types
            hargaSatuan.addEventListener('input', function(e) {
                // Store cursor position
                const cursorPos = this.selectionStart;
                const originalLength = this.value.length;

                // Remove non-numeric characters
                let value = this.value.replace(/[^\d]/g, '');

                // Format the number
                if (value) {
                    this.value = formatNumber(value);
                    // Adjust cursor position
                    const newLength = this.value.length;
                    const posDiff = newLength - originalLength;
                    this.setSelectionRange(cursorPos + posDiff, cursorPos + posDiff);
                }

                // Calculate harga jual
                calculateHargaJual();
            });

            // Calculate harga jual when persentase changes
            persentase.addEventListener('input', calculateHargaJual);

            // Calculate harga jual function
            function calculateHargaJual() {
                if (hargaSatuan.value && persentase.value) {
                    const harga = parseNumber(hargaSatuan.value);
                    const percent = parseFloat(persentase.value);
                    const result = harga + (harga * percent / 100);
                    hargaJual.value = formatNumber(Math.round(result));
                }
            }

            // Remove formatting before form submission
            form.addEventListener('submit', function() {
                hargaSatuan.value = parseNumber(hargaSatuan.value);
                hargaJual.value = parseNumber(hargaJual.value);
            });

            // Image preview
            document.getElementById('gambar_barang').addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        document.getElementById('gambar_barang_preview').setAttribute('src', this.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>