@extends('templates.layout')
@section('breadcrumbs', 'Stok Barang')

@section('content')

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                Stok {{ $barang->barang_nm }}
            </h4>
            <div class="d-flex flex-nowrap align-items-center" id="headerButtons" style="gap: 0.5rem; margin-left:auto;">

                <!-- Tambah stok desktop -->
                <button class="btn btn-primary btn-sm d-none d-sm-inline-flex" id="btnTambah" style="white-space: nowrap;">
                    Tambah Stok
                </button>

                <!-- Tambah stok mobile -->
                <button class="btn btn-primary btn-sm d-inline-flex d-sm-none" id="btnTambahMobile"
                    style="width:60px; height:36px; display:inline-flex; align-items:center; justify-content:center; margin-top:-7px;">
                    <i class="fas fa-plus  fa-sm"></i>
                </button>

            </div>

        </div>



        <div class="card-body">
            <table class="table table-bordered table-striped table-responsive" id="tableLog" style="width:100%">
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
                        text: 'Export', // desktop
                        className: 'btn btn-success btn-sm d-none d-sm-inline-flex',
                        titleAttr: 'Export ke Excel',
                        init: function(api, node, config) {
                            $(node).css({
                                'display': 'inline-flex',
                                'align-items': 'center',
                                'justify-content': 'center',
                                'height': '31px', // samain tinggi
                                'padding': '0.25rem 0.5rem', // samain padding dengan .btn-sm
                                'font-size': '0.875rem',
                                'line-height': '1.5',
                                'border-radius': '0.25rem'
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-print fa-sm"></i>', // mobile icon
                        className: 'btn btn-success btn-sm d-inline-flex d-sm-none',
                        titleAttr: 'Export ke Excel',
                        init: function(api, node, config) {
                            $(node).css({
                                'display': 'inline-flex',
                                'align-items': 'center',
                                'justify-content': 'center',
                                'height': '36px',
                                'width': '15px',
                                'padding': '0.25rem',
                                'border-radius': '0.25rem'
                            });
                        }
                    }
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

            $('#btnTambah, #btnTambahMobile').click(function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                if (barangId) {
                    $('#barang_id').val(String(barangId));
                } else {
                    $('#barang_id').val('');
                }
                $('#modalBarang .modal-title').text(
                    'Tambah Stok Barang' + (barangNm ? (' - ' + barangNm) : '')
                );
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
