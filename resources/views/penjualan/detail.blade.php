<x-app-layout :title="'Detail Penjualan'">
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
                            <a class="btn btn-primary waves-effect waves-light" href="{{ route('penjualan.index') }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="sub-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <p class="m-t-10"><strong>Nomor Penjualan : </strong> {{ $penjualan->no_penjualan }}
                                    </p>
                                    <p class="m-t-10"><strong>Tanggal Penjualan : </strong>
                                        {{ date('d F Y', strtotime($penjualan->tgl_penjualan)) }}
                                    </p>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="m-t-10"><strong>Keterangan : </strong> {{ $penjualan->keterangan }} </p>
                                    <p class="m-t-10"><strong>Nama Pelanggan : </strong>
                                        {{ $penjualan->nama_pelanggan }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('penjualan.storedetail') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_penjualan_header" id="id_penjualan_header"
                            value="{{ $penjualan->id }}">
                        <input type="hidden" name="no_penjualan" id="no_penjualan"
                            value="{{ $penjualan->no_penjualan }}">
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Barang</label>
                            <select class="form-select @error('id_barang') border border-danger @enderror"
                                id="id_barang" name="id_barang">
                                <option value="" disabled selected> ==>> Pilih Barang <<== </option>
                                        @foreach ($barang as $v)
                                <option value="{{ $v->id }}" data-harga="{{ $v->harga_satuan }}">
                                    {{ $v->nama_barang }} - Stok {{ $v->stok }}
                                </option>
                                @endforeach

                            </select>
                            @if ($errors->has('id_barang'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('id_barang') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="kuantitas" class="form-label">Kuantitas</label>
                            <input type="text" inputmode="numeric"
                                class="form-control @error('kuantitas') border border-danger @enderror"
                                id="kuantitas_display" placeholder="Contoh : 10" value="{{ old('kuantitas') }}" />
                            <!-- Hidden field untuk menyimpan nilai asli kuantitas -->
                            <input type="hidden" name="kuantitas" id="kuantitas" value="{{ old('kuantitas') }}">
                            @if ($errors->has('kuantitas'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('kuantitas') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" inputmode="numeric"
                                    class="form-control @error('harga_satuan') border border-danger @enderror"
                                    id="harga_satuan_display" placeholder="Contoh : 10.000"
                                    value="{{ old('harga_satuan') }}" />
                                <!-- Hidden field untuk menyimpan nilai asli harga satuan -->
                                <input type="hidden" name="harga_satuan" id="harga_satuan"
                                    value="{{ old('harga_satuan') }}">
                            </div>
                            @if ($errors->has('harga_satuan'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('harga_satuan') }}
                                </div>
                            @endif
                        </div>

                        <div class="alert alert-success" id="diskon-alert" style="display: none;">
                            <p id="diskon-message"></p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="total" class="form-label">Total (Kuantitas × Harga)</label>
                                <input type="text" class="form-control" id="total" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="diskon_display" class="form-label">Diskon</label>
                                <input type="text" class="form-control" id="diskon_display" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="subtotal_display" class="form-label">Subtotal</label>
                                <input type="text" class="form-control bg-success text-white" id="subtotal_display"
                                    readonly>
                            </div>
                        </div>


                        <!-- untuk tombol simpan -->
                        <input class="btn btn-primary waves-effect waves-light" type="submit" value="Simpan">

                        <!-- untuk tombol batal simpan -->
                        <a class="btn btn-secondary waves-effect" href="{{ url('/penjualan') }}"
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
                                <th>Nama Barang</th>
                                <th>Kuantitas</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Diskon</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_keseluruhan = 0;
                            @endphp
                            @foreach ($penjualan_detail as $p)
                                <tr>
                                    <td>{{ $p->nama_barang }}</td>
                                    <td align="center">{{ $p->kuantitas }}</td>
                                    <td align="right">Rp. {{ number_format($p->harga_satuan, 2, ',', '.') }}</td>
                                    <td align="right">Rp.
                                        {{ number_format($p->harga_satuan * $p->kuantitas, 2, ',', '.') }}</td>
                                    <td align="right">
                                        @if ($p->diskon > 0)
                                            <span class="text-success">-Rp.
                                                {{ number_format($p->diskon, 2, ',', '.') }}</span>
                                            @if ($p->nama_diskon)
                                                <br><small class="text-muted">({{ $p->nama_diskon }})</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td align="right">
                                        <strong>Rp. {{ number_format($p->subtotal, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <a onclick="deleteConfirm(this); return false;" href="#"
                                            data-id="{{ $p->id }}"
                                            data-barang="{{ $p->nama_barang }} - {{ $p->kuantitas }} - Rp. {{ number_format($p->harga_satuan, 2, ',', '.') }}"
                                            class="btn btn-danger btn-circle">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $total_keseluruhan += $p->subtotal;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"><strong>Total Pembelian</strong></td>
                                <td align="right"><strong>Rp.
                                        {{ number_format($total_keseluruhan, 2, ',', '.') }}</strong></td>
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
            barang = e.getAttribute('data-barang');

            var url3 = "{{ url('penjualan/destroydetail/') }}";
            var url4 = url3.concat("/", id);
            tomboldelete.setAttribute("href", url4); //akan meload kontroller delete

            var pesan = "Data Barang <b>"
            var pesan2 = " </b>akan dihapus"
            var res = barang;
            document.getElementById("xid").innerHTML = pesan.concat(res, pesan2);

            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                keyboard: false
            });

            myModal.show();

        }
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectBarang = document.getElementById('id_barang');
            const inputHargaDisplay = document.getElementById('harga_satuan_display');
            const inputHarga = document.getElementById('harga_satuan');
            const inputKuantitasDisplay = document.getElementById('kuantitas_display');
            const inputKuantitas = document.getElementById('kuantitas');
            const inputTotal = document.getElementById('total');
            const inputDiskon = document.getElementById('diskon_display');
            const inputSubtotal = document.getElementById('subtotal_display');
            const diskonAlert = document.getElementById('diskon-alert');
            const diskonMessage = document.getElementById('diskon-message');

            // Format number with thousand separator
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Parse formatted number back to raw number
            function parseFormattedNumber(formattedNumber) {
                if (!formattedNumber) return 0;
                return parseInt(formattedNumber.replace(/\D/g, ''));
            }

            // Format harga satuan when user types
            inputHargaDisplay.addEventListener('input', function(e) {
                // Get the raw number from the input
                const rawValue = parseFormattedNumber(this.value);

                // Format the number and update the display
                this.value = formatNumber(rawValue);

                // Store the raw value in the hidden field
                inputHarga.value = rawValue;

                // Calculate total after formatting
                calculateTotal();
            });

            // Format kuantitas when user types
            inputKuantitasDisplay.addEventListener('input', function(e) {
                // Get the raw number from the input
                const rawValue = parseFormattedNumber(this.value);

                // Format the number and update the display
                this.value = formatNumber(rawValue);

                // Store the raw value in the hidden field
                inputKuantitas.value = rawValue;

                // Calculate total after formatting
                calculateTotal();
            });

            // Set harga saat barang dipilih
            selectBarang.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const harga = selected.getAttribute('data-harga');

                if (harga) {
                    // Store the raw value in the hidden field
                    inputHarga.value = harga;

                    // Format the price and display it
                    inputHargaDisplay.value = formatNumber(harga);
                } else {
                    inputHargaDisplay.value = '';
                    inputHarga.value = '';
                }

                calculateTotal();
            });

            function calculateTotal() {
                const kuantitas = parseInt(inputKuantitas.value) || 0;
                const harga = parseInt(inputHarga.value) || 0;
                const total = kuantitas * harga;
                const idBarang = selectBarang.value;

                inputTotal.value = 'Rp. ' + formatNumber(total);

                if (total > 0 && idBarang) {
                    // Panggil API untuk cek diskon
                    fetch('{{ route('penjualan.getDiskon') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                id_barang: idBarang,
                                total: total
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const diskonNominal = data.diskon_nominal;
                                const subtotal = data.subtotal;

                                inputDiskon.value = 'Rp. ' + formatNumber(diskonNominal);
                                inputSubtotal.value = 'Rp. ' + formatNumber(subtotal);

                                // Tambahkan tombol tutup ke notifikasi diskon
                                diskonMessage.innerHTML =
                                    `Selamat! Anda mendapat diskon <strong>${data.diskon.nama_diskon}</strong> sebesar <strong>Rp. ${formatNumber(diskonNominal)}</strong> dari total <strong>Rp. ${formatNumber(total)}</strong> <button type="button" class="btn-close float-end" aria-label="Close" onclick="document.getElementById('diskon-alert').style.display='none'"></button>`;
                                diskonAlert.style.display = 'block';
                            } else {
                                inputDiskon.value = 'Rp. 0';
                                inputSubtotal.value = 'Rp. ' + formatNumber(total);
                                diskonAlert.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            inputDiskon.value = 'Rp. 0';
                            inputSubtotal.value = 'Rp. ' + formatNumber(total);
                            diskonAlert.style.display = 'none';
                        });
                } else {
                    inputDiskon.value = 'Rp. 0';
                    inputSubtotal.value = 'Rp. ' + formatNumber(total);
                    diskonAlert.style.display = 'none';
                }
            }
        });
    </script>
</x-app-layout>
