@extends('templates.layout')

@section('breadcrumbs', 'Master Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title d-inline">Daftar Master Barang</h4>
            <button class="btn btn-primary btn-sm float-right" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Barang
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tableBarang" style="width: 100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalBarang" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="formBarang">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="barang_nm">Nama Barang</label>
                            <input type="text" name="barang_nm" id="barang_nm" class="form-control" required>
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
            // Inisialisasi DataTables
            var table = $('#tableBarang').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('mst.barang.ajax') }}",
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Tombol Tambah
            $('#btnTambah').click(function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                $('#modalBarang .modal-title').text('Tambah Barang');
                $('#modalBarang').modal('show');
            });

            // Submit form
            $('#formBarang').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('mst.barang.simpan') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function() {
                        $('#modalBarang').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

            // Edit
            $('body').on('click', '.edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('mst/barang') }}/" + id + "/edit", function(data) {
                    $('#modalBarang .modal-title').text('Edit Barang');
                    $('#id').val(data.id);
                    $('#barang_nm').val(data.barang_nm);
                    $('#modalBarang').modal('show');
                });
            });

            // Delete
            $('body').on('click', '.delete', function() {
                if (confirm('Yakin ingin menghapus data ini?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: "{{ url('mst/barang') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
