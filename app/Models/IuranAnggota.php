<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IuranAnggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'iuran_anggota';
    protected $primaryKey = 'id_iuran_anggota';

    protected $guarded = ['id_iuran_anggota'];

    public function jenis_iuran(){
        return $this->belongsTo(JenisIuran::class,'jenis_iuran_id','id_jenis_iuran');
    }

    public function anggota(){
        return $this->belongsTo(Anggota::class, 'anggota_id', 'id_anggota');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
