<x-layouts.app title="Periksa Pasien">
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-12">
                @if(session('success') || session('message'))
                    <div class="alert alert-{{ session('type','success') }} alert-dismissible fade show" role="alert">
                        {{ session('success') ?? session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h1 class="mb-4">Periksa Pasien</h1>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:80px;">ID</th>
                                        <th>Pasien</th>
                                        <th>Keluhan</th>
                                        <th style="width:110px;">No Antrian</th>
                                        <th style="width:200px;">Jadwal</th>
                                        <th style="width:160px;">Status Pemeriksaan</th>
                                        <th style="width:150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($daftarPasien as $d)
                                        <tr>
                                            <td class="align-middle text-center">{{ $d->id }}</td>
                                            <td class="align-middle">{{ optional($d->pasien)->nama ?? '—' }}</td>
                                            <td class="align-middle">{{ $d->keluhan ?? '-' }}</td>
                                            <td class="align-middle text-center">{{ $d->no_antrian ?? '-' }}</td>

                                            <td class="align-middle">
                                                @if($d->jadwalPeriksa)
                                                    <div><strong>{{ $d->jadwalPeriksa->hari ?? '-' }}</strong></div>
                                                    <div class="text-muted small">
                                                        @if($d->jadwalPeriksa->jam_mulai || $d->jadwalPeriksa->jam_selesai)
                                                            {{ \Carbon\Carbon::parse($d->jadwalPeriksa->jam_mulai)->format('H:i') ?? '' }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($d->jadwalPeriksa->jam_selesai)->format('H:i') ?? '' }}
                                                        @endif
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="align-middle text-center">
                                                @if($d->periksas && $d->periksas->count() > 0)
                                                    <span class="badge bg-success">Sudah Diperiksa</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @endif
                                            </td>

                                            <td class="align-middle text-center">
                                                @if($d->periksas && $d->periksas->count() > 0)
                                                    <a href="{{ route('dokter.periksa-pasien.show', $d->id) ?? '#' }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                @else
                                                    <a href="{{ route('dokter.periksa-pasien.create', $d->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Periksa
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">Belum ada pasien untuk diperiksa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div> <!-- /.table-responsive -->
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div>
        </div>
    </div>
</x-layouts.app>
