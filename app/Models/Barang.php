<?php

namespace App\Models;

use App\Models\Diskon;
use App\Models\Vendor;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        // 'harga_satuan',
        'gambar_barang',
        'id_vendor',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public $timestamps = true;

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }
    public function diskon()
    {
        return $this->hasMany(Diskon::class, 'id_barang');
    }
    
}
