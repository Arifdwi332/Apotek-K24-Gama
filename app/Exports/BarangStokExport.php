<?php

namespace App\Exports;

use App\Models\M_BarangStok;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class BarangStokExport implements FromCollection, WithHeadings
{
    protected $barangId;

    public function __construct($barangId = null)
    {
        $this->barangId = $barangId;
    }

    public function collection()
    {
        $query = M_BarangStok::with('mstBarang');

        if ($this->barangId) {
            $query->where('barang_id', $this->barangId);
        }

        return $query->get()->map(function ($stok) {
            return [
                'ID'            => $stok->id,
                'Nama Barang'   => $stok->mstBarang->barang_nm ?? '-',
                'Jumlah Stok'   => $stok->stok,
                'Tanggal Exp'   => $stok->exp_tgl,
                'Tanggal Catat' => $stok->catat_tgl,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Barang',
            'Jumlah Stok',
            'Tanggal Expired',
            'Tanggal Pencatatan',
        ];
    }
}
