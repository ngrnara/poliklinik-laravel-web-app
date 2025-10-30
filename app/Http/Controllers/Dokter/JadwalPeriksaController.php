<?php

namespace App\Http\Controllers\Dokter; 

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class JadwalPeriksaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. ambil user dari auth
        $dokter = Auth::user(); // Menggunakan Auth::user()
        
        // 2. ambil jadwal periksa berdasarkan id_dokter
        $jadwalPeriksas = JadwalPeriksa::where('id_dokter', $dokter->id)
            ->orderBy('hari')
            ->get();
            
        // 3. return view
        return view('dokter.jadwal-periksa.index', compact('jadwalPeriksas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // PERBAIKAN: PDF menggunakan underscore. Ganti ke dash.
        return view('dokter.jadwal-periksa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai', // Validasi jam selesai > jam mulai
        ]);

        // Simpan jadwal periksa
        JadwalPeriksa::create([
            'id_dokter' => Auth::id(), // Ambil ID dokter yang login
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // PERBAIKAN: PDF salah route. Ganti ke 'dokter.jadwal-periksa.index'
        return redirect()->route('dokter.jadwal-periksa.index')->with('message', 'Jadwal periksa berhasil ditambahkan.')->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tidak digunakan di modul ini
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);
        
        // Tambahan Keamanan: Pastikan dokter hanya bisa edit jadwalnya sendiri
        if ($jadwalPeriksa->id_dokter != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE HALAMAN INI.');
        }

        return view('dokter.jadwal-periksa.edit', compact('jadwalPeriksa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);
        
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);

        // Tambahan Keamanan
        if ($jadwalPeriksa->id_dokter != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES UNTUK MENGUBAH JADWAL INI.');
        }

        $jadwalPeriksa->update([
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // PERBAIKAN: PDF salah route. Ganti ke 'dokter.jadwal-periksa.index'
        return redirect()->route('dokter.jadwal-periksa.index')
            ->with('message', 'Jadwal periksa berhasil diperbarui.')
            ->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);

        // Tambahan Keamanan
        if ($jadwalPeriksa->id_dokter != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES UNTUK MENGHAPUS JADWAL INI.');
        }
        
        $jadwalPeriksa->delete();

        // PERBAIKAN: PDF salah route. Ganti ke 'dokter.jadwal-periksa.index'
        return redirect()->route('dokter.jadwal-periksa.index')
            ->with('message', 'Jadwal periksa berhasil dihapus.')
            ->with('type', 'success');
    }
}