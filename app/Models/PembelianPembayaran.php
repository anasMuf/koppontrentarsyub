<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembelianPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembelian_pembayaran';
    protected $primaryKey = 'id_pembelian_pembayaran';

    protected $guarded = ['id_pembelian_pembayaran'];

    public function pembelian(){
        return $this->belongsTo(Pembelian::class,'pembelian_id','id_pembelian');
    }
}
