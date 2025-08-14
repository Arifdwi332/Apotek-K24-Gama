@extends('templates.layout')
@section('breadcrumbs', 'Input Stock Barang')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4 class="card-title d-inline">Daftar Stok Barang</h4>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalBarang" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formBarang">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Stok Barang</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="barang_id">Nama Barang</label>
                            <select name="barang_id" id="barang_id" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($mstBarang as $barang)
                                    <option value="{{ $barang->id }}">{{ $barang->barang_nm }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="stok">Jumlah Stock</label>
                            <input type="number" name="stok" id="stok" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="exp_tgl">Tanggal Expired</label>
                            <input type="date" name="exp_tgl" id="exp_tgl" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="catat_tgl">Tanggal Pencatatan</label>
                            <input type="date" name="catat_tgl" id="catat_tgl" class="form-control" required>
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
            var table = $('#tableBarang').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('barangstok.data') }}",
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
                        data: 'exp_tgl',
                        name: 'status',
                        render: function(data, type, row) {
                            if (!data)
                                return '<span class="badge bg-secondary">Tidak ada tanggal</span>';

                            let today = new Date();
                            let exp = new Date(data);

                            // Hitung selisih hari
                            let diffTime = exp - today; // milidetik
                            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            if (diffDays > 60) { // lebih dari 2 bulan
                                return '<span class="badge bg-success rounded-circle p-2" style="width:20px; height:20px;" title="' +
                                    diffDays + ' hari tersisa">&nbsp;</span>'; // Hijau
                            } else if (diffDays > 0 && diffDays <= 60) { // <= 2 bulan
                                return '<span class="badge bg-danger rounded-circle p-2" style="width:20px; height:20px;" title="' +
                                    diffDays + ' hari tersisa">&nbsp;</span>'; // Merah
                            } else { // sudah expired
                                return '<span class="badge bg-warning rounded-circle p-2" style="width:20px; height:20px;" title="Expired">&nbsp;</span>'; // Kuning
                            }

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

            // Tambah Stok
            $('#btnTambah').click(function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                $('#modalBarang .modal-title').text('Tambah Stok Barang');
                $('#modalBarang').modal('show');
            });

            // Submit form (Tambah / Edit)
            $('#formBarang').submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                var url = id ? "/stock-barang/store" :
                    "/stock-barang/store"; // updateOrCreate di controller handle otomatis

                $.ajax({
                    url: url,
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

            // Edit Stok
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
        });
    </script>
@endpush
