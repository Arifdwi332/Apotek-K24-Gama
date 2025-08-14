<?php

namespace App\Http\Controllers;
use App\Models\M_MstBarang;
use Yajra\DataTables\Facades\DataTables;

use App\Models\M_BarangStoK;

use Illuminate\Http\Request;

class BarangStokController extends Controller
{
    public function inputStock()
    {
        $mstBarang = M_MstBarang::all(); // ambil semua data
    return view('barang_stok.index', compact('mstBarang'));
    }

     public function getData()
{
    $data = M_BarangStok::with('mstBarang')->get();
    return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('barang_nm', function($row){
            return $row->mstBarang->barang_nm ?? '-';
        })
        ->addColumn('aksi', function($row){
            return '
                <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'">Edit</button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'">Hapus</button>
            ';
        })
        ->rawColumns(['aksi'])
        ->make(true);
}

public function store(Request $request)
{
    $request->validate([
        'barang_id' => 'required|exists:mst_barang,id',
        'stok' => 'required|integer|min:0',
        'exp_tgl' => 'nullable|date',
        'catat_tgl' => 'required|date',
    ]);

    if ($request->id) {
        $stok = M_BarangStok::findOrFail($request->id);
        
        $stok->update([
            'barang_id' => $request->barang_id,
            'stok' => $request->stok,
            'exp_tgl' => $request->exp_tgl,
            'catat_tgl' => $request->catat_tgl,
            'updated_by' => auth()->id(),
        ]);
        $message = 'Data berhasil diperbarui';
    } else {
        M_BarangStok::create([
            'barang_id' => $request->barang_id,
            'stok' => $request->stok,
            'exp_tgl' => $request->exp_tgl,
            'catat_tgl' => $request->catat_tgl,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        $message = 'Data berhasil disimpan';
    }

    return response()->json(['success' => true, 'message' => $message]);
}



    // Ambil data untuk edit
    public function edit($id)
    {
        $barang = M_BarangStoK::where('id', $id)->firstOrFail();
        return response()->json($barang);
    }


 

    // Hapus data
    public function destroy($id)
    {
        $barang = M_BarangStoK::findOrFail($id);
        $barang->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
