<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukVarian extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'produk_varian';
    protected $primaryKey = 'id_produk_varian';

    protected $guarded = ['id_produk_varian'];

    public function pembelian_detail(){
        return $this->hasMany(PembelianDetail::class,'produk_varian_id','id_produk_varian');
    }
    public function penjualan_detail(){
        return $this->hasMany(PenjualanDetail::class,'produk_varian_id','id_produk_varian');
    }

    public function produk(){
        return $this->belongsTo(Produk::class,'produk_id','id_produk');
    }
}
