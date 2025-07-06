<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Vendor;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang = DB::table('barang as a')
            ->leftJoin('vendor as b', 'a.id_vendor', '=', 'b.id')
            ->select('a.*', 'b.nama_vendor')
            ->get();

        return view('barang.index', ['barang' => $barang, 'title' => 'Barang']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_barang = Autocode::code('barang', 'kode_barang', 'BG');
        $vendor = Vendor::all();

        return view('barang.create', ['kode_barang' => $kode_barang, 'vendor' => $vendor, 'title' => 'Tambah Barang']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'harga_satuan' => 'required',
            'harga_jual' => 'required',
            'gambar_barang' => 'file|image|mimes:jpeg,png,jpg|max:2048',
            'id_vendor' => 'required',
        ], [
            'kode_barang.required' => 'Kode Barang harus diisi',
            'nama_barang.required' => 'Nama Barang harus diisi',
            'harga_satuan.required' => 'Harga Satuan harus diisi',
            'harga_jual.required' => 'Harga Jual harus diisi',
            'id_vendor.required' => 'Vendor harus diisi',
        ]);

        try {
            if ($request->hasFile('gambar_barang')) {
                $file = $request->file('gambar_barang');
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::read($file);
                $image->resize(289, 289, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('assets/images/barang/' . $file_name));
            }

            Barang::create([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'harga_satuan' => $request->harga_satuan,
                'harga_jual' => $request->harga_jual,
                'gambar_barang' => $request->hasFile('gambar_barang') ? $file_name : 'dummy-image.png',
                'id_vendor' => $request->id_vendor,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('barang.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::find($id);
        $vendor = Vendor::all();

        return view('barang.edit', ['barang' => $barang, 'vendor' => $vendor, 'title' => 'Edit Barang']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'harga_satuan' => 'required',
            'harga_jual' => 'required',
            'gambar_barang' => 'file|image|mimes:jpeg,png,jpg|max:2048',
            'id_vendor' => 'required',
        ], [
            'kode_barang.required' => 'Kode Barang harus diisi',
            'nama_barang.required' => 'Nama Barang harus diisi',
            'harga_satuan.required' => 'Harga Satuan harus diisi',
            'harga_jual.required' => 'Harga Jual harus diisi',
            'id_vendor.required' => 'Vendor harus diisi',
        ]);

        try {
            if ($request->hasFile('gambar_barang')) {
                $barang = Barang::find($id);

                if (file_exists(public_path('assets/images/barang/' . $barang->gambar_barang)) && $barang->gambar_barang != 'dummy-image.png') {
                    unlink(public_path('assets/images/barang/' . $barang->gambar_barang));
                }

                $file = $request->file('gambar_barang');
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::read($file);
                $image->resize(289, 289, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('assets/images/barang/' . $file_name));
            }

            $barang = Barang::find($id);

            Barang::where('id', $id)->update([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'harga_satuan' => $request->harga_satuan,
                'harga_jual' => $request->harga_jual,
                'gambar_barang' => $request->hasFile('gambar_barang') ? $file_name : $barang->gambar_barang,
                'id_vendor' => $request->id_vendor,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('barang.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::find($id);
        try {
            $barang->delete();

            if (file_exists(public_path('assets/images/barang/' . $barang->gambar_barang)) && $barang->gambar_barang != 'dummy-image.png') {
                unlink(public_path('assets/images/barang/' . $barang->gambar_barang));
            }

            return redirect()->route('barang.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->with('error', 'Data gagal dihapus');
        }
    }
}
