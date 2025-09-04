<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class M_Member extends Model
{
    protected $table = 'dat_member';
    protected $primaryKey = 'id_member';
    public $timestamps = true;

    protected $fillable = [
        'nama_lengkap',
        'no_hp',
        'jenis_kelamin',
        'usia',
        'alamat',
        'created_by',
        'updated_by',
    ];
}
