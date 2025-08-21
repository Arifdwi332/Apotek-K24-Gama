<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class M_User extends Authenticatable
{
    use Notifiable;

    protected $table = 'mst_users';
    protected $primaryKey = 'id'; // cek, apakah pk memang `id` atau `user_id`?

    protected $fillable = [
        'username',
        'password',
        'pegawai_id',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pegawai()
    {
        return $this->belongsTo(M_MstPegawai::class, 'pegawai_id');
    }
}
