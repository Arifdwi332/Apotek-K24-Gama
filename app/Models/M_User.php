<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class M_User extends Authenticatable
{
    protected $table = 'mst_users';
    protected $fillable = ['username','password','pegawai_id','created_by','updated_by'];

    public function pegawai()
    {
        return $this->belongsTo(M_Pegawai::class, 'pegawai_id');
    }

}
