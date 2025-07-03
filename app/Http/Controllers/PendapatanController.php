<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pelanggan;
use App\Models\Pendapatandetail;
use App\Models\Pendapatanheader;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendapatan = DB::table('pendapatan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_pendapatan', 'a.tgl_pendapatan', 'b.nama_pelanggan')
            ->get();

        foreach ($pendapatan as $item) {
            $reservations = DB::table('pendapatan_detail as pd')
                ->join('reservasi as r', 'pd.id_reservasi', '=', 'r.id')
                ->where('pd.id_pendapatan_header', '=', $item->id)
                ->pluck('r.no_reservasi')  // Remove the status filter since it should show all reservations
                ->toArray();

            $item->no_reservasi = !empty($reservations) ? implode(', ', $reservations) : '-';

            // Calculate total subtotal for this pendapatan header
            $total_subtotal = DB::table('pendapatan_detail')
                ->where('id_pendapatan_header', $item->id)
                ->sum('subtotal');

            $item->total_subtotal = $total_subtotal ?: 0;
        }

        return view('pendapatan.index', ['pendapatan' => $pendapatan, 'title' => 'Pendapatan']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_pendapatam = Autocode::code('pendapatan_header', 'no_pendapatan', 'PDT');

        $pelanggan = Pelanggan::all();

        return view('pendapatan.create', ['no_pendapatan' => $no_pendapatam, 'pelanggan' => $pelanggan, 'title' => 'Tambah Pendapatan']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pendapatan' => 'required',
            'tgl_pendapatan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'no_pendapatan.required' => 'Nomor Pendapatan harus diisi',
            'tgl_pendapatan.required' => 'Tanggal Pendapatan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            $pendapatan = Pendapatanheader::create([
                'no_pendapatan' => $request->no_pendapatan,
                'tgl_pendapatan' => $request->tgl_pendapatan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pendapatan.detail', $pendapatan->id)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pendapatan = Pendapatanheader::find($id);

        $pelanggan = Pelanggan::all();

        return view('pendapatan.edit', ['pendapatan' => $pendapatan, 'pelanggan' => $pelanggan, 'title' => 'Edit Pendapatan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_pendapatan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'tgl_pendapatan.required' => 'Tanggal Pendapatan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            Pendapatanheader::where('id', $id)->update([
                'tgl_pendapatan' => $request->tgl_pendapatan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pendapatan.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')->with('error', 'Data gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Pendapatanheader::destroy($id);

            return redirect()->route('pendapatan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function detail($id)
    {
        $pendapatan = DB::table('pendapatan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_pendapatan', 'a.tgl_pendapatan', 'b.nama_pelanggan', 'b.id as id_pelanggan')
            ->where('a.id', $id)
            ->first();

        $reservations = DB::table('reservasi as r')
            ->join('layanan as l', 'r.id_layanan', '=', 'l.id')
            ->leftJoin('pendapatan_detail as pd', 'r.id', '=', 'pd.id_reservasi')
            ->where('r.id_pelanggan', $pendapatan->id_pelanggan)
            ->where('r.status', '=', 'Disetujui')
            ->whereNull('pd.id_reservasi') // Only get reservations not already in pendapatan_detail
            ->select('r.id', 'r.no_reservasi', 'l.nama_layanan', 'r.ruangan', 'r.waktu_mulai', 'r.waktu_selesai', 'l.harga_layanan as harga', 'l.id as id_layanan')
            ->get();

        $pendapatan_detail = DB::table('pendapatan_detail as pd')
            ->leftJoin('reservasi as r', 'pd.id_reservasi', '=', 'r.id')
            ->leftJoin('layanan as l', 'pd.id_layanan', '=', 'l.id')
            ->where('pd.id_pendapatan_header', $id)
            ->select('pd.*', 'r.no_reservasi', 'l.nama_layanan')
            ->get();

        return view('pendapatan.detail', [
            'pendapatan' => $pendapatan,
            'pendapatan_detail' => $pendapatan_detail,
            'reservations' => $reservations,
            'title' => 'Detail Pendapatan'
        ]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'id_reservasi' => 'required',
            'harga' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'keterangan_diskon' => 'nullable|string',
            'subtotal' => 'required|numeric',
            'id_layanan' => 'required',
        ], [
            'id_reservasi.required' => 'Reservasi harus diisi',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'diskon.numeric' => 'Diskon harus berupa angka',
            'keterangan_diskon.string' => 'Keterangan Diskon harus berupa teks',
            'subtotal.required' => 'Subtotal harus diisi',
            'subtotal.numeric' => 'Subtotal harus berupa angka',
            'id_layanan.required' => 'Layanan harus diisi',
        ]);

        try {
            $harga = $request->harga;
            $diskon = $request->diskon ?? 0;
            $subtotal = $harga - $diskon;

            $pendapatan_detail = Pendapatandetail::create([
                'id_reservasi' => $request->id_reservasi,
                'harga' => $harga,
                'diskon' => $diskon,
                'keterangan_diskon' => $request->keterangan_diskon ?? '',
                'subtotal' => $subtotal,
                'id_layanan' => $request->id_layanan,
                'id_pendapatan_header' => $request->id_pendapatan_header,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            // Update reservation status from "Disetujui" to "Selesai"
            DB::table('reservasi')
                ->where('id', $request->id_reservasi)
                ->update([
                    'status' => 'Selesai',
                    'user_id_updated' => Auth::user()->id,
                    'updated_at' => now()
                ]);

            $check_akun_kas = Coa::where('kode_akun', '101')->first();
            $check_akun_diskon_pendapatan = Coa::where('kode_akun', '412')->first();
            $check_akun_pendapatan = Coa::where('kode_akun', '401')->first();

            if (!$check_akun_kas) {
                $coa_kas = Coa::create([
                    'kode_akun' => '101',
                    'nama_akun' => 'Kas',
                    'header_akun' => 1,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_diskon_pendapatan) {
                $coa_diskon_pendapatan = Coa::create([
                    'kode_akun' => '412',
                    'nama_akun' => 'Diskon Pendapatan',
                    'header_akun' => 4,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_pendapatan) {
                $coa_pendapatan = Coa::create([
                    'kode_akun' => '401',
                    'nama_akun' => 'Pendapatan',
                    'header_akun' => 4,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            $jurnal_debit = Jurnal::create([
                'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
                'tgl_jurnal' => now(),
                'posisi_dr_cr' => 'd',
                'nominal' => $request->subtotal,
                'jenis_transaksi' => 'pendapatan',
                'id_transaksi' => $pendapatan_detail->id,
                'id_coa' => !$check_akun_kas ? $coa_kas->id : $check_akun_kas->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            if ($request->diskon > 0) {
                Jurnal::create([
                    'no_jurnal' => $jurnal_debit->no_jurnal,
                    'tgl_jurnal' => now(),
                    'posisi_dr_cr' => 'd',
                    'nominal' => $request->diskon,
                    'jenis_transaksi' => 'pendapatan',
                    'id_transaksi' => $pendapatan_detail->id,
                    'id_coa' => !$check_akun_diskon_pendapatan ? $coa_diskon_pendapatan->id : $check_akun_diskon_pendapatan->id,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => now(),
                'posisi_dr_cr' => 'c',
                'nominal' => $request->harga,
                'jenis_transaksi' => 'pendapatan',
                'id_transaksi' => $pendapatan_detail->id,
                'id_coa' => !$check_akun_pendapatan ? $coa_pendapatan->id : $check_akun_pendapatan->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pendapatan.detail', $request->id_pendapatan_header)->with('success', 'Data berhasil disimpan dan status reservasi diubah menjadi Selesai');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.detail', $request->id_pendapatan_header)->with('error', 'Data gagal disimpan: ' . $e->getMessage());
        }
    }

    public function destroydetail($id)
    {
        $pendapatan_detail = Pendapatandetail::find($id);

        try {
            // Update reservation status back to "Disetujui"
            if ($pendapatan_detail->id_reservasi) {
                DB::table('reservasi')
                    ->where('id', $pendapatan_detail->id_reservasi)
                    ->update([
                        'status' => 'Disetujui',
                        'updated_at' => now()
                    ]);
            }

            // Delete related journal entries
            Jurnal::where('jenis_transaksi', 'pendapatan')
                ->where('id_transaksi', $id)
                ->delete();

            // Delete the pendapatan detail
            $pendapatan_detail->delete();

            return redirect()->route('pendapatan.detail', $pendapatan_detail->id_pendapatan_header)
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.detail', $pendapatan_detail->id_pendapatan_header)
                ->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }
}
