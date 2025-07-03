<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    protected $fillable = [
        'kode_vendor',
        'nama_vendor',
        'alamat_vendor',
        'no_hp',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
