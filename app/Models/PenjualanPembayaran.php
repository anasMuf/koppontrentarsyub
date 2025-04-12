<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenjualanPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualan_pembayaran';
    protected $primaryKey = 'id_penjualan_pembayaran';

    protected $guarded = ['id_penjualan_pembayaran'];

    public function penjualan(){
        return $this->belongsTo(Penjualan::class,'penjualan_id','id_penjualan');
    }
}
