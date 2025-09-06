@extends('templates.layout')
@section('breadcrumbs', 'Dashboard')

@section('content')
    <div class="row">
        {{-- Barang Expired --}}
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Barang Expired Date</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="tblExpired" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Rak</th>
                                <th>Shaft</th>
                                <th>Nama Barang</th>
                                <th>Expired Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- Fast Moving --}}
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Barang Fast Moving</h5>
                    <div class="small text-muted">30 hari terakhir</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="tblFast" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th class="d-none">Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            // helper status badge (sama seperti report kamu)
            function renderStatus(expStr) {
                if (!expStr) return '<span class="badge bg-secondary">Tidak ada tanggal</span>';
                const today = new Date();
                const exp = new Date(expStr);
                const diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
                if (diffDays > 60) return '<span class="badge bg-success" title="' + diffDays +
                    ' hari tersisa">Aman</span>';
                if (diffDays > 0) return '<span class="badge bg-danger"  title="' + diffDays +
                    ' hari tersisa">Hampir Expired</span>';
                return '<span class="badge bg-warning">Expired</span>';
            }

            // Expired
            $('#tblExpired').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.expired') }}"
                },
                order: [
                    [4, 'asc']
                ], // exp_tgl paling dekat dulu
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
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
                        name: 'exp_tgl',
                        render: function(data) {
                            if (!data) return '-';
                            let date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'exp_tgl',
                        name: 'status',
                        render: renderStatus
                    }
                ]
            });

            // Fast Moving
            $('#tblFast').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.fast') }}",
                    data: d => {
                        d.days = 30
                    }
                },
                paging: false,
                searching: false,
                info: false,
                order: [
                    [2, 'desc']
                ], // total desc
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'barang_nm',
                        name: 'barang_nm'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        className: 'd-none'
                    }
                ]
            });

        });
    </script>
@endpush
