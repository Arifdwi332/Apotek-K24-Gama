@extends('templates.layout')
@section('breadcrumbs', 'Report Stok Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title mb-0">Laporan Stok Barang</h4>
        </div>

        <div class="card-body">
            {{-- FILTER --}}
            <form id="filterForm" class="mb-3">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Rak</label>
                        <select name="rak_id" id="rak_id" class="form-control">
                            <option value="">-- Semua --</option>
                            @foreach ($raks as $rak)
                                <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Shaft</label>
                        <select name="rak_shaft_id" id="rak_shaft_id" class="form-control">
                            <option value="">-- Semua --</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Nama Barang</label>
                        <select id="filter_barang_id" class="form-control">
                            <option value="">-- Semua Barang --</option>
                            @foreach ($barangList as $b)
                                <option value="{{ $b->id }}">{{ $b->barang_nm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="aman">Aman</option>
                            <option value="hampir">Hampir Expired</option>
                            <option value="expired">Expired</option>
                            <option value="tanpa">Tanpa Tanggal</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Dibuat Oleh</label>
                        <select id="filter_created_by" class="form-control">
                            <option value="">-- Semua User --</option>
                            @foreach ($userList as $u)
                                <option value="{{ $u->id }}">{{ $u->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 d-flex align-items-end">
                        <button type="button" id="btnFilter" class="btn btn-primary mr-2">Filter</button>
                        <button type="button" id="btnClear" class="btn btn-secondary">Clear Filter</button>
                    </div>
                </div>
            </form>

            {{-- TABEL --}}
            <table class="table table-bordered table-striped" id="tableReport" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Rak</th>
                        <th>Shaft</th>
                        <th>Nama Barang</th>
                        <th>Expired Date</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Sisa</th>
                        <th>Dibuat Oleh</th>
                        <th>Diubah Oleh</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // load shaft dinamis
            $('#rak_id').on('change', function() {
                let rakId = $(this).val();
                $('#rak_shaft_id').empty().append('<option value="">-- Semua --</option>');
                if (!rakId) return;
                $.get("{{ url('/rak') }}/" + rakId + "/shafts", function(rows) {
                    rows.forEach(r => {
                        $('#rak_shaft_id').append(
                            `<option value="${r.id}">${r.nama_shaft}</option>`);
                    });
                });
            });

            // DataTables
            let table = $('#tableReport').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('barangstok.reportData') }}",
                    data: function(d) {
                        d.tgl_mulai = $('#tgl_mulai').val();
                        d.tgl_selesai = $('#tgl_selesai').val();
                        d.rak_id = $('#rak_id').val();
                        d.rak_shaft_id = $('#rak_shaft_id').val();
                        d.barang_id = $('#filter_barang_id').val();
                        d.created_by = $('#filter_created_by').val();
                        d.status = $('#status').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'catat_tgl',
                        name: 'catat_tgl'
                    },
                    {
                        data: 'rak_nama',
                        name: 'rak_nama'
                    },
                    {
                        data: 'shaft_nama',
                        name: 'shaft_nama'
                    },
                    {
                        data: 'barang_nm',
                        name: 'barang_nm'
                    },
                    {
                        data: 'exp_tgl',
                        name: 'exp_tgl'
                    },
                    {
                        data: 'exp_tgl',
                        name: 'status',
                        render: function(data) {
                            if (!data)
                                return '<span class="badge bg-secondary">Tidak ada tanggal</span>';
                            const today = new Date();
                            const exp = new Date(data);
                            const diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
                            if (diffDays > 60) return '<span class="badge bg-success" title="' +
                                diffDays +
                                ' hari tersisa">Aman</span>';
                            if (diffDays > 0) return '<span class="badge bg-danger"  title="' +
                                diffDays +
                                ' hari tersisa">Hampir Expired</span>';
                            return '<span class="badge bg-warning">Expired</span>';
                        }
                    },
                    {
                        data: 'masuk',
                        name: 'masuk'
                    },
                    {
                        data: 'keluar',
                        name: 'keluar'
                    },
                    {
                        data: 'stok',
                        name: 'stok'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },
                ],
                order: [
                    [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i>',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i>',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });

            // tombol filter
            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });
            $('#btnClear').on('click', function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });
        });
    </script>
@endpush
