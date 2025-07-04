<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pelanggan;
use App\Models\Pengambilan;
use App\Models\Penjualandetail;
use App\Models\Penjualanheader;
use App\Models\Diskon;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualan = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.subtotal) as total FROM penjualan_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
            ->select('a.id', 'a.no_penjualan', 'b.nama_pelanggan', 'c.daftar_barang', 'a.status_pembayaran', DB::raw('IFNULL(c.total, 0) as total'))
            ->get();

        return view('penjualan.index', ['penjualan' => $penjualan, 'title' => 'Penjualan Produk']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_penjualan = Autocode::code('penjualan_header', 'no_penjualan', 'PP');

        $pelanggan = Pelanggan::all();

        // $diskon = Diskon::where('barang_id', 

        return view('penjualan.create', ['no_penjualan' => $no_penjualan, 'pelanggan' => $pelanggan, 'title' => 'Tambah Penjualan Produk']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_penjualan' => 'required',
            'tgl_penjualan' => 'required',
            'keterangan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'no_penjualan.required' => 'Nomor Penjualan harus diisi',
            'tgl_penjualan.required' => 'Tanggal Penjualan harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            $penjualan = Penjualanheader::create([
                'no_penjualan' => $request->no_penjualan,
                'tgl_penjualan' => $request->tgl_penjualan,
                'keterangan' => $request->keterangan,
                'status_pembayaran' => 'lunas',
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('penjualan.detail', $penjualan->id)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penjualan = Penjualanheader::find($id);
        $pelanggan = Pelanggan::all();

        return view('penjualan.edit', ['penjualan' => $penjualan, 'pelanggan' => $pelanggan, 'title' => 'Edit Penjualan Produk']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_penjualan' => 'required',
            'keterangan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'tgl_penjualan.required' => 'Tanggal Penjualan harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            Penjualanheader::where('id', $id)->update([
                'tgl_penjualan' => $request->tgl_penjualan,
                'keterangan' => $request->keterangan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('penjualan.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Penjualanheader::destroy($id);

            return redirect()->route('penjualan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function detail($id)
    {
        $penjualan = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_penjualan', 'a.tgl_penjualan', 'a.keterangan', 'b.nama_pelanggan')
            ->where('a.id', $id)
            ->first();

        $penjualan_detail = DB::table('penjualan_detail as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin('diskon as d', 'a.id_diskon', '=', 'd.id')
            ->select('a.id', 'a.kuantitas', 'b.nama_barang', 'a.harga_satuan', 'a.diskon', 'a.subtotal', 'd.nama_diskon', 'd.persentase_diskon')
            ->where('a.id_penjualan_header', $id)
            ->get();

        $barang = DB::table('persediaan as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as c'), 'a.id', '=', 'c.id_persediaan')
            ->select('a.id_barang as id', 'b.nama_barang', 'b.harga_satuan', DB::raw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) as stok'))
            ->whereRaw('a.kuantitas - COALESCE(c.kuantitas, 0) > 0')
            ->groupBy('a.id_barang', 'b.nama_barang', 'b.harga_satuan')
            ->get();

        return view('penjualan.detail', ['penjualan' => $penjualan, 'penjualan_detail' => $penjualan_detail, 'barang' => $barang, 'title' => 'Detail Penjualan Produk']);
    }

    public function getDiskon(Request $request)
    {
        $id_barang = $request->id_barang;
        $total = $request->total;
        $tgl_penjualan = $request->tgl_penjualan ?? now()->format('Y-m-d');

        $query = Diskon::where('id_barang', $id_barang)
            ->where('min_transaksi', '<=', $total);

        // Tambahkan kondisi untuk diskon dengan periode
        $query->where(function ($q) use ($tgl_penjualan) {
            $q->whereNull('tanggal_mulai') // Diskon tanpa periode
                ->orWhere(function ($q2) use ($tgl_penjualan) {
                    // Diskon dengan periode yang sesuai
                    $q2->where('tanggal_mulai', '<=', $tgl_penjualan)
                        ->where('tanggal_selesai', '>=', $tgl_penjualan);
                });
        });

        $diskon = $query->orderBy('max_diskon', 'desc')
            ->first();

        if ($diskon) {
            $diskon_nominal = min(($total * $diskon->persentase_diskon / 100), $diskon->max_diskon);
            return response()->json([
                'success' => true,
                'diskon' => $diskon,
                'diskon_nominal' => $diskon_nominal,
                'subtotal' => $total - $diskon_nominal
            ]);
        }

        return response()->json([
            'success' => false,
            'diskon_nominal' => 0,
            'subtotal' => $total
        ]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'kuantitas' => 'required',
            'harga_satuan' => 'required',
            'id_barang' => 'required',
            'id_penjualan_header' => 'required',
        ], [
            'kuantitas.required' => 'Kuantitas harus diisi',
            'harga_satuan.required' => 'Harga Satuan harus diisi',
            'id_barang.required' => 'Barang harus diisi',
            'id_penjualan_header.required' => 'Penjualan Header harus diisi',
        ]);

        try {
            $stok = DB::table('persediaan as a')
                ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
                ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as c'), 'a.id', '=', 'c.id_persediaan')
                ->select('a.id_barang', 'b.nama_barang', DB::raw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) as stok'))
                ->where('a.id_barang', $request->id_barang)
                ->groupBy('a.id_barang', 'b.nama_barang')
                ->first()->stok;

            if ($request->kuantitas > $stok) {
                return redirect()->route('penjualan.detail', $request->id_penjualan_header)->with('error', 'Kuantitas penjualan melebihi stok tersedia');
            }

            $total = $request->kuantitas * $request->harga_satuan;


            $diskon = Diskon::where('id_barang', $request->id_barang)
                ->where('min_transaksi', '<=', $total)
                ->orderBy('max_diskon', 'desc')
                ->first();

            $diskon_nominal = 0;
            $id_diskon = null;

            if ($diskon) {
                $diskon_nominal = min(($total * $diskon->persentase_diskon / 100), $diskon->max_diskon);
                $id_diskon = $diskon->id;
            }

            $subtotal = $total - $diskon_nominal;

            $penjualan_detail = Penjualandetail::create([
                'kuantitas' => $request->kuantitas,
                'harga_satuan' => $request->harga_satuan,
                'diskon' => $diskon_nominal,
                'subtotal' => $subtotal,
                'id_barang' => $request->id_barang,
                'id_diskon' => $id_diskon,
                'id_penjualan_header' => $request->id_penjualan_header,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            $result = DB::table('persediaan as a')
                ->select('a.*', DB::raw('COALESCE(b.total_pengambilan, 0) as total_pengambilan'), DB::raw('a.kuantitas - COALESCE(b.total_pengambilan, 0) as saldo_stok'))
                ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as total_pengambilan FROM pengambilan GROUP BY id_persediaan) b'), 'a.id', '=', 'b.id_persediaan')
                ->where('a.id_barang', $request->id_barang)
                ->whereRaw('a.kuantitas - COALESCE(b.total_pengambilan, 0) > 0')
                ->orderBy('a.tgl_persediaan')
                ->get();
            $kuantitas_pengambilan = $request->kuantitas;

            foreach ($result as $r) {
                if ($kuantitas_pengambilan >= $r->saldo_stok && $kuantitas_pengambilan > 0) {
                    Pengambilan::create([
                        'tgl_pengambilan' => now(),
                        'keterangan' => 'Penjualan dengan Nomor Penjualan ' . $request->no_penjualan,
                        'kuantitas' => $r->saldo_stok,
                        'id_barang' => $request->id_barang,
                        'id_persediaan' => $r->id,
                        'id_penjualan_detail' => $penjualan_detail->id,
                        'user_id_created' => Auth::user()->id,
                        'user_id_updated' => Auth::user()->id,
                    ]);
                    $kuantitas_pengambilan -= $r->saldo_stok;
                } else {
                    if ($kuantitas_pengambilan < $r->saldo_stok && $kuantitas_pengambilan > 0) {
                        Pengambilan::create([
                            'tgl_pengambilan' => now(),
                            'keterangan' => 'Penjualan dengan Nomor Penjualan ' . $request->no_penjualan,
                            'kuantitas' => $kuantitas_pengambilan,
                            'id_barang' => $request->id_barang,
                            'id_persediaan' => $r->id,
                            'id_penjualan_detail' => $penjualan_detail->id,
                            'user_id_created' => Auth::user()->id,
                            'user_id_updated' => Auth::user()->id,
                        ]);
                        $kuantitas_pengambilan = 0;
                    }
                }
            }



            $check_akun_kas = Coa::where('kode_akun', '101')->first();

            $check_akun_diskon_penjualan = Coa::where('kode_akun', '411')->first();

            $check_akun_penjualan = Coa::where('kode_akun', '406')->first();

            if (!$check_akun_kas) {
                $coa_kas = Coa::create([
                    'kode_akun' => '101',
                    'nama_akun' => 'Kas',
                    'header_akun' => 1,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_diskon_penjualan) {
                $coa_diskon_penjualan = Coa::create([
                    'kode_akun' => '411',
                    'nama_akun' => 'Diskon Penjualan',
                    'header_akun' => 4,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_penjualan) {
                $coa_penjualan = Coa::create([
                    'kode_akun' => '406',
                    'nama_akun' => 'Penjualan',
                    'header_akun' => 4,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            $jurnal_debit = Jurnal::create([
                'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
                'tgl_jurnal' => now(),
                'posisi_dr_cr' => 'd',
                'nominal' => $subtotal, // Gunakan subtotal bukan total
                'jenis_transaksi' => 'penjualan',
                'id_transaksi' => $penjualan_detail->id,
                'id_coa' => !$check_akun_kas ? $coa_kas->id : $check_akun_kas->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            if ($diskon_nominal > 0) {
                Jurnal::create([
                    'no_jurnal' => $jurnal_debit->no_jurnal,
                    'tgl_jurnal' => now(),
                    'posisi_dr_cr' => 'd',
                    'nominal' => $diskon_nominal,
                    'jenis_transaksi' => 'penjualan',
                    'id_transaksi' => $penjualan_detail->id,
                    'id_coa' => !$check_akun_diskon_penjualan ? $coa_diskon_penjualan->id : $check_akun_diskon_penjualan->id,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => now(),
                'posisi_dr_cr' => 'c',
                'nominal' => $request->kuantitas * $request->harga_satuan,
                'jenis_transaksi' => 'penjualan',
                'id_transaksi' => $penjualan_detail->id,
                'id_coa' => !$check_akun_penjualan ? $coa_penjualan->id : $check_akun_penjualan->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('penjualan.detail', $request->id_penjualan_header)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.detail', $request->id_penjualan_header)->with('error', 'Data gagal disimpan: ' . $e->getMessage());
        }
    }

    public function destroydetail($id)
    {
        $penjualan_detail = Penjualandetail::find($id);

        try {
            Pengambilan::where('id_penjualan_detail', $id)->delete();
            Jurnal::where('jenis_transaksi', 'penjualan')->where('id_transaksi', $id)->delete();

            $penjualan_detail->delete();

            return redirect()->route('penjualan.detail', $penjualan_detail->id_penjualan_header)->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.detail', $penjualan_detail->id_penjualan_header)->with('error', 'Data gagal dihapus');
        }
    }
}
