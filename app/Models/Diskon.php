<?php

namespace App\Models;

use App\Models\Barang;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Diskon extends Model
{
   use HasFactory;

    protected $table = 'diskon';

    protected $fillable = [
        'kode_diskon',
        'nama_diskon',
        'min_transaksi',
        'persentase_diskon',
        'max_diskon',
        'id_barang',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }
    public function userUpdated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
