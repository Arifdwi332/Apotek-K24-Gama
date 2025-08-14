<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_MstBarang;
use Yajra\DataTables\Facades\DataTables;

class MstBarangController extends Controller
{
    public function index()
    {
        return view('mst_barang.index');
    }

  public function ajax(Request $request)
    {
        $data = M_MstBarang::select('id', 'barang_nm');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-warning btn-sm edit" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm delete" data-id="'.$row->id.'">Hapus</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        M_MstBarang::updateOrCreate(
            ['id' => $request->id],
            [
                'barang_nm' => $request->barang_nm,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return M_MstBarang::findOrFail($id);
    }

    public function destroy($id)
    {
        M_MstBarang::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
