<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisIuran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_iuran';
    protected $primaryKey = 'id_jenis_iuran';

    protected $guarded = ['id_jenis_iuran'];

    public function iuran_anggota(){
        return $this->hasMany(IuranAnggota::class,'jenis_iuran_id','id_jenis_iuran');
    }
}
