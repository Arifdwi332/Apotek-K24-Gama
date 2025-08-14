<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_MstBarang extends Model
{
    protected $table = 'mst_barang';
    protected $fillable = [
        'barang_nm',
        'created_by',
        'updated_by'
    ];
}
