<x-layouts.app>
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="mb-4">Edit Obat</h1>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('obat.update', $obat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Row 1: Nama & Kemasan -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            Nama Obat <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            name="nama_obat"
                                            class="form-control @error('nama_obat') is-invalid @enderror"
                                            value="{{ old('nama_obat', $obat->nama_obat) }}"
                                            required>
                                        @error('nama_obat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            Kemasan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            name="kemasan"
                                            class="form-control @error('kemasan') is-invalid @enderror"
                                            value="{{ old('kemasan', $obat->kemasan) }}"
                                            required>
                                        @error('kemasan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Harga & Stok -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            Harga <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            name="harga"
                                            class="form-control @error('harga') is-invalid @enderror"
                                            value="{{ old('harga', $obat->harga) }}"
                                            required>
                                        @error('harga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            Stok <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            name="stok"
                                            class="form-control @error('stok') is-invalid @enderror"
                                            value="{{ old('stok', $obat->stok) }}"
                                            min="0"
                                            required>
                                        @error('stok')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('obat.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
