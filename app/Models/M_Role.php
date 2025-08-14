<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['role_nm', 'deleted_st', 'created_by', 'updated_by'];
}
