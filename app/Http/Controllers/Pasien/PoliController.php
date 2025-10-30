<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    /**
     * Menampilkan halaman form daftar poli
     */
    public function get()
    {
        // Ambil data user yang login, semua poli, dan semua jadwal
        $user = Auth::user();
        $polis = Poli::all();
        // Eager load relasi dokter dan poli dokter
        $jadwals = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Memproses pendaftaran poli
     */
    public function submit(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
        ]);

        // 2. Hitung nomor antrian
        // Ambil jumlah pendaftar di jadwal yang sama
        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();
        $nomor_antrian = $jumlahSudahDaftar + 1;

        // 3. Simpan pendaftaran
        DaftarPoli::create([
            // PERBAIKAN: Ambil ID Pasien dari Auth, bukan request
            'id_pasien' => Auth::id(), 
            
            // PERBAIKAN: Ambil id_jadwal dari request (PDF salah ketik 'id_jadwals')
            'id_jadwal' => $request->id_jadwal,
            
            'keluhan' => $request->keluhan,
            'no_antrian' => $nomor_antrian,
        ]);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('message', 'Berhasil Mendaftar ke Poli. Nomor antrian Anda: ' . $nomor_antrian)->with('type', 'success');
    }
}