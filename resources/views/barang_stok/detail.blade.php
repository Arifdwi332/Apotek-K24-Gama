@extends('templates.layout')
@section('breadcrumbs', 'Stok Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                Stok {{ $barang->barang_nm }}
            </h4>
            <div class="d-flex align-items-center" id="headerButtons" style="margin-left:auto;">

                <button class="btn btn-primary btn-sm d-none d-sm-inline-flex" id="btnTambah"
                    style="white-space: nowrap;margin-left: 9px">
                    Tambah Stok
                </button>

                <!-- Tambah stok mobile -->
                <button class="btn btn-primary btn-sm d-inline-flex d-sm-none" id="btnTambahMobile"
                    style="width:60px; height:36px; display:inline-flex; align-items:center; justify-content:center; margin-top:-7px; margin-left: 9px">
                    <i class="fas fa-plus  fa-sm"></i>
                </button>
            </div>
        </div>

        <div class="card-body table-responsive">
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
                        text: 'Export', // desktop
                        className: 'btn btn-success btn-sm d-none d-sm-inline-flex',
                        titleAttr: 'Export ke Excel',
                        init: function(api, node, config) {
                            $(node).css({
                                'display': 'inline-flex',
                                'align-items': 'center',
                                'justify-content': 'center',
                                'height': '31px',
                                'padding': '0.25rem 0.5rem',
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

            $('body').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("/stock-barang/edit/" + id, function(data) {
                    $('#id').val(data.id);
                    $('#barang_id').val(data.barang_id);
                    $('#stok').val(data.stok);
                    $('#exp_tgl').val(data.exp_tgl);
                    $('#catat_tgl').val(data.catat_tgl);
                    $('#modalBarang .modal-title').text('Edit Stok Barang');
                    $('#modalBarang').modal('show');
                });
            });

            // Hapus Stok
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                if (confirm('Yakin ingin menghapus data ini?')) {
                    $.ajax({
                        url: "/stock-barang/delete/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            table.ajax.reload();
                            alert(res.message);
                        }
                    });
                }
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
