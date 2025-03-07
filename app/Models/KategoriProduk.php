<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_produk';
    protected $primaryKey = 'id_kategori_produk';

    protected $guarded = ['id_kategori_produk'];

    public function produk(){
        return $this->hasMany(Produk::class,'kategori_produk_id','id_kategori_produk');
    }
}
