<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Member;
use DataTables;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Halaman index member
     */
    public function index()
    {
        return view('member.index');
    }

    /**
     * Ambil data untuk DataTables (server-side)
     */
    public function getData()
    {
       
        $data = M_Member::query();

        return DataTables::of($data)
            ->addIndexColumn()
          
            ->editColumn('created_at', fn($row) => only_admin(fn() => $row->created_at?->format('d-m-Y H:i:s')))
            ->editColumn('updated_at', fn($row) => only_admin(fn() => $row->updated_at?->format('d-m-Y H:i:s')))
            ->editColumn('created_by', fn($row) => only_admin(fn() => get_user($row->created_by)))
            ->editColumn('updated_by', fn($row) => only_admin(fn() => get_user($row->updated_by)))

            ->addColumn('aksi', function($row){
                return only_admin(function() use ($row) {
                    return '
                        <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id_member.'">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id_member.'">Delete</button>
                    ';
                });
            })

            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Simpan data member baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string',
            'no_hp'          => 'nullable|string|max:30',
            'jenis_kelamin'  => ['nullable', Rule::in(['L','P'])],
            'usia'           => 'nullable|integer|min:0|max:150',
            'alamat'         => 'nullable|string',
        ]);

        M_Member::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'jenis_kelamin'=> $request->jenis_kelamin,
            'usia'         => $request->usia,
            'alamat'       => $request->alamat,
            'created_by'   => auth()->id(),
            'updated_by'   => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Member berhasil ditambahkan']);
    }

    /**
     * Ambil detail member untuk edit
     */
    public function edit($id)
    {
        $member = M_Member::findOrFail($id);
        return response()->json($member);
    }

    /**
     * Update data member
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string',
            'no_hp'          => 'nullable|string|max:30',
            'jenis_kelamin'  => ['nullable', Rule::in(['L','P'])],
            'usia'           => 'nullable|integer|min:0|max:150',
            'alamat'         => 'nullable|string',
        ]);

        $member = M_Member::findOrFail($id);
        $member->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'jenis_kelamin'=> $request->jenis_kelamin,
            'usia'         => $request->usia,
            'alamat'       => $request->alamat,
            'updated_by'   => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Member berhasil diperbarui']);
    }

    /**
     * Hapus data member
     */
    public function destroy($id)
    {
        $member = M_Member::findOrFail($id);
        $member->delete();

        return response()->json(['success' => true, 'message' => 'Member berhasil dihapus']);
    }
}
