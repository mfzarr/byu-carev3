<x-app-layout :title="'Detail Pendapatan'">
    <div class="container-fluid">
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
        <div class="row">
            <!-- DataTales Example -->
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <h1 class="h3 mb-2 text-gray-800 text-primary">Data {{ $title }}</h1>
                        </div>
                        <div class="float-end">
                            <a class="btn btn-primary waves-effect waves-light" href="{{ route('pendapatan.index') }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="sub-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <p class="m-t-10"><strong>Nomor Pendapatan : </strong>
                                        {{ $pendapatan->no_pendapatan }}
                                    </p>
                                    <p class="m-t-10"><strong>Tanggal Pendapatan : </strong>
                                        {{ date('d F Y', strtotime($pendapatan->tgl_pendapatan)) }}
                                    </p>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="m-t-10"><strong>Nama Pelanggan : </strong>
                                        {{ $pendapatan->nama_pelanggan }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('pendapatan.storedetail') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pendapatan_header" id="id_pendapatan_header"
                            value="{{ $pendapatan->id }}">
                        <div class="mb-3">
                            <label for="id_reservasi" class="form-label">Reservasi</label>
                            <select class="form-select @error('id_reservasi') border border-danger @enderror"
                                id="id_reservasi" name="id_reservasi">
                                <option value="" disabled selected> ==>> Pilih Reservasi <<== </option>
                                        @if (isset($reservations) && count($reservations) > 0)
                                            @foreach ($reservations as $reservation)
                                <option value="{{ $reservation->id }}" data-harga="{{ $reservation->harga }}"
                                    data-layanan="{{ $reservation->nama_layanan }}"
                                    data-id-layanan="{{ $reservation->id_layanan }}">
                                    {{ $reservation->no_reservasi }} -
                                    {{ $reservation->ruangan }}.
                                    {{ date('H:i', strtotime($reservation->waktu_mulai)) }} -
                                    {{ date('H:i', strtotime($reservation->waktu_selesai)) }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @if ($errors->has('id_reservasi'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('id_reservasi') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="nama_layanan" class="form-label">Nama Layanan</label>
                            <input type="text"
                                class="form-control @error('nama_layanan') border border-danger @enderror"
                                id="nama_layanan" name="nama_layanan" placeholder="Nama layanan akan terisi otomatis"
                                value="{{ old('nama_layanan') }}" readonly />
                            <!-- Hidden field for id_layanan -->
                            <input type="hidden" id="id_layanan" name="id_layanan" value="{{ old('id_layanan') }}">
                            @if ($errors->has('nama_layanan'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('nama_layanan') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text"
                                    class="form-control @error('harga') border border-danger @enderror" id="harga"
                                    placeholder="Harga akan terisi otomatis" value="{{ old('harga') }}" readonly />
                                <!-- Hidden field for raw harga value -->
                                <input type="hidden" id="harga-raw" name="harga" value="{{ old('harga') }}">
                            </div>
                            @if ($errors->has('harga'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('harga') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="diskon" class="form-label">Diskon</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text"
                                    class="form-control @error('diskon') border border-danger @enderror" id="diskon"
                                    placeholder="Contoh : 10.000" value="{{ old('diskon') }}" />
                                <!-- Hidden field for raw diskon value -->
                                <input type="hidden" id="diskon-raw" name="diskon" value="{{ old('diskon', '0') }}">
                            </div>
                            @if ($errors->has('diskon'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('diskon') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_diskon" class="form-label">Keterangan Diskon</label>
                            <input type="text"
                                class="form-control @error('keterangan_diskon') border border-danger @enderror"
                                id="keterangan_diskon" name="keterangan_diskon" placeholder="Keterangan diskon"
                                value="{{ old('keterangan_diskon') }}" />
                            @if ($errors->has('keterangan_diskon'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('keterangan_diskon') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text"
                                    class="form-control @error('subtotal') border border-danger @enderror"
                                    id="subtotal" placeholder="Subtotal akan terisi otomatis"
                                    value="{{ old('subtotal') }}" readonly />
                                <!-- Hidden field for raw subtotal value -->
                                <input type="hidden" id="subtotal-raw" name="subtotal"
                                    value="{{ old('subtotal', '0') }}">
                            </div>
                            @if ($errors->has('subtotal'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('subtotal') }}
                                </div>
                            @endif
                        </div>

                        <!-- untuk tombol simpan -->
                        <input class="btn btn-primary waves-effect waves-light" type="submit" value="Simpan">

                        <!-- untuk tombol batal simpan -->
                        <a class="btn btn-secondary waves-effect" href="{{ url('/pendapatan') }}"
                            role="button">Batal</a>

                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr align="center">
                                <th>No Reservasi</th>
                                <th>Layanan</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Keterangan Diskon</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($pendapatan_detail as $p)
                                <tr>
                                    <td>{{ $p->no_reservasi }}</td>
                                    <td>{{ $p->nama_layanan }}</td>
                                    <td align="right">Rp. {{ number_format($p->harga, 0, ',', '.') }}</td>
                                    <td align="right">Rp. {{ number_format($p->diskon, 0, ',', '.') }}</td>
                                    <td>{{ $p->keterangan_diskon }}</td>
                                    <td align="right">Rp. {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                                    <td>
                                        <a onclick="deleteConfirm(this); return false;" href="#"
                                            data-id="{{ $p->id }}" data-keterangan="{{ $p->nama_layanan }}"
                                            class="btn btn-danger btn-circle">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $total += $p->subtotal;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"><strong>Total Pembelian</strong></td>
                                <td align="right"><strong>Rp. {{ number_format($total, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Delete --}}
    <script>
        function deleteConfirm(e) {
            var tomboldelete = document.getElementById('btn-delete')
            id = e.getAttribute('data-id');
            keterangan = e.getAttribute('data-keterangan');

            var url3 = "{{ url('pendapatan/destroydetail/') }}";
            var url4 = url3.concat("/", id);
            tomboldelete.setAttribute("href", url4); //akan meload kontroller delete

            var pesan = "Data Barang <b>"
            var pesan2 = " </b>akan dihapus"
            var res = keterangan;
            document.getElementById("xid").innerHTML = pesan.concat(res, pesan2);

            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                keyboard: false
            });

            myModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectReservasi = document.getElementById('id_reservasi');
            const inputHarga = document.getElementById('harga');
            const inputNamaLayanan = document.getElementById('nama_layanan');
            const inputIdLayanan = document.getElementById('id_layanan');
            const inputDiskon = document.getElementById('diskon');
            const inputSubtotal = document.getElementById('subtotal');

            // Format number with thousand separator
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Parse formatted number back to raw number
            function parseFormattedNumber(formattedNumber) {
                if (!formattedNumber) return 0;
                return parseInt(formattedNumber.replace(/\D/g, ''));
            }

            // Calculate and update subtotal
            function updateSubtotal() {
                const harga = parseFormattedNumber(inputHarga.value) || 0;
                const diskon = parseFormattedNumber(inputDiskon.value) || 0;
                const subtotal = Math.max(0, harga - diskon); // Ensure subtotal is not negative

                // Display formatted subtotal
                inputSubtotal.value = formatNumber(subtotal);

                // Store raw value in a hidden field for form submission
                document.getElementById('subtotal-raw').value = subtotal;
            }

            // Format input when user types in diskon field
            inputDiskon.addEventListener('input', function(e) {
                // Get the raw number from the input
                const rawValue = parseFormattedNumber(this.value);

                // Format the number and update the display
                this.value = formatNumber(rawValue);

                // Update the hidden field with raw value
                document.getElementById('diskon-raw').value = rawValue;

                // Update subtotal
                updateSubtotal();
            });

            // Update price, service name, and calculate subtotal when reservation is selected
            selectReservasi.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const harga = selected.getAttribute('data-harga');
                const layanan = selected.getAttribute('data-layanan');
                const idLayanan = selected.getAttribute('data-id-layanan');

                if (harga) {
                    // Set the raw price in hidden field
                    document.getElementById('harga-raw').value = harga;

                    // Format and display the price
                    inputHarga.value = formatNumber(harga);

                    // Set the service name
                    inputNamaLayanan.value = layanan;

                    // Set the service ID in the hidden field
                    inputIdLayanan.value = idLayanan;

                    // Reset diskon field
                    inputDiskon.value = '';
                    document.getElementById('diskon-raw').value = '0';

                    // Update subtotal
                    updateSubtotal();
                } else {
                    inputHarga.value = '';
                    document.getElementById('harga-raw').value = '0';
                    inputNamaLayanan.value = '';
                    inputIdLayanan.value = '';
                    inputDiskon.value = '';
                    document.getElementById('diskon-raw').value = '0';
                    inputSubtotal.value = '';
                    document.getElementById('subtotal-raw').value = '0';
                }
            });
        });
    </script>

    <!-- Logout Delete Confirmation-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah anda yakin?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="xid"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <a id="btn-delete" class="btn btn-danger" href="#">Hapus</a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
