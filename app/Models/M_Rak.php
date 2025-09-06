<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_Rak extends Model
{
    protected $table = 'raks';
    protected $fillable = ['nama_rak'];

    public function shafts()
    {
        return $this->hasMany(M_RakShaft::class, 'rak_id');
    }
}
