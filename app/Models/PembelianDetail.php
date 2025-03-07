<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembelianDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';

    protected $guarded = ['id_pembelian_detail'];

    public function pembelian(){
        return $this->belongsTo(Pembelian::class,'pembelian_id','id_pembelian');
    }

    public function produk_varian(){
        return $this->belongsTo(ProdukVarian::class,'produk_varian_id','id_produk_varian');
    }
}
