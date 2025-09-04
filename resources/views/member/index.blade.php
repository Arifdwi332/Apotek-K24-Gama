@extends('templates.layout')

@section('breadcrumbs')
    Member Management
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title d-inline">Daftar Member</h4>
            <button class="btn btn-primary btn-sm float-right" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Member
            </button>
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
                ajax: "{{ route('member.data') }}",
                columns: columns,
                order: order
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
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(msg);
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
                var id = $(this).data('id');
                if (confirm('Yakin ingin menghapus member ini?')) {
                    $.ajax({
                        url: "{{ url('member/delete') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            table.ajax.reload(null, false);
                            alert(res.message);
                        }
                    });
                }
            });
        });
    </script>
@endpush
