<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_BarangStok extends Model
{
      protected $table = 'dat_stok';
    protected $fillable = [
        'barang_id',
        'stok',
        'exp_tgl',    
        'catat_tgl',  
        'created_by',
        'updated_by'
    ];


    // Relasi ke M_MstBarang
    public function mstBarang()
    {
        return $this->belongsTo(M_MstBarang::class, 'barang_id');
    }
}
