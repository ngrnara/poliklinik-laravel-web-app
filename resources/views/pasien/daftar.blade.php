<x-layouts.app title="Poli">
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-12">
                 {{-- Alert flash message --}}
                @if (session('message'))
                    <div class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <h1 class="mb-4">Poli</h1>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Form Daftar Poli --}}
                            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3"> {{-- Dibuat lebih ke tengah --}}
                                <div class="card">
                                    <h5 class="card-header bg-gray">Daftar Poli</h5>
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <strong>Terjadi Kesalahan!</strong>
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                                            <div class="mb-3">
                                                <label for="no_rm" class="form-label">Nomor Rekam Medis</label>
                                                <input type="text" class="form-control" id="no_rm" name="no_rm"
                                                    value="{{ $user->no_rm }}" placeholder="Nomor Rekam Medis"
                                                    readonly> {{-- Ganti ke readonly agar data tetap terkirim --}}
                                            </div>

                                            <div class="mb-3">
                                                <label for="selectPoli" class="form-label">Pilih Poli</label>
                                                <select name="id_poli" id="selectPoli" class="form-control @error('id_poli') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Poli --</option>
                                                    @foreach ($polis as $poli)
                                                        <option value="{{ $poli->id }}">{{ $poli->nama_poli }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_poli')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="selectJadwal" class="form-label">Pilih Jadwal
                                                    Periksa</label>
                                                <select name="id_jadwal" id="selectJadwal" class="form-control @error('id_jadwal') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Jadwal --</option>
                                                    @foreach ($jadwals as $jadwal)
                                                        <option value="{{ $jadwal->id }}"
                                                            data-id-poli="{{ $jadwal->dokter->poli->id ?? '' }}">
                                                            {{ $jadwal->dokter->poli->nama_poli ?? '' }} |
                                                            {{ $jadwal->hari }}, {{ $jadwal->jam_mulai }} -
                                                            {{ $jadwal->jam_selesai }} |
                                                            {{ $jadwal->dokter->nama ?? '--' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_jadwal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="keluhan" class="form-label">Keluhan</label>
                                                <textarea name="keluhan" id="keluhan" rows="3" class="form-control @error('keluhan') is-invalid @enderror">{{ old('keluhan') }}</textarea>
                                                @error('keluhan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <button type="submit" name="submit"
                                                class="btn btn-primary w-100">Daftar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Script JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectPoli = document.getElementById('selectPoli');
            const selectJadwal = document.getElementById('selectJadwal');
            // Simpan semua opsi jadwal (termasuk yang disembunyikan)
            const allJadwalOptions = Array.from(selectJadwal.options);

            function filterJadwal() {
                const poliId = selectPoli.value;
                
                // Hapus semua opsi jadwal saat ini
                selectJadwal.innerHTML = '<option value="">-- Pilih Jadwal --</option>';

                // Filter dan tambahkan kembali opsi yang sesuai
                allJadwalOptions.forEach(option => {
                    if (option.value === "") return; // Selalu tampilkan "-- Pilih Jadwal --"
                    
                    // Tampilkan jika ID Poli-nya cocok ATAU jika tidak ada Poli yang dipilih
                    if (!poliId || option.dataset.idPoli == poliId) {
                        selectJadwal.appendChild(option.cloneNode(true));
                    }
                });
                selectJadwal.value = ""; // Reset pilihan jadwal
            }

            // Saat poli dipilih, filter jadwal
            selectPoli.addEventListener('change', filterJadwal);

            // Saat jadwal dipilih, isi poli otomatis jika belum
            selectJadwal.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const poliId = selected.dataset.idPoli;
                if (!selectPoli.value && poliId) {
                    selectPoli.value = poliId;
                    // Panggil filterJadwal lagi untuk memastikan hanya jadwal poli itu yang tampil
                    filterJadwal();
                    // Set value jadwal kembali setelah di-filter
                    selectJadwal.value = this.value; 
                }
            });

            // Jalankan filter saat halaman pertama kali dimuat (jika ada data lama)
            filterJadwal();
        });
    </script>
    @endpush

</x-layouts.app>