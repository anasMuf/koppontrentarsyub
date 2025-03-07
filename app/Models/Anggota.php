<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    protected $guarded = ['id_anggota'];

    public function pendaftaran_anggota(){
        return $this->hasOne(PendaftaranAnggota::class);
    }
    public function iuran_anggota(){
        return $this->hasMany(IuranAnggota::class, 'anggota_id','id_anggota');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function no_anggota(){
        $lastNomor = Anggota::select('no_anggota')->orderBy('id_anggota','desc')->first();
        if($lastNomor){
            $lastNumber = (int) substr($lastNomor->no_anggota, -5);
            $newNomor = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }else{
            $newNomor = '00001';
        }
        $no_anggota = $newNomor;
        return $no_anggota;
    }
}
