<?php

namespace App\Http\Controllers;
use App\Models\M_MstBarang;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\M_BarangStok;

use Illuminate\Http\Request;

class LogPencatatanController extends Controller
{
     public function inputStock()
    {
        $mstBarang = M_MstBarang::all(); // ambil semua data
    return view('log_pencatatan.index', compact('mstBarang'));
    }

    public function getData(Request $request)
    {

        $query = M_BarangStok::with(['mstBarang','createdBy','updatedBy']);

        if ($request->barang_id) {
            $query->where('barang_id', $request->barang_id);
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('barang_nm', function($row){
                return $row->mstBarang->barang_nm ?? '-';
            })
             ->addColumn('created_by', function($row){
                return $row->createdBy->pegawai_nm ?? '-';
            })
            ->addColumn('updated_by', function($row){
                return $row->updatedBy->pegawai_nm ?? '-';
            })
              ->editColumn('updated_at', function($row){
                return $row->updated_at 
                    ? Carbon::parse($row->updated_at)->format('d-m-Y H:i') 
                    : '-';
            })
             ->editColumn('catat_tgl', function($row){
                return $row->catat_tgl 
                    ? Carbon::parse($row->catat_tgl)->format('d-m-Y') 
                    : '-';
            })
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



    public function edit($id)
    {
        $barang = M_BarangStoK::where('id', $id)->firstOrFail();
        return response()->json($barang);
    }


 

    public function destroy($id)
    {
        $barang = M_BarangStoK::findOrFail($id);
        $barang->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

 public function showBarang($id)
{
    $barang     = M_MstBarang::findOrFail($id); // satu barang untuk detail
    $mstBarang  = M_MstBarang::select('id','barang_nm')->orderBy('barang_nm')->get(); // list utk dropdown

    return view('log_pencatatan.detail', compact('barang','mstBarang'));
}

}
