<div class="modal fade" id="modalBarang" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formBarang">
            @csrf
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="barang_id" id="barang_id"> {{-- akan terisi kalau edit --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Stok Barang</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    {{-- Nama Barang (text, bukan select) --}}
                    <div class="form-group">
                        <label for="barang_nm">Nama Barang</label>
                        <input type="text" name="barang_nm" id="barang_nm" class="form-control"
                            placeholder="Tulis nama barang..." required>

                    </div>
                    <div class="form-group">
                        <label for="barang_nm">Rak</label>
                        <select name="rak_id" id="rak_id" class="form-control"
                            data-shafts-url-template="{{ route('rak.shafts', ['rak' => '__RAK_ID__']) }}">
                            <option value="">-- Pilih Rak --</option>
                            @foreach ($raks as $rak)
                                <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                            @endforeach
                        </select>
                        <input type="radio" id="arah_masuk" name="arah" value="masuk" checked hidden>

                    </div>
                    <div class="form-group">
                        <label for="barang_nm">Shaft</label>
                        <select name="rak_shaft_id" id="rak_shaft_id" class="form-control">
                            <option value="">-- Pilih Shaft --</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="d-block">Satuan</label>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="sat_tablet" name="satuan" class="custom-control-input"
                                value="tablet" checked>
                            <label class="custom-control-label" for="sat_tablet">Tablet</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="sat_strip" name="satuan" class="custom-control-input"
                                value="strip">
                            <label class="custom-control-label" for="sat_strip">Strip</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stok">Jumlah Stock</label>
                        <input type="number" name="stok" id="stok" class="form-control" min="0"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="exp_tgl">Tanggal Expired</label>
                        <input type="date" name="exp_tgl" id="exp_tgl" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="catat_tgl">Tanggal Pencatatan</label>
                        @if (auth()->user()->pegawai->role_id == 1)
                            <input type="date" name="catat_tgl" id="catat_tgl" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        @else
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
