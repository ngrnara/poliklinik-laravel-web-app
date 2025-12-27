<x-layouts.app title="Riwayat Pasien">
    <div class="container-fluid px-4 mt-4">
        <h1 class="mb-4">Riwayat Pasien</h1>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No Antrian</th>
                                <th>Nama Pasien</th>
                                <th>Keluhan</th>
                                <th>Tanggal Periksa</th>
                                <th>Biaya</th>
                                <th style="width:130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatPasien as $p)
                                <tr>
                                    <td>{{ $p->daftarPoli->no_antrian }}</td>
                                    <td>{{ $p->daftarPoli->pasien->nama }}</td>
                                    <td>{{ $p->daftarPoli->keluhan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tgl_periksa)->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($p->biaya_periksa, 0, ',', '.') }}</td>

                                    <td>
                                        <a href="{{ route('dokter.periksa-pasien.show', $p->id_daftar_poli) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada riwayat pemeriksaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
