<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_MstPegawai extends Model
{
    protected $table = 'mst_pegawai';
    protected $primaryKey = 'pegawai_id';
    protected $fillable = ['pegawai_nm','role_id','status_st','created_by','updated_by'];

    public function user()
    {
        return $this->hasOne(M_User::class, 'pegawai_id');
    }

    public function role()
    {
        return $this->belongsTo(M_Role::class, 'role_id');
    }

}
