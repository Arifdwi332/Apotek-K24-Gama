<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_users')->insert([
            [
                'username'   => 'admin',
                'email'      => 'admin@example.com',
                'password'   => Hash::make('password123'), // ganti sesuai kebutuhan
                'pegawai_id' => 1, // pastikan ada data pegawai_id = 1 di tabel mst_pegawai
                'deleted_st' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'karyawan',
                'email'      => 'karyawan@example.com',
                'password'   => Hash::make('password123'), // ganti sesuai kebutuhan
                'pegawai_id' => 2, // pastikan ada data pegawai_id = 2 di tabel mst_pegawai
                'deleted_st' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
