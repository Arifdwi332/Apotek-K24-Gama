<?php

namespace App\Http\Controllers;
use App\Models\M_MstBarang;
use Yajra\DataTables\Facades\DataTables;

use App\Models\M_BarangStok;
use App\Models\M_Rak;
use App\Models\M_RakShaft;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class BarangStokController extends Controller
{
      public function inputStock()
    {
        $raks = M_Rak::orderBy('nama_rak')->get(['id','nama_rak']);
        return view('barang_stok.index', compact('raks'));
    }

  public function getData(Request $request)
{
    $bsTable = (new M_BarangStok)->getTable(); // 'dat_stok'

    $latestDates = DB::table($bsTable.' as t')
        ->select('t.barang_id', DB::raw('MAX(t.catat_tgl) as max_catat'))
        ->when($request->barang_id, fn($q) => $q->where('t.barang_id', $request->barang_id))
        ->groupBy('t.barang_id');

    $latestIds = DB::table($bsTable.' as t')
        ->joinSub($latestDates, 'ld', function ($join) {
            $join->on('t.barang_id', '=', 'ld.barang_id')
                 ->on('t.catat_tgl', '=', 'ld.max_catat');
        })
        ->select('t.barang_id', DB::raw('MAX(t.id) as last_id'))
        ->groupBy('t.barang_id');

    $query = M_BarangStok::from($bsTable.' as bs')
        ->joinSub($latestIds, 'lx', function ($join) {
            $join->on('bs.barang_id', '=', 'lx.barang_id')
                 ->on('bs.id', '=', 'lx.last_id');
        })
        ->with(['mstBarang.rak','mstBarang.rakShaft','createdBy','updatedBy'])
        ->select('bs.*');

    if ($request->filled('barang_id')) {
        $query->where('bs.barang_id', $request->barang_id);
    }


   return DataTables::of($query)
    ->addIndexColumn()
    ->addColumn('barang_nm',  fn($row) => $row->mstBarang->barang_nm ?? '-')
    ->editColumn('created_by', fn($row) => only_admin(fn() => get_user($row->created_by)))
    ->editColumn('updated_by', fn($row) => only_admin(fn() => get_user($row->updated_by)))
    ->addColumn('rak_nama',   fn($row) => optional($row->mstBarang->rak)->nama_rak ?? '-')
    ->addColumn('shaft_nama', fn($row) => optional($row->mstBarang->rakShaft)->nama_shaft ?? '-')
 ->addColumn('aksi', function ($row) {
    $btnCatat = '<button class="btn btn-sm btn-info btn-catat mr-1"
                        data-barang-id="'.$row->barang_id.'"
                        data-barang-nm="'.e($row->mstBarang->barang_nm ?? '').'"
                        style="white-space:nowrap;">
                    Catat Stok
                 </button>';

    $btnDetail = only_admin(function () use ($row) {
        return '<a href="'.route('barangstok.historyPage', ['barang' => $row->barang_id]).'"
                    class="btn btn-sm btn-primary"
                    style="white-space:nowrap;">
                    Detail
                </a>';
    });  $btnDeleteBarang = only_admin(function () use ($row) {
        return '<button class="btn btn-sm btn-danger btn-del-barang ml-1"
                        data-barang-id="'.$row->barang_id.'"
                        data-barang-nm="'.e($row->mstBarang->barang_nm ?? '').'"
                        style="white-space:nowrap;">
                    Hapus
                </button>';
    });

    return '<div class="d-inline-flex">'.$btnCatat.$btnDetail.$btnDeleteBarang.'</div>';
})




    ->rawColumns(['aksi'])
    ->make(true);

}

// App\Http\Controllers\BarangStokController.php


    public function store(Request $request)
    {
        $request->validate([
            'barang_id'     => 'nullable|exists:mst_barang,id',
            'barang_nm'     => 'required_without:barang_id|string|max:255',
            'rak_id'        => 'required|exists:raks,id',
            'rak_shaft_id'  => 'required|exists:rak_shafts,id',

            'arah'          => 'required|in:masuk,keluar',   
            'stok'          => 'required|integer|min:0',     
            'satuan'        => 'nullable|in:tablet,strip',  
            'lokasi'        => 'nullable|string|max:150',
            'keterangan'    => 'nullable|string',

            'exp_tgl'       => 'nullable|date',
            'catat_tgl'     => 'required|date',
        ]);

        $message = 'Data berhasil disimpan';

        DB::transaction(function () use ($request, &$message) {
            // validasi shaft milik rak
            $valid = M_RakShaft::where('id', $request->rak_shaft_id)
                            ->where('rak_id', $request->rak_id)
                            ->exists();
            if (!$valid) abort(422, 'Shaft tidak termasuk pada rak terpilih.');

            // master barang (seperti kode kamu)
            if ($request->filled('barang_id')) {
                $barang = M_MstBarang::lockForUpdate()->findOrFail($request->barang_id);
                $barang->update([
                    'rak_id'       => $request->rak_id,
                    'rak_shaft_id' => $request->rak_shaft_id,
                    'updated_by'   => auth()->id(),
                ]);
            } else {
                $barang = M_MstBarang::lockForUpdate()->firstOrCreate(
                    ['barang_nm' => trim($request->barang_nm)],
                    [
                        'rak_id'       => $request->rak_id,
                        'rak_shaft_id' => $request->rak_shaft_id,
                        'created_by'   => auth()->id(),
                        'updated_by'   => auth()->id(),
                    ]
                );
                if ($barang->rak_id != $request->rak_id || $barang->rak_shaft_id != $request->rak_shaft_id) {
                    $barang->update([
                        'rak_id'       => $request->rak_id,
                        'rak_shaft_id' => $request->rak_shaft_id,
                        'updated_by'   => auth()->id(),
                    ]);
                }
            }

            $jumlah = (int) $request->stok;               // angka bebas
            $arah   = $request->arah;                     // 'masuk' / 'keluar'
            $masuk  = $arah === 'masuk'  ? $jumlah : 0;
            $keluar = $arah === 'keluar' ? $jumlah : 0;
            $delta  = $masuk - $keluar;                   // + masuk, - keluar

            // saldo terakhir
            $latest = M_BarangStok::where('barang_id', $barang->id)
                        ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                        ->lockForUpdate()->first();

            $saldoSebelum = $latest->stok ?? 0;
            $saldoBaru    = $saldoSebelum + $delta;
            if ($saldoBaru < 0) abort(422, 'Stok tidak mencukupi untuk keluar.');

            if ($request->filled('id')) {
                // opsional: izinkan update hanya baris terbaru
                $target = M_BarangStok::lockForUpdate()->findOrFail($request->id);
                if (!$latest || $target->id !== $latest->id) {
                    abort(422, 'Hanya catatan stok terbaru yang boleh diubah.');
                }
                // saldo sebelumnya untuk baris target
                $prev = M_BarangStok::where('barang_id', $barang->id)
                            ->where(function($q) use ($target){
                                $q->where('catat_tgl','<',$target->catat_tgl)
                                ->orWhere(function($q2) use ($target){
                                    $q2->where('catat_tgl',$target->catat_tgl)
                                        ->where('id','<',$target->id);
                                });
                            })
                            ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                            ->lockForUpdate()->first();

                $saldoBaruTarget = ($prev->stok ?? 0) + $delta;
                if ($saldoBaruTarget < 0) abort(422, 'Stok tidak mencukupi untuk keluar.');

                $target->update([
                    'barang_id'  => $barang->id,
                    'stok'       => $saldoBaruTarget,     // saldo setelah transaksi
                    'masuk'      => $masuk,
                    'keluar'     => $keluar,
                    'satuan'     => $request->satuan,     // bila ada kolom
                    'lokasi'     => $request->lokasi,
                    'keterangan' => $request->keterangan,
                    'exp_tgl'    => $request->exp_tgl,
                    'catat_tgl'  => $request->catat_tgl,
                    'updated_by' => auth()->id(),
                ]);
                $message = 'Data berhasil diperbarui';
            } else {
                M_BarangStok::create([
                    'barang_id'  => $barang->id,
                    'stok'       => $saldoBaru,           // SALDO setelah transaksi
                    'masuk'      => $masuk,
                    'keluar'     => $keluar,
                    'satuan'     => $request->satuan,     // bila ada kolom
                    'lokasi'     => $request->lokasi,
                    'keterangan' => $request->keterangan,
                    'exp_tgl'    => $request->exp_tgl,
                    'catat_tgl'  => $request->catat_tgl,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function historyPage(M_MstBarang $barang)
    {
        $barang->load(['rak','rakShaft']);
        return view('barang_stok.history', compact('barang'));
    }

    public function history(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:mst_barang,id',
        ]);

        $bsTable = (new M_BarangStok)->getTable(); // 'dat_stok'

        $query = M_BarangStok::from($bsTable.' as bs')
            ->with(['mstBarang.rak','mstBarang.rakShaft','createdBy','updatedBy'])
            ->where('bs.barang_id', $request->barang_id)
            ->orderBy('bs.catat_tgl', 'desc')
            ->orderBy('bs.id', 'desc')
            ->select('bs.*');

        return DataTables::of($query)
            ->addIndexColumn()

            // nama user
            ->editColumn('created_by', fn($row) => $row->createdBy->nama ?? get_user($row->created_by) ?? '-')
            ->editColumn('updated_by', fn($row) => $row->updatedBy->nama ?? get_user($row->updated_by) ?? '-')

            // kolom tambahan untuk tabel
            ->addColumn('barang_nm',  fn($row) => $row->mstBarang->barang_nm ?? '-')
            ->addColumn('rak_nama',   fn($row) => optional($row->mstBarang->rak)->nama_rak ?? '-')
            ->addColumn('shaft_nama', fn($row) => optional($row->mstBarang->rakShaft)->nama_shaft ?? '-')

            // admin tool pada halaman histori (opsional: edit/hapus baris histori)
            ->addColumn('aksi', function ($row) {
                $btnEdit   = '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'">Edit</button>';
                $btnDelete = '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'">Hapus</button>';
                return $btnEdit.' '.$btnDelete;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
     public function historyEdit($id)
    {
        // Ambil baris + relasi minimal untuk ditampilkan di modal
        $row = M_BarangStok::with(['mstBarang:id,barang_nm', 'createdBy', 'updatedBy'])->findOrFail($id);

        // Cek apakah dia baris TERBARU untuk barang terkait
        $latest = M_BarangStok::where('barang_id', $row->barang_id)
                    ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                    ->first();

        return response()->json([
            'row'      => $row,
            'is_latest'=> $latest && $latest->id === $row->id,  // untuk info di UI
        ]);
    }

    public function historyUpdate(Request $r, $id)
    {
        $r->validate([
            'catat_tgl'  => ['required','date'],
            'exp_tgl'    => ['nullable','date'],
            'arah'       => ['required', Rule::in(['masuk','keluar'])],
            'jumlah'     => ['required','integer','min:0'],
            'satuan'     => ['nullable', Rule::in(['tablet','strip'])],
            'lokasi'     => ['nullable','string','max:150'],
            'keterangan' => ['nullable','string'],
        ]);

        DB::transaction(function() use ($r,$id){
            $target = M_BarangStok::lockForUpdate()->findOrFail($id);

            // pastikan hanya baris terbaru yang boleh diubah
            $latest = M_BarangStok::where('barang_id', $target->barang_id)
                        ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                        ->lockForUpdate()->first();
            if (!$latest || $latest->id !== $target->id) {
                abort(422, 'Hanya catatan stok terbaru yang boleh diubah.');
            }

            // saldo sebelum baris target = baris sebelum-nya
            $prev = M_BarangStok::where('barang_id', $target->barang_id)
                        ->where(function($q) use ($target){
                            $q->where('catat_tgl','<',$target->catat_tgl)
                            ->orWhere(function($q2) use ($target){
                                $q2->where('catat_tgl',$target->catat_tgl)
                                    ->where('id','<',$target->id);
                            });
                        })
                        ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                        ->first();

            $jumlah = (int) $r->jumlah;
            $masuk  = $r->arah === 'masuk'  ? $jumlah : 0;
            $keluar = $r->arah === 'keluar' ? $jumlah : 0;

            $saldoBaru = ($prev->stok ?? 0) + ($masuk - $keluar);
            if ($saldoBaru < 0) abort(422, 'Stok tidak mencukupi.');

            $target->update([
                'catat_tgl'  => $r->catat_tgl,
                'exp_tgl'    => $r->exp_tgl,
                'masuk'      => $masuk,
                'keluar'     => $keluar,
                'stok'       => $saldoBaru, // saldo setelah transaksi
                'satuan'     => $r->satuan,
                'lokasi'     => $r->lokasi,
                'keterangan' => $r->keterangan,
                'updated_by' => auth()->id(),
            ]);
        });

        return response()->json(['success'=>true,'message'=>'Histori berhasil diperbarui.']);
    }

    public function historyDestroy($id)
    {
        DB::transaction(function() use ($id){
            $target = M_BarangStok::lockForUpdate()->findOrFail($id);

            // hanya boleh hapus baris terbaru agar saldo konsisten
            $latest = M_BarangStok::where('barang_id', $target->barang_id)
                        ->orderBy('catat_tgl','desc')->orderBy('id','desc')
                        ->lockForUpdate()->first();
            if (!$latest || $latest->id !== $target->id) {
                abort(422, 'Hanya catatan stok terbaru yang boleh dihapus.');
            }

            $target->delete();
        });

        return response()->json(['success'=>true,'message'=>'Histori berhasil dihapus.']);
    }

    public function edit($id)
    {
        $barang = M_BarangStok::with(['mstBarang.rak','mstBarang.rakShaft'])
                    ->where('id', $id)->firstOrFail();
        return response()->json($barang);
    }

    public function destroy($id)
    {
        $barang = M_BarangStok::findOrFail($id);
        $barang->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

   
    public function showBarang($id)
    {
        $barang = M_MstBarang::with(['rak:id,nama_rak', 'rakShaft:id,nama_shaft'])
            ->select('id','barang_nm','rak_id','rak_shaft_id')
            ->findOrFail($id);

        return response()->json([
            'id'            => $barang->id,
            'barang_nm'     => $barang->barang_nm,
            'rak_id'        => $barang->rak_id,        // << dari master
            'rak_shaft_id'  => $barang->rak_shaft_id,  // << dari master
            'rak'           => $barang->rak,           // {id,nama_rak}
            'rak_shaft'     => $barang->rakShaft,      // {id,nama_shaft}
        ]);
    }





    public function storeRak(Request $r)
    {
        $r->validate([
            'nama_rak' => 'required|string|max:100',
            'shafts'   => 'required|array|min:1',
            'shafts.*' => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($r) {
            $rak = M_Rak::create(['nama_rak' => $r->nama_rak]);
            foreach ($r->shafts as $nama) {
                M_RakShaft::create([
                    'rak_id'     => $rak->id,
                    'nama_shaft' => $nama,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Rak tersimpan']);
    }

    public function getShafts(M_Rak $rak) {
        return response()->json($rak->shafts()->select('id','nama_shaft')->orderBy('nama_shaft')->get());
    }

   public function hapus($id)
{
    DB::transaction(function () use ($id) {
        $barang = M_MstBarang::lockForUpdate()->findOrFail($id);

        // hapus semua histori stok
        M_BarangStok::where('barang_id', $barang->id)->delete();

        // hapus master
        $barang->delete();
    });

    return response()->json([
        'success' => true,
        'message' => 'Barang dan seluruh histori berhasil dihapus.'
    ]);
}

public function listRakBarang()
{
    $raks = \App\Models\M_Rak::with([
        'shafts' => fn($q) => $q->orderBy('nama_shaft'),
        'shafts.barangs' => fn($q) => $q->select('id','barang_nm','rak_shaft_id')->orderBy('barang_nm'),
    ])->orderBy('nama_rak')
      ->get(['id','nama_rak']);

    return response()->json($raks);
}


}
