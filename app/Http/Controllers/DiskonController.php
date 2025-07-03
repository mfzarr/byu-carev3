<?php

namespace App\Http\Controllers;

use Autocode;
use App\Models\Barang;
use App\Models\Diskon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;



class DiskonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diskon = Diskon::with('barang', 'user', 'userUpdated')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('diskon.index', [
            'diskon' => $diskon,
            'title' => 'Diskon'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_diskon = Autocode::code('diskon', 'kode_diskon', 'DSK');
        $barang = Barang::all();

        return view('diskon.create', [
            'kode_diskon' => $kode_diskon,
            'barang' => $barang,
            'title' => 'Tambah Diskon'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_diskon' => 'required',
            'nama_diskon' => 'required',
            'min_transaksi' => 'required|numeric|min:0',
            'persentase_diskon' => 'required|numeric|min:0|max:100',
            'max_diskon' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barang,id',
        ], [
            'kode_diskon.required' => 'Kode Diskon harus diisi',
            'nama_diskon.required' => 'Nama Diskon harus diisi',
            'min_transaksi.required' => 'Minimal Transaksi harus diisi',
            'persentase_diskon.required' => 'Persentase Diskon harus diisi',
            'max_diskon.required' => 'Maksimal Diskon harus diisi',
            'id_barang.required' => 'Barang harus dipilih',
        ]);

        try {
            Diskon::create([
                'kode_diskon' => $request->kode_diskon,
                'nama_diskon' => $request->nama_diskon,
                'min_transaksi' => $request->min_transaksi,
                'persentase_diskon' => $request->persentase_diskon,
                'max_diskon' => $request->max_diskon,
                'id_barang' => $request->id_barang,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('diskon.index')->with('error', 'Gagal menambahkan diskon: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $diskon = Diskon::find($id);
        $barang = Barang::all();

        return view('diskon.edit', [
            'diskon' => $diskon,
            'barang' => $barang,
            'title' => 'Edit Diskon'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_diskon' => 'required',
            'nama_diskon' => 'required',
            'min_transaksi' => 'required|numeric|min:0|max:100',
            'persentase_diskon' => 'required|numeric|min:0|max:100',
            'max_diskon' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barang,id',
        ], [
            'kode_diskon.required' => 'Kode Diskon harus diisi',
            'nama_diskon.required' => 'Nama Diskon harus diisi',
            'min_transaksi.required' => 'Minimal Transaksi harus diisi',
            'persentase_diskon.required' => 'Persentase Diskon harus diisi',
            'max_diskon.required' => 'Maksimal Diskon harus diisi',
            'id_barang.required' => 'Barang harus dipilih',
        ]);
        try {
            $diskon = Diskon::find($id);
            $diskon->update([
                'kode_diskon' => $request->kode_diskon,
                'nama_diskon' => $request->nama_diskon,
                'min_transaksi' => $request->min_transaksi,
                'persentase_diskon' => $request->persentase_diskon,
                'max_diskon' => $request->max_diskon,
                'id_barang' => $request->id_barang,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('diskon.index')->with('error', 'Gagal memperbarui diskon: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diskon = Diskon::find($id);
        try {
            $diskon->delete();
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil dihapus');
        } catch (\Exception $e) {
            // Check if it's a foreign key constraint violation
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                return redirect()->route('diskon.index')->with('error', 'Gagal menghapus diskon. Diskon ini sedang digunakan dalam transaksi penjualan.');
            }

            // For other errors, return a generic message
            return redirect()->route('diskon.index')->with('error', 'Gagal menghapus diskon');
        }
    }
}
