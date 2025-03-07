<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';

    protected $guarded = ['id_pembelian'];

    public function pembelian_detail(){
        return $this->hasMany(PembelianDetail::class,'pembelian_id','id_pembelian');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id','id_supplier');
    }
}
