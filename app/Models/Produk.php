<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $guarded = ['id_produk'];

    public function produk_varian(){
        return $this->hasMany(ProdukVarian::class,'produk_id','id_produk');
    }

    public function kategori_produk(){
        return $this->belongsTo(KategoriProduk::class,'kategori_produk_id','id_kategori_produk');
    }
}
