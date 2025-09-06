<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\M_BarangStok;
use App\Models\M_MstBarang;
use Illuminate\Http\Request;
class DashboardController extends Controller
{


public function dashboard()
{
    return view('dashboard.index');
}

/** Barang Expired Date: latest row per barang, urut exp_tgl naik */
public function dashboardExpired()
{
    $bs = (new M_BarangStok)->getTable(); // 'dat_stok'

    // latest catat_tgl per barang
    $latestDates = DB::table("$bs as t")
        ->select('t.barang_id', DB::raw('MAX(t.catat_tgl) as max_catat'))
        ->groupBy('t.barang_id');

    // pastikan baris terbaru (kalau di tanggal sama ambil id terbesar)
    $latestIds = DB::table("$bs as t")
        ->joinSub($latestDates, 'ld', function($j){
            $j->on('t.barang_id','=','ld.barang_id')->on('t.catat_tgl','=','ld.max_catat');
        })
        ->select('t.barang_id', DB::raw('MAX(t.id) as last_id'))
        ->groupBy('t.barang_id');

    $q = M_BarangStok::from("$bs as bs")
        ->joinSub($latestIds, 'lx', function($j){
            $j->on('bs.barang_id','=','lx.barang_id')->on('bs.id','=','lx.last_id');
        })
        ->join('mst_barang as mb','mb.id','=','bs.barang_id')
        ->leftJoin('raks as r','r.id','=','mb.rak_id')
        ->leftJoin('rak_shafts as rs','rs.id','=','mb.rak_shaft_id')
        ->whereNotNull('bs.exp_tgl')
        ->orderBy('bs.exp_tgl','asc')
        ->selectRaw('bs.id, bs.barang_id, bs.exp_tgl, mb.barang_nm, r.nama_rak, rs.nama_shaft');

    return DataTables::of($q)
        ->addIndexColumn()
        ->addColumn('rak_nama', fn($row) => $row->nama_rak ?? '-')
        ->addColumn('shaft_nama', fn($row) => $row->nama_shaft ?? '-')
        ->addColumn('barang_nm', fn($row) => $row->barang_nm ?? '-')
        ->make(true);
}

/** Fast Moving: barang dengan jumlah catatan terbanyak (default 30 hari terakhir) */
public function dashboardFast(Request $r)
{
    $days = (int)($r->get('days', 30));
    $bs   = (new M_BarangStok)->getTable(); // 'dat_stok'

    $counts = DB::table("$bs as t")
        ->when($days > 0, fn($q)=>$q->whereDate('t.catat_tgl','>=', Carbon::today()->subDays($days)))
        ->select('t.barang_id', DB::raw('COUNT(*) as total'))
        ->groupBy('t.barang_id');

    $q = DB::table('mst_barang as mb')
        ->joinSub($counts, 'c', 'mb.id', '=', 'c.barang_id')
        ->orderByDesc('c.total')
        ->select('mb.id','mb.barang_nm','c.total');

    return DataTables::of($q)
        ->addIndexColumn()
        ->addColumn('barang_nm', fn($row)=>$row->barang_nm)
        ->addColumn('total', fn($row)=>$row->total) // bisa disembunyikan di UI
        ->make(true);
}
}