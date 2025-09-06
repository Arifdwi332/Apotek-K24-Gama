<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_RakShaft extends Model
{
    protected $table = 'rak_shafts';
    protected $fillable = ['rak_id', 'nama_shaft'];

    public function rak()
    {
        return $this->belongsTo(M_Rak::class, 'rak_id');
    }
    public function barangs()
{
    return $this->hasMany(\App\Models\M_MstBarang::class, 'rak_shaft_id');
}
}
