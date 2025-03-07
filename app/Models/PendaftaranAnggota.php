<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendaftaranAnggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran_anggota';
    protected $primaryKey = 'id_pendaftaran_anggota';

    protected $guarded = ['id_pendaftaran_anggota'];

    public function anggota(){
        return $this->hasOne(Anggota::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function no_formulir(){
        $lastNomor = PendaftaranAnggota::select('no_formulir')->orderBy('id_pendaftaran_anggota','desc')->first();
        if($lastNomor){
            $lastNumber = (int) substr($lastNomor->no_formulir, -5);
            $newNomor = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }else{
            $newNomor = '00001';
        }
        $no_formulir = $newNomor;
        return $no_formulir;
    }
}
