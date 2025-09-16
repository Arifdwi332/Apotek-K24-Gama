@extends('templates.layout')
@section('breadcrumbs', 'Input Stock Barang')

@section('content')
    <div class="card shadow" id="cardTable">
        <div class="card-header">
            <h4 class=" d-inline">Daftar Stok Barang <span id="titleBarang"></span></h4>
            <button class="btn btn-primary btn-sm float-right" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Stok
            </button>
            <button class="btn btn-primary btn-sm float-right mr-2" id="btnTambahRak">
                <i class="fas fa-plus"></i> Tambah Rak
            </button>
            <button class="btn btn-secondary btn-sm float-right mr-2" id="btnListRakBarang">
                <i class="fas fa-list-ul"></i> Lihat Rak & Barang
            </button>

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableBarang" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Rak</th>
                            <th>Shaft</th>
                            @if (is_admin())
                                <th>Masuk</th>
                                <th>Keluar</th>
                            @endif
                            <th>Jumlah Stock</th>
                            <th>Tanggal Expired</th>
                            <th>Tanggal Pencatatan</th>
                            <th>Status</th>
                            @if (is_admin())
                                <th>Dibuat Oleh</th>
                                <th>Diubah Oleh</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Rak --}}
    <div class="modal fade" id="modalRak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formRak" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Nama Rak</h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_rak">Nama Rak</label>
                        <input type="text" class="form-control" id="nama_rak" name="nama_rak" required>
                    </div>

                    <label class="mb-2">Jumlah Shaft</label>
                    <div id="shaftList"></div>

                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="btnAddShaft">Tambah</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Catat Stok (BARU) --}}
    <div class="modal fade" id="modalCatat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formCatat" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="barang_id" id="barang_id">

                <div class="modal-header">
                    <h5 class="modal-title">Catat Stok <span id="catat_nama_barang" class="text-muted"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    {{-- (opsional) tampilkan nama barang readonly --}}
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" id="catat_nama_barang_input" class="form-control" readonly>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Pilih Rak</label>
                            <select name="rak_id" id="catat_rak_id" class="form-control"
                                data-shafts-url-template="{{ url('/rak/__RAK_ID__/shafts') }}" required>
                                <option value="">-- Pilih Rak --</option>
                                @foreach ($raks as $rak)
                                    <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group col-md-6">
                            <label>Pilih Shaft</label>
                            <select name="rak_shaft_id" id="catat_rak_shaft_id" class="form-control" required>
                                <option value="">-- Pilih Shaft --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tanggal Pencatatan</label>
                            <input type="date" name="catat_tgl" id="catat_tgl" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Expired Date</label>
                            <input type="date" name="exp_tgl" id="exp_tgl" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        {{-- Catat Stock: RADIO + input angka bebas --}}
                        <div class="form-group col-md-6">
                            <label class="d-block">Catat Stock</label>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="arah_masuk" name="arah" class="custom-control-input"
                                    value="masuk" checked>
                                <label class="custom-control-label" for="arah_masuk">Masuk</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="arah_keluar" name="arah" class="custom-control-input"
                                    value="keluar">
                                <label class="custom-control-label" for="arah_keluar">Keluar</label>
                            </div>

                            <input type="number" name="stok" id="stok" class="form-control mt-2"
                                placeholder="Jumlah" required>
                        </div>

                        {{-- Satuan: RADIO --}}
                        {{-- Satuan: RADIO (MODAL CATAT) --}}
                        <div class="form-group col-md-6">
                            <label class="d-block">Satuan</label>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="catat_sat_tablet" name="satuan" class="custom-control-input"
                                    value="tablet" checked>
                                <label class="custom-control-label" for="catat_sat_tablet">Tablet</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="catat_sat_strip" name="satuan" class="custom-control-input"
                                    value="strip">
                                <label class="custom-control-label" for="catat_sat_strip">Strip</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="catat_sat_botol" name="satuan" class="custom-control-input"
                                    value="botol">
                                <label class="custom-control-label" for="catat_sat_botol">Botol</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="catat_sat_pcs" name="satuan" class="custom-control-input"
                                    value="pcs">
                                <label class="custom-control-label" for="catat_sat_pcs">PCS</label>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Lokasi</label>
                        <textarea name="lokasi" id="lokasi" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modalRakBarang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lihat List Rak dan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="tblRakBarang">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th style="width:160px;">Nama Rak</th>
                                    <th style="width:140px;">Shaft</th>
                                    <th>Daftar Barang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Memuat…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Rak (mirip Tambah Rak) -->
    <div class="modal fade" id="modalEditRak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditRak" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_rak_id" name="rak_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Rak</h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_rak">Nama Rak</label>
                        <input type="text" class="form-control" id="edit_nama_rak" name="nama_rak" required>
                    </div>

                    <label class="mb-2">Jumlah Shaft</label>
                    <div id="editShaftList"></div>

                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2"
                        id="btnEditAddShaft">Tambah</button>
                    <small class="text-muted d-block mt-2">
                        Menghapus baris shaft di sini akan menghapus shaft di server jika tidak sedang dipakai barang.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    @include('barang_stok.form')
