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
                            {{-- <div class="mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('harga_satuan') border border-danger @enderror"
                                        id="harga_satuan" name="harga_satuan" placeholder="Contoh : 10000"
                                        value="{{ old('harga_satuan') }}" />
                                </div>
                                @if ($errors->has('harga_satuan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('harga_satuan') }}
                                    </div>
                                @endif
                            </div> --}}
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

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->

    @push('scripts')
        <script>
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
        </script>
    @endpush
</x-app-layout>
