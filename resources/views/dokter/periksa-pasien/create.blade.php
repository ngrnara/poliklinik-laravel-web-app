<x-layouts.app title="Periksa Pasien">
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="mb-4">Periksa Pasien</h1>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('dokter.periksa-pasien.store') }}" method="POST" id="form-periksa">
                            @csrf
                            <input type="hidden" name="id_daftar_poli" value="{{ $id }}">
                            <input type="hidden" name="obat_json" id="obat_json" value="[]">
                            <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">

                            {{-- Pilih Obat --}}
                            <div class="mb-3">
                                <label for="select-obat" class="form-label">Pilih Obat</label>
                                <div class="d-flex gap-2">
                                    <select id="select-obat" class="form-select">
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach($obats as $obat)
                                            <option value="{{ $obat->id }}"
                                                data-nama="{{ $obat->nama_obat }}"
                                                data-harga="{{ $obat->harga }}"
                                                {{ $obat->stok <= 0 ? 'disabled' : '' }}>
                                                
                                                {{ $obat->nama_obat }}
                                                — Rp {{ number_format($obat->harga,0,',','.') }}
                                                {{ $obat->stok <= 0 ? '(Stok Habis)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="button" id="btn-add-obat" class="btn btn-primary">Tambah</button>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="4">{{ old('catatan') }}</textarea>
                            </div>

                            {{-- Obat Terpilih --}}
                            <div class="mb-3">
                                <label class="form-label">Obat Terpilih</label>

                                <div id="list-obat">
                                    {{-- akan diisi oleh JS --}}
                                </div>
                            </div>

                            {{-- Total Harga --}}
                            <div class="mb-3">
                                <label class="form-label">Total Harga</label>
                                <div class="h5" id="total-harga">Rp 0</div>
                                <small class="text-muted">Catatan: Jasa Dokter Rp 50.000 akan ditambahkan di sistem.</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ route('dokter.periksa-pasien.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('select-obat');
    const btnAdd = document.getElementById('btn-add-obat');
    const listEl = document.getElementById('list-obat');
    const totalEl = document.getElementById('total-harga');
    const inputObatJson = document.getElementById('obat_json');
    const inputBiayaPeriksa = document.getElementById('biaya_periksa');

    const BIAYA_JASA_DOKTER = 50000;

    let daftar = [];

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function renderList() {
        listEl.innerHTML = '';

        if (daftar.length === 0) {
            listEl.innerHTML = '<div class="card"><div class="card-body text-muted">Belum ada obat terpilih.</div></div>';
        } else {
            daftar.forEach((it, idx) => {
                const card = document.createElement('div');
                card.className = 'card mb-2';
                card.innerHTML = `
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${it.nama}</strong><br>
                            <small class="text-muted">${formatRp(it.harga)}</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-danger btn-hapus" data-idx="${idx}">Hapus</button>
                        </div>
                    </div>
                `;
                listEl.appendChild(card);
            });
        }

        // update input hidden
        inputObatJson.value = JSON.stringify(daftar.map(d => d.id));
        updateTotal();
    }

    function updateTotal() {
        const sumObat = daftar.reduce((s, o) => s + Number(o.harga || 0), 0);
        // kita simpan subtotal obat di biaya_periksa, backend menambahkan jasa dokter
        inputBiayaPeriksa.value = sumObat;
        totalEl.textContent = `${formatRp(sumObat)}  + Jasa Dokter ${formatRp(BIAYA_JASA_DOKTER)} (ditambahkan di sistem)`;
    }

    btnAdd.addEventListener('click', function () {
        const opt = select.options[select.selectedIndex];
        const id = opt.value;
        const nama = opt.dataset.nama;
        const harga = Number(opt.dataset.harga || 0);

        if (!id) return;

        // jika sudah ada, abaikan
        if (daftar.some(x => String(x.id) === String(id))) return;

        daftar.push({ id, nama, harga });
        renderList();
        // reset select
        select.selectedIndex = 0;
    });

    // delegasi hapus
    listEl.addEventListener('click', function (e) {
        if (e.target.matches('.btn-hapus')) {
            const idx = Number(e.target.dataset.idx);
            daftar.splice(idx, 1);
            renderList();
        }
    });

    // initial render
    renderList();

    // sebelum submit set obat_json lengkap (sudah di update di renderList)
    document.getElementById('form-periksa').addEventListener('submit', function (ev) {
        inputObatJson.value = JSON.stringify(daftar.map(d => d.id));
    });
});
</script>
@endpush
</x-layouts.app>
