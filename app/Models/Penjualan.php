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

    protected $appends = ['status_pembayaran','nominal_dibayar'];

    public function penjualan_detail(){
        return $this->hasMany(PenjualanDetail::class,'penjualan_id','id_penjualan');
    }
    public function penjualan_pembayaran(){
        return $this->hasMany(PenjualanPembayaran::class,'penjualan_id','id_penjualan');
    }

    public function anggota(){
        return $this->belongsTo(Anggota::class,'anggota_id','id_anggota');
    }

    public function getNominalDibayarAttribute(){
        return $totalDibayar = $this->penjualan_pembayaran->sum('nominal');
    }
    public function getStatusPembayaranAttribute(){

        $totalDibayar = (float) $this->nominal_dibayar;
        $totalPenjualan = (float) $this->total_penjualan;

        return ($totalDibayar >= $totalPenjualan) ? 'Lunas' : 'Belum Lunas';
    }
}
