@extends('templates.layout')

@section('breadcrumbs')
    Member Management
@endsection

@section('content')
    <div class="card shadow">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class=" mb-0">
                Daftar Member
            </h5>
            <div class="d-flex align-items-center" id="headerButtons" style="margin-left:auto;">

                <button class="btn btn-primary btn-sm d-none d-sm-inline-flex" id="btnTambah"
                    style="white-space: nowrap;margin-left: 9px">
                    Tambah Member
                </button>

                <button class="btn btn-primary btn-sm d-inline-flex d-sm-none" id="btnTambahMobile"
                    style="width:60px; height:36px; display:inline-flex; align-items:center; justify-content:center; margin-top:-7px; margin-left: 9px">
                    <i class="fas fa-plus  fa-sm"></i>
                </button>
            </div>

        </div>

        <div class="card-body">

            <div class="mb-2">
                <label for="qHp" class="mb-1" style="font-size: 0.85rem;">Cari Member</label>
                <div class="d-flex">
                    <input type="text" id="qHp" class="form-control form-control-sm" placeholder="nomor hp member"
                        style="max-width:200px;">
                    <button class="btn btn-outline-primary btn-sm ml-2" id="btnCariMember">Cari</button>
                    <button class="btn btn-outline-secondary btn-sm ml-2" id="btnResetMember">Reset</button>
                </div>
                @if (!is_admin())
                    <small class="text-muted">Non-admin harus mencari dulu untuk menampilkan data.</small>
                @endif
            </div>

        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableMember" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>No HP</th>
                        <th>Jenis Kelamin</th>
                        <th>Usia</th>
                        <th>Alamat</th>

                        @if (is_admin())
                            <th>Dibuat Pada</th>
                            <th>Dibuat Oleh</th>
                            <th>Diubah Pada</th>
                            <th>Diubah Oleh</th>
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <!-- Modal Form -->
    <div class="modal fade" id="modalMember" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formMember">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Member</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="no_hp">No HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                <option value="">-</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="usia">Usia</label>
                            <input type="number" name="usia" id="usia" class="form-control" min="0"
                                max="150">
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- created_by / updated_by otomatis diisi di controller pakai auth()->id() -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const IS_ADMIN = {!! is_admin() ? 'true' : 'false' !!};

            const columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_lengkap',
                    name: 'nama_lengkap'
                },
                {
                    data: 'no_hp',
                    name: 'no_hp',
                    defaultContent: '-'
                },
                {
                    data: 'jenis_kelamin',
                    name: 'jenis_kelamin',
                    render: function(data) {
                        return data === 'L' ? 'Laki-laki' : (data === 'P' ? 'Perempuan' : '-');
                    }
                },
                {
                    data: 'usia',
                    name: 'usia',
                    defaultContent: '-'
                },
                {
                    data: 'alamat',
                    name: 'alamat',
                    defaultContent: '-'
                },
            ];

            if (IS_ADMIN) {
                columns.push({
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    defaultContent: '-'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'updated_by',
                    name: 'updated_by',
                    defaultContent: '-'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }, );
            }

            const order = IS_ADMIN ? [
                [6, 'desc']
            ] : [
                [1, 'asc']
            ];

            const table = $('#tableMember').DataTable({
                processing: true,
                serverSide: true,
                searching: false, // disable search default
                deferLoading: IS_ADMIN ? null : 0, // non-admin: tidak load awal
                ajax: {
                    url: "{{ route('member.data') }}",
                    data: function(d) {
                        d.q = $('#qHp').val(); // kirim q = nomor HP
                    }
                },
                columns: columns,
                order: order,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: 'Export', // desktop
                        className: 'btn btn-success btn-sm d-none d-sm-inline-flex',
                        titleAttr: 'Export ke Excel',
                        exportOptions: {
                            columns: ':visible:not(:last-child)',
                        },
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
            });
            table.buttons().container().prependTo('#headerButtons');

            $('#btnCariMember').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#btnResetMember').on('click', function(e) {
                e.preventDefault();
                $('#qHp').val(''); // kosongkan input
                table.ajax.reload(); // reload tabel
            });

            $('#btnTambah').click(function() {
                $('#formMember')[0].reset();
                $('#id').val('');
                $('#modalMember .modal-title').text('Form Member');
                $('#modalMember').modal('show');
            });

            $('#formMember').submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                var url = id ? "{{ url('member/update') }}/" + id : "{{ route('member.store') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#modalMember').modal('hide');
                            table.ajax.reload(null, false);
                            swalSuccess(res.message);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        swalError(msg);
                    }
                });
            });

            $('body').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('member/edit') }}/" + id, function(data) {
                    $('#id').val(data.id_member);
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $('#no_hp').val(data.no_hp);
                    $('#jenis_kelamin').val(data.jenis_kelamin);
                    $('#usia').val(data.usia);
                    $('#alamat').val(data.alamat);
                    $('#modalMember .modal-title').text('Edit Member');
                    $('#modalMember').modal('show');
                });
            });

            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                swalConfirm('Yakin ingin menghapus member ini?', function() {
                    $.ajax({
                        url: "{{ url('member/delete') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            table.ajax.reload(null, false);
                            swalSuccess(res.message);
                        },
                        error: function(xhr) {
                            let msg = (xhr.responseJSON && xhr.responseJSON.message) ?
                                xhr.responseJSON.message : 'Gagal menghapus.';
                            swalError(msg);
                        }
                    });
                });
            });
        });

        function swalSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: msg || 'Operasi berhasil.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function swalError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg || 'Terjadi kesalahan.',
            });
        }

        function swalConfirm(msg, callback) {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi',
                html: msg || 'Yakin ingin melanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) callback();
            });
        }
    </script>
@endpush
