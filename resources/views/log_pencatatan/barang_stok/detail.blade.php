@extends('templates.layout')
@section('breadcrumbs', 'Stok Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                Daftar Stok Barang - {{ $barang->barang_nm }}
            </h4>
            <div class="d-flex align-items-center" id="headerButtons" style="margin-left:auto;">

                <button class="btn btn-primary btn-sm ms-2" id="btnTambah">
                    <i class="fas fa-plus"></i> Tambah Stok
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableBarang" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Stock</th>
                        <th>Tanggal Expired</th>
                        <th>Tanggal Pencatatan</th>
                        <th>Status</th>
                        @if (auth()->user()->pegawai->role_id == 1)
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('barang_stok.form')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let barangId = @json($barang->id ?? null);
            let barangNm = @json($barang->barang_nm ?? '');

            let table = $('#tableBarang').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('barangstok.data') }}",
                    data: function(d) {
                        d.barang_id = barangId;
                    }
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: 'Export',
                        className: 'btn btn-success btn-sm ms-2 mr-2',
                        titleAttr: 'Export ke Excel'
                    },

                ],

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'barang_nm'
                    },
                    {
                        data: 'stok'
                    },
                    {
                        data: 'exp_tgl'
                    },
                    {
                        data: 'catat_tgl'
                    },
                    {
                        data: 'exp_tgl',
                        name: 'status',
                        render: function(data) {
                            if (!data)
                                return '<span class="badge bg-secondary">Tidak ada tanggal</span>';
                            let today = new Date();
                            let exp = new Date(data);
                            let diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));

                            if (diffDays > 60) return '<span class="badge bg-success">Baik</span>';
                            else if (diffDays > 0)
                                return '<span class="badge bg-danger">Hampir Expired</span>';
                            else return '<span class="badge bg-warning">Expired</span>';
                        }
                    },
                    @if (auth()->user()->pegawai->role_id == 1)
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false
                        },
                    @endif
                ]
            });
            table.buttons().container().prependTo('#headerButtons');
            $('#btnTambah').click(function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                if (barangId) {
                    $('#barang_id').val(String(barangId));
                } else {
                    $('#barang_id').val('');
                }
                $('#modalBarang .modal-title').text('Tambah Stok Barang' + (barangNm ? (' - ' + barangNm) :
                    ''));
                $('#modalBarang').modal('show');
            });

            $('#formBarang').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "/stock-barang/store",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#modalBarang').modal('hide');
                            table.ajax.reload();
                            alert(res.message);
                        }
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan. Silakan cek inputan.');
                    }
                });
            });
        });
    </script>
@endpush
