<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenjualanDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualan_detail';
    protected $primaryKey = 'id_penjualan_detail';

    protected $guarded = ['id_penjualan_detail'];

    public function penjualan(){
        return $this->belongsTo(Penjualan::class,'penjualan_id','id_penjualan');
    }

    public function produk_varian(){
        return $this->belongsTo(ProdukVarian::class,'produk_varian_id','id_produk_varian');
    }
}
