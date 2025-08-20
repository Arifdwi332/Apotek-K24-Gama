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
            <table class="table table-bordered table-striped" id="tableLog" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Stock</th>
                        <th>Tanggal Expired</th>
                        <th>Tanggal Pencatatan</th>
                        <th>Tanggal Diperbarui</th>
                        <th>Dibuat Oleh</th>
                        <th>Diperbarui Oleh</th>

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

            table = $('#tableLog').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('logstok.data') }}",
                    data: function(d) {
                        d.barang_id = barangId;
                    }
                },
                dom: 'Bfrtip', // <--- ini
                buttons: [{
                        extend: 'excel',
                        text: 'Export',
                        className: 'btn btn-success btn-sm ms-2 mr-2',
                        titleAttr: 'Export ke Excel'
                    },

                ],
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
                        data: 'stok',
                        name: 'stok'
                    },
                    {
                        data: 'exp_tgl',
                        name: 'exp_tgl'
                    },
                    {
                        data: 'catat_tgl',
                        name: 'catat_tgl'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },

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
