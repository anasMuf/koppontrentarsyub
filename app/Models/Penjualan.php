<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';

    protected $guarded = ['id_penjualan'];

    public function penjualan_detail(){
        return $this->hasMany(PenjualanDetail::class,'penjualan_id','id_penjualan');
    }

    public function anggota(){
        return $this->belongsTo(Anggota::class,'anggota_id','id_anggota');
    }
}
