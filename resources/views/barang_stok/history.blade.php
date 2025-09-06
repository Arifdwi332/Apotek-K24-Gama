@extends('templates.layout')
@section('breadcrumbs', 'Histori Stok Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Histori Stok: {{ $barang->barang_nm }}
                <small class="text-muted">[Rak: {{ $barang->rak->nama_rak ?? '-' }}, Shaft:
                    {{ $barang->rakShaft->nama_shaft ?? '-' }}]</small>
            </h4>
            <a href="{{ route('barangstok.input_stock') }}" class="btn btn-light btn-sm float-right">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableHistory" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Catat</th>
                        <th>Rak</th>
                        <th>Shaft</th>
                        <th>Nama Barang</th>
                        <th>Expired</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Saldo</th>
                        <th>Satuan</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th>Dibuat Oleh</th>
                        <th>Diubah Oleh</th>
                        <th>Admin Tool</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalEditHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditHistory" class="modal-content">
                @csrf
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Catatan Stok</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Pencatatan</label>
                        <input type="date" class="form-control" id="edit_catat_tgl" name="catat_tgl" required>
                    </div>
                    <div class="form-group">
                        <label>Expired Date</label>
                        <input type="date" class="form-control" id="edit_exp_tgl" name="exp_tgl">
                    </div>

                    <div class="form-group">
                        <label class="d-block">Arah</label>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_arah_masuk" name="arah" value="masuk"
                                class="custom-control-input" checked>
                            <label class="custom-control-label" for="edit_arah_masuk">Masuk</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_arah_keluar" name="arah" value="keluar"
                                class="custom-control-input">
                            <label class="custom-control-label" for="edit_arah_keluar">Keluar</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="d-block">Satuan</label>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_sat_tablet" name="satuan" value="tablet"
                                class="custom-control-input" checked>
                            <label class="custom-control-label" for="edit_sat_tablet">Tablet</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_sat_strip" name="satuan" value="strip"
                                class="custom-control-input">
                            <label class="custom-control-label" for="edit_sat_strip">Strip</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Lokasi</label>
                        <textarea class="form-control" id="edit_lokasi" name="lokasi" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function() {
            $('#tableHistory').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('barangstok.history') }}",
                    data: {
                        barang_id: "{{ $barang->id }}"
                    }
                },
                order: [
                    [1, 'desc']
                ], // sort Tgl Catat
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'catat_tgl',
                        name: 'catat_tgl',
                        defaultContent: '-'
                    },

                    {
                        data: 'rak_nama',
                        name: 'rak_nama',
                        defaultContent: '-'
                    },
                    {
                        data: 'shaft_nama',
                        name: 'shaft_nama',
                        defaultContent: '-'
                    },
                    {
                        data: 'barang_nm',
                        name: 'barang_nm',
                        defaultContent: '-'
                    },

                    {
                        data: 'exp_tgl',
                        name: 'exp_tgl',
                        defaultContent: '-'
                    },

                    {
                        data: 'masuk',
                        name: 'masuk',
                        defaultContent: '0'
                    },
                    {
                        data: 'keluar',
                        name: 'keluar',
                        defaultContent: '0'
                    },
                    {
                        data: 'stok',
                        name: 'stok',
                        defaultContent: '0'
                    },

                    {
                        data: 'satuan',
                        name: 'satuan',
                        defaultContent: '-'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi',
                        defaultContent: '-'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        defaultContent: '-'
                    },

                    {
                        data: 'created_by',
                        name: 'created_by',
                        defaultContent: '-'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by',
                        defaultContent: '-'
                    },

                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        targets: [0, 14],
                        className: 'text-center'
                    },
                    {
                        targets: [6, 7, 8],
                        className: 'text-right'
                    } // angka rata kanan
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel fa-lg"></i>',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf fa-lg"></i>',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });
            // buka modal edit
            // === buka modal edit ===
            $('body').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const editUrl = "{{ url('/stock-barang/history') }}/" + id + "/edit";

                $.get(editUrl)
                    .done(function(res) {
                        const r = res.row;

                        $('#edit_id').val(r.id);
                        $('#edit_catat_tgl').val(r.catat_tgl ? r.catat_tgl.substring(0, 10) : '');
                        $('#edit_exp_tgl').val(r.exp_tgl ? r.exp_tgl.substring(0, 10) : '');

                        if ((r.keluar || 0) > 0) {
                            $('#edit_arah_keluar').prop('checked', true);
                            $('#edit_jumlah').val(r.keluar);
                        } else {
                            $('#edit_arah_masuk').prop('checked', true);
                            $('#edit_jumlah').val(r.masuk || 0);
                        }

                        (r.satuan === 'strip' ? $('#edit_sat_strip') : $('#edit_sat_tablet')).prop(
                            'checked', true);
                        $('#edit_lokasi').val(r.lokasi || '');
                        $('#edit_keterangan').val(r.keterangan || '');

                        if (res.is_latest !== true) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: 'Hanya catatan terbaru yang bisa diubah/dihapus.'
                            });
                        }

                        $('#modalEditHistory').modal('show');
                    })
                    .fail(() => Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat data.'
                    }));
            });

            // === submit edit (HAPUS deklarasi const id yang dobel) ===
            $('#formEditHistory').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_id').val(); // <-- cukup sekali
                const updateUrl = "{{ url('/stock-barang/history') }}/" + id;

                $.post(updateUrl, $(this).serialize())
                    .done(function(res) {
                        $('#modalEditHistory').modal('hide');
                        $('#tableHistory').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Histori diperbarui.'
                        });
                    })
                    .fail(xhr => {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                            .message : 'Gagal menyimpan.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    });
            });

            // === hapus baris (KONFIRMASI DULU, baru DELETE) ===
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const deleteUrl = "{{ url('/stock-barang/history') }}/" + id;

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus catatan?',
                    text: 'Tindakan ini tidak bisa dibatalkan.',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then(res => {
                    if (!res.isConfirmed) return;

                    $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            }
                        })
                        .done(function(resp) {
                            $('#tableHistory').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: resp.message || 'Histori dihapus.'
                            });
                        })
                        .fail(xhr => {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                .responseJSON.message : 'Gagal menghapus.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: msg
                            });
                        });
                });
            });


        });
    </script>
@endpush
