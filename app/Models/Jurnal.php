<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnal';
    protected $fillable = [
        'no_jurnal',
        'tgl_jurnal',
        'posisi_dr_cr',
        'nominal',
        'jenis_transaksi',
        'id_transaksi',
        'id_coa',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public static function getNoJurnal()
    {
        // query kode barang
        $sql = "SELECT IFNULL(MAX(no_jurnal), CONCAT('JRL', DATE_FORMAT(CURDATE(), '%Y%m%d'), '0000')) AS no_jurnal FROM jurnal";
        $no_jurnal = DB::select($sql);

        // cacah hasilnya
        foreach ($no_jurnal as $jrl) {
            $nojrl = $jrl->no_jurnal;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($nojrl, -4);
        $noakhir = $noawal + 1; //menambahkan 1, hasilnya adalah integer cth 1

        //menyambung dengan string PR-001
        $noakhir = 'JRL' . date('Ymd') . str_pad($noakhir, 4, "0", STR_PAD_LEFT);

        return $noakhir;
    }

    // view data jurnal umum berdasarkan periode
    public static function viewjurnalumum($periode)
    {
        // periode memiliki format YYYY-MM
        $sql = "   SELECT a.*,b.nama_akun, b.kode_akun
                    FROM jurnal a JOIN coa b
                    ON (a.id_coa=b.id)
                    WHERE DATE_FORMAT(tgl_jurnal, '%Y-%m')=?
                    ORDER BY 1 ASC
                ";
        $list = DB::select($sql, [$periode]);
        return $list;
    }

    // view data data-data akun buku besar suatu perusahaan
    public static function viewakunbukubesar()
    {
        // periode memiliki format YYYY-MM
        $sql = "   SELECT b.id, b.nama_akun, b.kode_akun
                FROM jurnal a JOIN coa b
                ON (a.id_coa=b.id)
                GROUP BY b.id, b.nama_akun, b.kode_akun
                ORDER BY 2 ASC
            ";
        $list = DB::select($sql);
        return $list;
    }

    // view data jurnal umum berdasarkan periode
    public static function viewdatabukubesar($periode, $akun)
    {
        // periode memiliki format YYYY-MM
        $sql = "   SELECT a.*,b.nama_akun
                    FROM jurnal a JOIN coa b
                    ON (a.id_coa=b.id)
                    WHERE DATE_FORMAT(tgl_jurnal, '%Y-%m')= ?
                    AND b.id = ?
                    ORDER BY 1 ASC
                ";
        $list = DB::select($sql, [$periode, $akun]);
        return $list;
    }

    // viewposisisdb saldo normal
    public static function viewposisisaldonormalakun($akun)
    {
        $akun = DB::table('coa as a')
            ->leftJoin('coa as b', 'a.header_akun', '=', 'b.id')
            ->select('b.kode_akun as header')
            ->where('a.id', $akun)
            ->first()->header;
        switch ($akun) {
            case '1':
                $posisi_saldo_normal = 'd';
                break;
            case '2':
                $posisi_saldo_normal = 'c';
                break;
            case '3':
                $posisi_saldo_normal = 'c';
                break;
            case '4':
                $posisi_saldo_normal = 'c';
                break;
            case '5':
                $posisi_saldo_normal = 'd';
                break;
        }
        return $posisi_saldo_normal;
    }

    // view saldo buku besar bulan sebelumnya berdasarkan periode dan kode akun
    public static function viewsaldobukubesar($periode, $akun)
    {
        // dapatkan posisi saldo normal akun tsb
        $posisi_saldo_normal = Jurnal::viewposisisaldonormalakun($akun);

        $sql = "   SELECT tbl1.posisi_dr_cr,ifnull(tbl2.total,0) as nominal FROM
                    (
                        SELECT 'c' posisi_dr_cr
                        UNION
                        SELECT 'd' posisi_dr_cr
                    ) tbl1
                    LEFT OUTER JOIN
                    (
                        Select a.posisi_dr_cr,sum(a.nominal) as total
                        FROM jurnal a
                        JOIN coa b ON (a.id_coa=b.id)
                        WHERE a.id_coa = ?
                        AND date_format(a.tgl_jurnal,'%Y-%m') < ?
                        GROUP BY  a.posisi_dr_cr
                    ) tbl2
                    ON (tbl1.posisi_dr_cr = tbl2.posisi_dr_cr)
                ";
        $list = DB::select($sql, [$akun, $periode]);
        $saldo_debet = 0;
        $saldo_kredit = 0;
        foreach ($list as $cacah):
            if (strcmp($cacah->posisi_dr_cr, 'd') == 0) {
                $saldo_debet = $saldo_debet + $cacah->nominal;
            } else {
                $saldo_kredit = $saldo_kredit + $cacah->nominal;
            }
        endforeach;

        if (strcmp($posisi_saldo_normal, 'd') == 0) {
            $saldo = $saldo_debet - $saldo_kredit;
        } else {
            $saldo = $saldo_kredit - $saldo_debet;
        }
        return $saldo;
    }

    public static function viewpembelian($periode)
    {
        $pembelian = DB::table('pembelian_header as a')
            ->leftJoin('vendor as b', 'a.id_vendor', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT id_pembelian_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.kuantitas * x.harga_satuan) as total FROM pembelian_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY id_pembelian_header) as c"), 'a.id', '=', 'c.id_pembelian_header')
            ->select('a.no_pembelian', 'a.tgl_pembelian', 'b.nama_vendor', 'c.daftar_barang', DB::raw('IFNULL(c.total, 0) as total'))
            ->whereRaw("DATE_FORMAT(a.tgl_pembelian, '%Y-%m') = '$periode'")
            ->get();

        return $pembelian;
    }

    public static function viewpenjualan($periode)
    {
        $penjualan = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.subtotal) as total FROM penjualan_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
            ->select('a.no_penjualan', 'a.tgl_penjualan', 'b.nama_pelanggan', 'c.daftar_barang', DB::raw('IFNULL(c.total, 0) as total'))
            ->whereRaw("DATE_FORMAT(a.tgl_penjualan, '%Y-%m') = '$periode'")
            ->get();

        return $penjualan;
    }

    public static function viewlabarugi($periode, $akun)
    {
        return DB::table('coa as a')
            ->leftJoin('jurnal as b', function ($join) use ($periode) {
                $join->on('a.id', '=', 'b.id_coa')
                    ->whereRaw("DATE_FORMAT(b.tgl_jurnal, '%Y-%m') = ?", [$periode]);
            })
            ->select('a.nama_akun', DB::raw('COALESCE(SUM(b.nominal), 0) AS nominal'))
            ->where('a.kode_akun', 'LIKE', $akun . '%')
            ->groupBy('a.id', 'a.nama_akun')
            ->orderBy('a.id')
            ->get();
    }

    public static function viewkartustok($periode)
    {
        $kartu_stok = DB::table('barang as a')
            ->leftJoin(DB::raw("
        (SELECT
            id_barang,
            SUM(kuantitas) as kuantitas_persediaan
        FROM
            persediaan
        WHERE
            DATE_FORMAT(tgl_persediaan, '%Y-%m') = '$periode'
        GROUP BY
            id_barang) as b
    "), 'a.id', '=', 'b.id_barang')
            ->leftJoin(DB::raw("
        (SELECT
            id_barang,
            SUM(kuantitas) as kuantitas_pengambilan
        FROM
            pengambilan
        WHERE
            DATE_FORMAT(tgl_pengambilan, '%Y-%m') = '$periode'
        GROUP BY
            id_barang) as c
    "), 'a.id', '=', 'c.id_barang')
            ->leftJoin(DB::raw("
        (SELECT
            id_barang,
            SUM(kuantitas) as saldo_persediaan
        FROM
            persediaan
        WHERE
            DATE_FORMAT(tgl_persediaan, '%Y-%m') < '$periode'
        GROUP BY
            id_barang) as d
    "), 'a.id', '=', 'd.id_barang')
            ->leftJoin(DB::raw("
        (SELECT
            id_barang,
            SUM(kuantitas) as saldo_pengambilan
        FROM
            pengambilan
        WHERE
            DATE_FORMAT(tgl_pengambilan, '%Y-%m') < '$periode'
        GROUP BY
            id_barang) as e
    "), 'a.id', '=', 'e.id_barang')
            ->select(
                'a.kode_barang',
                'a.nama_barang',
                DB::raw('IFNULL(d.saldo_persediaan, 0) - IFNULL(e.saldo_pengambilan, 0) AS saldo_awal'),
                DB::raw('IFNULL(b.kuantitas_persediaan, 0) AS kuantitas_persediaan'),
                DB::raw('IFNULL(c.kuantitas_pengambilan, 0) AS kuantitas_pengambilan')
            )
            ->get();

        return $kartu_stok;
    }

    public static function viewpengeluarankas($periode)
    {
        // Get pengeluaran data
        $pengeluaran_kas = Pengeluaran::select(
            'no_pengeluaran as no_transaksi',
            'tgl_pengeluaran as tanggal',
            DB::raw("CONCAT('Pengeluaran ', tipe_pengeluaran) as keterangan"),
            'nominal',
            DB::raw("'pengeluaran' as jenis")
        )
            ->whereRaw("DATE_FORMAT(tgl_pengeluaran, '%Y-%m') = ?", [$periode]);

        // Get pembelian data
        $pembelian_kas = DB::table('pembelian_header as ph')
            ->leftJoin(
                DB::raw("(SELECT id_pembelian_header, SUM(kuantitas * harga_satuan) as total FROM pembelian_detail GROUP BY id_pembelian_header) as pd"),
                'ph.id',
                '=',
                'pd.id_pembelian_header'
            )
            ->select(
                'ph.no_pembelian as no_transaksi',
                'ph.tgl_pembelian as tanggal',
                DB::raw("'Pembelian' as keterangan"),
                DB::raw('IFNULL(pd.total, 0) as nominal'),
                DB::raw("'pembelian' as jenis")
            )
            ->whereRaw("DATE_FORMAT(ph.tgl_pembelian, '%Y-%m') = ?", [$periode]);

        // Union the two queries and order by date
        $combined_data = $pengeluaran_kas->union($pembelian_kas)
            ->orderBy('tanggal')
            ->get();

        return $combined_data;
    }
}
