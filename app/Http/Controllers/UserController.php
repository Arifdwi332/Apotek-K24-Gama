<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_MstPegawai;
use App\Models\M_User;
use DataTables;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function getData()
    {
        $data = M_MstPegawai::with('role', 'user')->select('*');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('username', fn($row) => $row->user->username ?? '-')
            ->addColumn('aksi', function($row){
                return '
                    <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->pegawai_id.'">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->pegawai_id.'">Hapus</button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_nm' => 'required|string',
            'role_id' => 'required|in:1,2',
            'status_st' => 'required|in:0,1',
            'username' => 'required|string|unique:mst_users,username',
            'password' => 'required|string|min:6',
        ]);

        $pegawai = M_MstPegawai::create([
            'pegawai_nm' => $request->pegawai_nm,
            'role_id' => $request->role_id,
            'status_st' => $request->status_st,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        M_User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'pegawai_id' => $pegawai->pegawai_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil disimpan']);
    }

    public function edit($id)
    {
        $pegawai = M_MstPegawai::with('user')->findOrFail($id);
        return response()->json($pegawai);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_nm' => 'required|string',
            'role_id' => 'required|in:1,2',
            'status_st' => 'required|in:0,1',
            'username' => 'required|string|unique:mst_users,username,'.$id.',pegawai_id',
        ]);

        $pegawai = M_MstPegawai::findOrFail($id);
        $pegawai->update([
            'pegawai_nm' => $request->pegawai_nm,
            'role_id' => $request->role_id,
            'status_st' => $request->status_st,
            'updated_by' => auth()->id(),
        ]);

        $user = M_User::where('pegawai_id', $id)->first();
        if ($user) {
            $user->update([
                'username' => $request->username,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'updated_by' => auth()->id(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $pegawai = M_MstPegawai::findOrFail($id);
        $pegawai->delete();
        M_User::where('pegawai_id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
    }
}
