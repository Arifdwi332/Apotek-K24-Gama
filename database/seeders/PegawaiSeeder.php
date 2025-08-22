<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_pegawai')->insert([
            [
                'pegawai_id' => 1, // optional, jika auto increment bisa dihapus
                'pegawai_nm' => 'Admin Utama',
                'role_id'    => 1, // pastikan role_id = 1 ada di tabel roles
                'status_st'  => 1,
                'deleted_st' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'pegawai_id' => 2,
                'pegawai_nm' => 'Karyawan Biasa',
                'role_id'    => 2, // pastikan role_id = 2 ada di tabel roles
                'status_st'  => 1,
                'deleted_st' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
