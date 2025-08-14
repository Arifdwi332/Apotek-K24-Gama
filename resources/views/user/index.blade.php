@extends('templates.layout')

@section('breadcrumbs')
    User Management
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title d-inline">Daftar User</h4>
            <button class="btn btn-primary btn-sm float-right" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableUser" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formUser">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form User</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pegawai_nm">Nama Pegawai</label>
                            <input type="text" name="pegawai_nm" id="pegawai_nm" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="1">Admin</option>
                                <option value="2">Karyawan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status_st">Status</label>
                            <select name="status_st" id="status_st" class="form-control" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <small>(isi jika ingin ubah)</small></label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>

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
        $(document).ready(function() {
            var table = $('#tableUser').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pegawai_nm',
                        name: 'pegawai_nm'
                    },
                    {
                        data: 'role.role_nm',
                        name: 'role.role_nm'
                    },
                    {
                        data: 'status_st',
                        name: 'status_st',
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge badge-success">Aktif</span>' :
                                '<span class="badge badge-danger">Tidak Aktif</span>';
                        }
                    },
                    {
                        data: 'username',
                        name: 'username',
                        render: function(data, type, row) {
                            return row.user ? row.user.username : '-';
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btnTambah').click(function() {
                $('#formUser')[0].reset();
                $('#id').val('');
                $('#modalUser').modal('show');
            });

            $('#formUser').submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                var url = id ? "{{ url('user/update') }}/" + id : "{{ route('user.store') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#modalUser').modal('hide');
                            table.ajax.reload();
                            alert(res.message);
                        }
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan');
                    }
                });
            });

            $('body').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("/user/edit/" + id, function(data) {
                    $('#id').val(data.pegawai_id);
                    $('#pegawai_nm').val(data.pegawai_nm);
                    $('#role_id').val(data.role_id);
                    $('#status_st').val(data.status_st);
                    $('#username').val(data.user ? data.user.username : '');
                    $('#password').val('');
                    $('#modalUser .modal-title').text('Edit User');
                    $('#modalUser').modal('show');
                });
            });

            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                if (confirm('Yakin ingin menghapus user ini?')) {
                    $.ajax({
                        url: "/user/delete/" + id,
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
        });
    </script>
@endpush
