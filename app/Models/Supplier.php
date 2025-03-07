<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';

    protected $guarded = ['id_supplier'];

    // public function pembelian(){
    //     return $this->hasMany(Pembelian::class,'supplier_id','id_supplier');
    // }

}
