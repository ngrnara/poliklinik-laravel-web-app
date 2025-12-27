<x-layouts.app title="Detail Riwayat">
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Detail Riwayat</h2>
                <a href="{{ url()->previous() ?? route('dokter.periksa-pasien.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 offset-lg-1">

                {{-- Informasi Pasien (dari $daftar) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Informasi Pasien</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Pasien:</strong> {{ optional($daftar)->pasien->nama ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No. Antrian:</strong> {{ optional($daftar)->no_antrian ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Keluhan:</strong> {{ optional($daftar)->keluhan ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Poli:</strong>
                                {{ optional(optional($daftar)->jadwalPeriksa)->poli->nama_poli
                                   ?? optional(optional($daftar)->jadwalPeriksa)->hari ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Dokter:</strong> {{ optional(optional($daftar)->jadwalPeriksa)->dokter->nama ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Periksas: loop tiap periksa --}}
                @if(isset($periksas) && $periksas->count())
                    @foreach($periksas as $periksa)
                        {{-- Catatan Dokter untuk periksa ini --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Catatan Dokter</strong>
                                <span class="float-end">Tanggal: {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{!! nl2br(e($periksa->catatan ?? '-')) !!}</p>
                            </div>
                        </div>

                        {{-- Obat yang Diresepkan untuk periksa ini --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Obat yang Diresepkan</strong>
                            </div>
                            <div class="card-body">
                                @php
                                    $details = $periksa->detailPeriksas ?? collect();
                                @endphp

                                @if($details->count())
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:60px;">#</th>
                                                    <th>Nama Obat</th>
                                                    <th style="width:140px;" class="text-end">Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($details as $i => $d)
                                                    <tr>
                                                        <td class="text-center">{{ $i + 1 }}</td>
                                                        <td>{{ optional($d->obat)->nama_obat ?? ($d->nama_obat ?? '-') }}</td>
                                                        <td class="text-end">
                                                            {{ 'Rp ' . number_format((int) (optional($d->obat)->harga ?? 0), 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Tidak ada obat yang tercatat untuk pemeriksaan ini.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">Belum ada data pemeriksaan untuk pendaftaran ini.</div>
                @endif

                {{-- Footer action --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('dokter.riwayat-pasien.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                    </a>
                    <a href="{{ route('dokter.periksa-pasien.index') }}" class="btn btn-secondary">Daftar Antrian</a>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
