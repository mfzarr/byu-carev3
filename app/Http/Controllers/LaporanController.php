<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;

class LaporanController extends Controller
{
    public function jurnalumum()
    {
        return view('laporan.jurnalumum', ['title' => 'Jurnal Umum']);
    }

    public function viewdatajurnalumum($periode)
    {
        $jurnal = Jurnal::viewjurnalumum($periode);
        if ($jurnal) {
            return response()->json([
                'status' => 200,
                'jurnal' => $jurnal,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function bukubesar()
    {
        $akun = Jurnal::viewakunbukubesar();
        return view(
            'laporan.bukubesar',
            [
                'akun' => $akun,
                'title' => 'Buku Besar',
            ]
        );
    }

    public function viewdatabukubesar($periode, $akun)
    {
        $saldoawal = Jurnal::viewsaldobukubesar($periode, $akun);
        $posisi = Jurnal::viewposisisaldonormalakun($akun);

        $bukubesar = Jurnal::viewdatabukubesar($periode, $akun);
        if (count($bukubesar) > 0) {
            return response()->json([
                'status' => 200,
                'bukubesar' => $bukubesar,
                'saldoawal' => $saldoawal,
                'posisi' => $posisi,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function pembelian()
    {
        return view('laporan.pembelian', ['title' => 'Laporan Pembelian']);
    }

    public function viewdatapembelian($periode)
    {
        $pembelian = Jurnal::viewpembelian($periode);
        if (count($pembelian) > 0) {
            return response()->json([
                'status' => 200,
                'pembelian' => $pembelian,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function penjualan()
    {
        return view('laporan.penjualan', ['title' => 'Laporan Penjualan']);
    }

    public function viewdatapenjualan($periode)
    {
        $penjualan = Jurnal::viewpenjualan($periode);
        if (count($penjualan) > 0) {
            return response()->json([
                'status' => 200,
                'penjualan' => $penjualan,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function labarugi()
    {
        return view('laporan.labarugi', ['title' => 'Laporan Laba Rugi']);
    }

    public function viewdatalabarugi($periode)
    {
        $pendapatan = Jurnal::viewlabarugi($periode, 4);
        $beban = Jurnal::viewlabarugi($periode, 5);

        $total_pendapatan = 0;
        $total_diskon_pendapatan = 0;
        $total_diskon_penjualan = 0;

        // Process pendapatan and separate diskon types
        $pendapatan_processed = [];
        foreach ($pendapatan as $p) {
            $akun_name = strtolower($p->nama_akun);

            // Handle different types of discounts
            if (
                $akun_name === 'diskon pendapatan' ||
                strpos($akun_name, 'diskon pendapatan') !== false
            ) {
                // For diskon pendapatan, make the nominal negative
                $p->nominal = -$p->nominal;
                $total_diskon_pendapatan += $p->nominal; // This will be negative
            } else if (
                $akun_name === 'diskon penjualan' ||
                strpos($akun_name, 'diskon penjualan') !== false
            ) {
                // For diskon penjualan, make the nominal negative
                $p->nominal = -$p->nominal;
                $total_diskon_penjualan += $p->nominal; // This will be negative
            }

            $total_pendapatan += $p->nominal;
            $pendapatan_processed[] = $p;
        }

        $total_beban = 0;
        foreach ($beban as $b) {
            $total_beban += $b->nominal;
        }

        if ($total_pendapatan > 0 || $total_beban > 0) {
            return response()->json([
                'status' => 200,
                'pendapatan' => $pendapatan_processed,
                'beban' => $beban,
                'total_pendapatan' => $total_pendapatan,
                'total_diskon_pendapatan' => $total_diskon_pendapatan,
                'total_diskon_penjualan' => $total_diskon_penjualan,
                'total_diskon' => $total_diskon_pendapatan + $total_diskon_penjualan,
                'total_beban' => $total_beban,
                'laba_rugi' => $total_pendapatan - $total_beban
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function kartustok()
    {
        return view('laporan.kartustok', ['title' => 'Laporan Kartu Stok']);
    }

    public function viewdatakartustok($periode)
    {
        $kartustok = Jurnal::viewkartustok($periode);
        if (count($kartustok) > 0) {
            return response()->json([
                'status' => 200,
                'kartustok' => $kartustok,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function pengeluarankas()
    {
        return view('laporan.pengeluarankas', ['title' => 'Laporan Pengeluaran Kas']);
    }

    public function viewdatapengeluarankas($periode)
    {
        $pengeluarankas = Jurnal::viewpengeluarankas($periode);
        if (count($pengeluarankas) > 0) {
            return response()->json([
                'status' => 200,
                'pengeluarankas' => $pengeluarankas,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }
}
