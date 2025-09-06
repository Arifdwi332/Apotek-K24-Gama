<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_BarangStok extends Model
{
      protected $table = 'dat_stok';
    protected $fillable = [
        'barang_id',
        'stok',        
        'masuk',       
        'keluar',     
        'satuan',      
        'lokasi',
        'keterangan',
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
    public function createdBy()
{
    return $this->belongsTo(M_MstPegawai::class, 'created_by', 'pegawai_id');
}

public function updatedBy()
{
    return $this->belongsTo(M_MstPegawai::class, 'updated_by', 'pegawai_id');
}
}
