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
                            @foreach ($mstBarang as $b)
                                <option value="{{ $b->id }}"
                                    {{ isset($barang) && $barang->id == $b->id ? 'selected' : '' }}>
                                    {{ $b->barang_nm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stok">Jumlah Stock</label>
                        <input type="number" name="stok" id="stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="exp_tgl">Tanggal Expired</label>
                        <input type="date" name="exp_tgl" id="exp_tgl" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="catat_tgl">Tanggal Pencatatan</label>

                        @if (auth()->user()->pegawai->role_id == 1)
                            {{-- Role 1: default hari ini tapi bisa diubah --}}
                            <input type="date" name="catat_tgl" id="catat_tgl" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        @else
                            {{-- Selain role 1: default hari ini dan readonly --}}
                            <input type="date" name="catat_tgl" id="catat_tgl" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" readonly required>
                        @endif
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
