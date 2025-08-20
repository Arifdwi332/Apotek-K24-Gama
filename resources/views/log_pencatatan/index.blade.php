@extends('templates.layout')
@section('breadcrumbs', 'Input Stock Barang')

@section('content')
    <div class="row">
        @foreach ($mstBarang as $barang)
            <div class="col-6 col-md-3 mb-3">
                <div class="card card-outline card-primary h-100">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <h5 class="m-0 text-center">
                            <a href="{{ route('logstok.show', $barang->id) }}" class="stretched-link text-decoration-none">
                                {{ $barang->barang_nm }}
                            </a>
                        </h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>




    <div class="card shadow" id="cardTable" style="display:none;">
        <div class="card-header">
            <h4 class="card-title d-inline">Daftar Stok Barang <span id="titleBarang"></span></h4>
            <button class="btn btn-primary btn-sm float-right" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Stok
            </button>
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
                        <th>Tanggal Diperbarui</th>
                        <th>Dibuat Oleh</th>
                        <th>Diperbarui Oleh</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('log_pencatatan.form')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var selectedBarangId = null;
            var selectedBarangNm = null;
            var table = null;

            $('.pilih-barang').click(function() {
                selectedBarangId = $(this).data('id');
                selectedBarangNm = $(this).find('h5').text();

                $('#titleBarang').text('- ' + selectedBarangNm);
                $('#cardTable').show();

                if (table) {
                    table.destroy();
                }

                table = $('#tableBarang').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('barangstok.data') }}",
                        data: function(d) {
                            d.barang_id = selectedBarangId;
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
            });

            // Tambah stok → otomatis bawa barang yang dipilih
            $('#btnTambah').click(function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                $('#barang_id').val(selectedBarangId);
                $('#modalBarang .modal-title').text('Tambah Stok Barang - ' + selectedBarangNm);
                $('#modalBarang').modal('show');
            });

            // Submit form
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
