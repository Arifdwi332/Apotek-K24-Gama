<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_MstBarang extends Model
{
    protected $table = 'mst_barang';
    protected $fillable = ['barang_nm','rak_id', 'rak_shaft_id','created_by','updated_by'];

  public function rak()
{
    return $this->belongsTo(M_Rak::class,'rak_id');
}
public function rakShaft()
{
    return $this->belongsTo(M_RakShaft::class,'rak_shaft_id');
}

}