@endsection

@push('scripts')
    <script>
        $(function() {

            // ===== helper =====
            function loadShaftsForRak($rakSelect, $shaftSelect, rakId, selectedShaftId = null) {
                const tmpl = $rakSelect.data('shafts-url-template') || "{{ url('/rak/__RAK_ID__/shafts') }}";
                const url = tmpl.replace('__RAK_ID__', rakId);

                $shaftSelect.empty().append('<option value="">-- Pilih Shaft --</option>');
                if (!rakId) return;

                $.get(url).done(function(rows) {
                    rows.forEach(function(r) {
                        const opt = new Option(r.nama_shaft, r.id, false, Number(
                            selectedShaftId) === Number(r.id));
                        $shaftSelect.append(opt);
                    });
                    // refresh selectpicker jika dipakai
                    if ($shaftSelect.hasClass('selectpicker') && typeof $shaftSelect.selectpicker ===
                        'function') {
                        $shaftSelect.selectpicker('refresh');
                    }
                });
            }

            // ===== SweetAlert2 helpers =====
            function swalSuccess(msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: msg || 'Operasi berhasil.',
                    confirmButtonText: 'OK',

                });
            }

            function swalError(msg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: msg || 'Terjadi kesalahan.',
                    confirmButtonText: 'OK'
                });
            }

            function swalFromXhr(xhr, fallback = 'Terjadi kesalahan. Periksa input.') {
                let title = 'Gagal';
                let text = fallback;
                let html = '';

                if (xhr && xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        text = xhr.responseJSON
                            .message;
                    }
                    if (xhr.responseJSON.errors) {

                        const items = [];
                        Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                            xhr.responseJSON.errors[key].forEach(function(e) {
                                items.push(`<li>${e}</li>`);
                            });
                        });
                        if (items.length) {
                            html = `<ul style="text-align:left;margin-left:1rem;">${items.join('')}</ul>`;
                        }
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: html ? undefined : text,
                    html: html || undefined,
                    confirmButtonText: 'OK'
                });
            }

            const IS_ADMIN = @json(is_admin());

            let selectedBarangId = null;
            let selectedBarangNm = null;

            // Susun kolom secara dinamis
            const columns = [{
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
                    data: 'satuan',
                    name: 'satuan'
                },
                {
                    data: 'rak_nama',
                    name: 'rak_nama'
                },
                {
                    data: 'shaft_nama',
                    name: 'shaft_nama'
                },
            ];

            if (IS_ADMIN) {
                columns.push({
                    data: 'masuk',
                    name: 'masuk'
                }, {
                    data: 'keluar',
                    name: 'keluar'
                }, );
            }

            columns.push({
                data: 'stok',
                name: 'stok'
            }, {
                data: 'exp_tgl',
                name: 'exp_tgl',
                render: function(data) {
                    if (!data) return '-';
                    let date = new Date(data);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }, {
                data: 'catat_tgl',
                name: 'catat_tgl'
            }, {
                data: 'exp_tgl',
                name: 'status',
                render: function(data) {
                    if (!data) return '<span class="badge bg-secondary">Tidak ada tanggal</span>';
                    const today = new Date();
                    const exp = new Date(data);
                    const diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
                    if (diffDays > 60) return '<span class="badge bg-success" title="' + diffDays +
                        ' hari tersisa">Aman</span>';
                    if (diffDays > 0) return '<span class="badge bg-danger"  title="' + diffDays +
                        ' hari tersisa">Hampir Expired</span>';
                    return '<span class="badge bg-warning">Expired</span>';
                }
            });

            if (IS_ADMIN) {
                columns.push({
                    data: 'created_by',
                    name: 'created_by'
                }, {
                    data: 'updated_by',
                    name: 'updated_by'
                }, );
            }

            columns.push({
                data: 'aksi',
                name: 'aksi',
                orderable: false,
                searchable: false
            }, );

            const table = $('#tableBarang').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('barangstok.data') }}",
                    data: function(d) {
                        d.barang_id = selectedBarangId;
                    }
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel fa-lg"></i>',
                        className: 'btn btn-success btn-sm',
                        titleAttr: 'Export ke Excel'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf fa-lg"></i>',
                        className: 'btn btn-danger btn-sm',
                        titleAttr: 'Export ke PDF'
                    }
                ],
                columns: columns
            });

            $(document).on('click', '.pilih-barang', function() {
                selectedBarangId = $(this).data('id');
                selectedBarangNm = $(this).find('h5').text();
                $('#titleBarang').text('- ' + selectedBarangNm);
                table.ajax.reload();
            });

            // ===== Modal Tambah (lama) =====
            $('#btnTambah').on('click', function() {
                $('#formBarang')[0].reset();
                $('#id').val('');
                $('#barang_id').val(selectedBarangId || '');
                $('#modalBarang .modal-title').text('Tambah Stok Barang' + (selectedBarangNm ? (' - ' +
                    selectedBarangNm) : ''));
                $('#modalBarang').modal('show');
            });

            $('#modalBarang').on('change', '#rak_id', function() {
                loadShaftsForRak($('#modalBarang #rak_id'), $('#modalBarang #rak_shaft_id'), $(this).val(),
                    null);
            });

            // ===== CATAT STOK (modal baru) =====
            $('body').on('click', '.btn-catat', function() {
                const barangId = $(this).data('barang-id');
                const barangNm = $(this).data('barang-nm') || '';

                $('#formCatat')[0].reset();
                $('#barang_id').val(barangId);
                $('#catat_tgl').val(new Date().toISOString().slice(0, 10));
                $('#catat_nama_barang').text(' - ' + barangNm);
                $('#catat_nama_barang_input').val(barangNm);
                $('#catat_rak_shaft_id').html('<option value="">-- Pilih Shaft --</option>');

                $.get("{{ route('barangstok.showBarang', ['id' => '___ID___']) }}".replace('___ID___',
                        barangId))
                    .done(function(res) {
                        // ambil rak/shaft dari mst_barang; fallback ke relasi
                        const rakId = (res.rak_id ?? (res.rak && res.rak.id)) || '';
                        const shaftId = (res.rak_shaft_id ?? (res.rak_shaft && res.rak_shaft.id)) || '';

                        $('#catat_rak_id').val(rakId); // set rak
                        loadShaftsForRak($('#catat_rak_id'), $('#catat_rak_shaft_id'), rakId,
                            shaftId); // isi & pilih shaft

                        $('#modalCatat').modal('show');
                        setTimeout(() => $('#stok').trigger('focus'), 150);
                    })
                    .fail(function(xhr) {
                        console.error('Gagal load master barang:', xhr.status, xhr.responseText);
                        alert('Gagal memuat detail barang.');
                    });
            });

            // jika rak diubah manual oleh user pada modal Catat
            $('#modalCatat').on('change', '#catat_rak_id', function() {
                loadShaftsForRak($('#catat_rak_id'), $('#catat_rak_shaft_id'), $(this).val(), null);
            });

            $('#formCatat').on('submit', function(e) {
                e.preventDefault();
                $.post("/stock-barang/store", $(this).serialize())
                    .done(function(res) {
                        if (res.success) {
                            $('#modalCatat').modal('hide');
                            $('#tableBarang').DataTable().ajax.reload();
                            swalSuccess(res.message); // << pesan dari controller
                        } else {
                            swalError(res.message || 'Gagal menyimpan catatan stok');
                        }
                    })
                    .fail(function(xhr) {
                        swalFromXhr(xhr,
                            'Gagal menyimpan catatan stok');
                    });
            });



            // ===== Delete row =====
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                if (!confirm('Yakin ingin menghapus data ini?')) return;
                $.ajax({
                    url: "/stock-barang/delete/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                }).done(function(res) {
                    table.ajax.reload();
                    alert(res.message);
                });
            });

            // ===== Submit form Tambah/Edit (modal lama) =====
            $('#formBarang').on('submit', function(e) {
                e.preventDefault();
                $.post("/stock-barang/store", $(this).serialize())
                    .done(function(res) {
                        if (res.success) {
                            $('#modalBarang').modal('hide');
                            table.ajax.reload();
                            swalSuccess(res.message);
                        } else {
                            swalError(res.message || 'Gagal menyimpan data');
                        }
                    })
                    .fail(function(xhr) {
                        swalFromXhr(xhr, 'Terjadi kesalahan. Silakan cek inputan.');
                    });
            });



            // ===== Modal Tambah Rak + Shafts =====
            const $modalRak = $('#modalRak');
            const $shaftList = $('#shaftList');

            function shaftRow(value = '') {
                return (
                    '<div class="input-group mb-2 shaft-row">' +
                    '<input type="text" name="shafts[]" class="form-control" placeholder="Nama Shaft" value="' +
                    (value || '') + '" required>' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-danger btn-hapus-shaft" type="button">Hapus</button>' +
                    '</div>' +
                    '</div>'
                );
            }

            $('#btnTambahRak').on('click', function() {
                $('#formRak')[0].reset();
                $shaftList.empty().append(shaftRow());
                $modalRak.modal('show');
            });

            $('#btnAddShaft').on('click', function() {
                $shaftList.append(shaftRow());
            });

            $shaftList.on('click', '.btn-hapus-shaft', function() {
                const total = $shaftList.find('.shaft-row').length;
                if (total > 1) $(this).closest('.shaft-row').remove();
                else $(this).closest('.shaft-row').find('input').val('').focus();
            });

            $('#formRak').on('submit', function(e) {
                e.preventDefault();
                $.post("{{ route('rak.store') }}", $(this).serialize())
                    .done(function(res) {
                        if (res.success) {
                            $modalRak.modal('hide');
                            alert(res.message || 'Rak berhasil disimpan');
                        } else {
                            alert(res.message || 'Gagal menyimpan rak');
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Rak store error:', xhr.status, xhr.responseText);
                        alert('Terjadi kesalahan. Periksa input.');
                    });
            });

            // Hapus master barang + seluruh histori
            $('body').on('click', '.btn-del-barang', function() {
                const barangId = $(this).data('barang-id');
                const nama = $(this).data('barang-nm') || 'barang';

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus barang?',
                    html: `Anda akan menghapus <b>${nama}</b>.<br><span class="text-danger">Semua histori stok terkait juga akan dihapus.</span>`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((res) => {
                    if (!res.isConfirmed) return;

                    $.ajax({
                            url: "{{ route('mst.barang.hapus', ':id') }}".replace(':id',
                                barangId),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            }
                        })
                        .done(function(resp) {
                            $('#tableBarang').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: resp.message || 'Berhasil menghapus.'
                            });
                        })
                        .fail(function(xhr) {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                .responseJSON.message : 'Gagal menghapus barang.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: msg
                            });
                        });
                });
            });
            $(function() {
                // buka modal & load data
                $('#btnListRakBarang').on('click', function() {
                    const $tbody = $('#tblRakBarang tbody');
                    $tbody.html('<tr><td colspan="4" class="text-center">Memuat…</td></tr>');
                    $('#modalRakBarang').modal('show');

                    $.get("{{ route('rak.list') }}")
                        .done(function(raks) {
                            const rows = [];
                            let no = 1;

                            raks.forEach(function(rak) {
                                const shafts = rak.shafts || [];

                                if (shafts.length === 0) {
                                    // rak tanpa shaft
                                    rows.push(
                                        `<tr>
                                            <td class="text-center align-middle" rowspan="1">${no}</td>
                                            <td class="align-middle" rowspan="1">
                                            ${rak.nama_rak ?? '-'}
                                            <div class="mt-1">
                                                <button class="btn btn-xs btn-outline-primary btn-edit-rak" data-id="${rak.id}">
                                                <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                            </td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>`
                                    );
                                    no++;
                                    return;
                                }

                                shafts.forEach(function(shaft, idx) {
                                    const barangList = (shaft.barangs || [])
                                        .map(b => b.barang_nm).join(' | ') ||
                                        '-';

                                    if (idx === 0) {
                                        rows.push(
                                            `<tr>
                                            <td class="text-center align-middle" rowspan="${shafts.length}">${no}</td>
                                            <td class="align-middle" rowspan="${shafts.length}">
                                                ${rak.nama_rak ?? '-'}
                                                <div class="mt-1">
                                                <button class="btn btn-xs btn-outline-primary btn-edit-rak" data-id="${rak.id}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                </div>
                                            </td>
                                            <td>${shaft.nama_shaft ?? '-'}</td>
                                            <td>${barangList}</td>
                                            </tr>`
                                        );
                                    } else {
                                        rows.push(
                                            `<tr>
                                                <td>${shaft.nama_shaft ?? '-'}</td>
                                                <td>${barangList}</td>
                                            </tr>`
                                        );
                                    }
                                });

                                no++;
                            });


                            $('#tblRakBarang tbody').html(rows.join('') ||
                                '<tr><td colspan="4" class="text-center">Data kosong</td></tr>'
                            );
                        })
                        .fail(function() {
                            $('#tblRakBarang tbody').html(
                                '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>'
                            );
                        });
                });
            });

            function editShaftRow(id = '', value = '') {
                return `
                    <div class="input-group mb-2 edit-shaft-row">
                    <input type="hidden" class="shaft-id" value="${id || ''}">
                    <input type="text" class="form-control shaft-name" placeholder="Nama Shaft" value="${value || ''}" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger btn-edit-hapus-shaft" type="button">Hapus</button>
                    </div>
                    </div>
                `;
            }

            function renumberShaftInputs() {
                $('#editShaftList .edit-shaft-row').each(function(i) {
                    $(this).find('.shaft-id').attr('name', `shafts[${i}][id]`);
                    $(this).find('.shaft-name').attr('name', `shafts[${i}][nama_shaft]`);
                });
            }


            const $modalEditRak = $('#modalEditRak');
            const $editShaftList = $('#editShaftList');

            $('body').on('click', '.btn-edit-rak', function() {
                const id = $(this).data('id');
                $('#formEditRak')[0].reset();
                $('#edit_rak_id').val(id);
                $editShaftList.empty();

                $.get("{{ route('rak.show', ':id') }}".replace(':id', id))
                    .done(function(res) {
                        // isi form
                        $('#edit_nama_rak').val(res.nama_rak);
                        if (Array.isArray(res.shafts) && res.shafts.length) {
                            res.shafts.forEach(s => $editShaftList.append(editShaftRow(s.id, s
                                .nama_shaft)));
                        } else {
                            $editShaftList.append(editShaftRow());
                        }

                        // setelah data siap, atur transisi modal
                        if ($('#modalRakBarang').hasClass('show')) {
                            $('#modalRakBarang')
                                .one('hidden.bs.modal', function() {
                                    $('#modalEditRak').modal({
                                        show: true,
                                        backdrop: 'static'
                                    });
                                })
                                .modal('hide');
                        } else {
                            $('#modalEditRak').modal({
                                show: true,
                                backdrop: 'static'
                            });
                        }
                    })
                    .fail(() => swalError('Gagal memuat detail rak'));
            });

            $('#modalEditRak').on('hidden.bs.modal', function() {
                $('#modalRakBarang').modal('show');
            });

            $('#btnEditAddShaft').on('click', function() {
                $editShaftList.append(editShaftRow());
            });

            // hapus baris shaft di UI (boleh tinggal 1, kosongkan saja)
            $editShaftList.on('click', '.btn-edit-hapus-shaft', function() {
                const total = $editShaftList.find('.edit-shaft-row').length;
                if (total > 1) $(this).closest('.edit-shaft-row').remove();
                else $(this).closest('.edit-shaft-row').find('input[type="text"]').val('').focus();
            });
            $('#formEditRak').on('submit', function(e) {
                e.preventDefault();
                renumberShaftInputs(); // <<< penting

                const id = $('#edit_rak_id').val();
                const payload = $(this).serialize();

                $.ajax({
                        url: "{{ route('rak.update', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: payload
                    })
                    .done(function(resp) {
                        $('#modalEditRak').modal('hide');
                        swalSuccess(resp.message || 'Rak & shafts diperbarui');
                        $('#btnListRakBarang').trigger('click');
                    })
                    .fail(function(xhr) {
                        swalFromXhr(xhr, 'Gagal menyimpan perubahan rak');
                    });
            });



        }); // end document ready
    </script>
@endpush
