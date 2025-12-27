<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        // validasi input (biaya_periksa dikirim dari front sebagai subtotal obat, 
        // namun controller akan menghitung ulang dari DB untuk keamanan)
        $request->validate([
            'obat_json' => 'required',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
        ]);

        // konstanta jasa dokter
        $JASA_DOKTER = 50000;

        // dekode obat_json dari form 
        $obatData = json_decode($request->obat_json, true);
        if (!is_array($obatData)) {
            $obatData = [];
        }

        // buat record Periksa dulu (sementara biaya_periksa diset 0)
        $periksa = Periksa::create([
            'id_daftar_poli' => $request->id_daftar_poli,
            'tgl_periksa' => now(),
            'catatan' => $request->catatan,
            'biaya_periksa' => 0,
        ]);

        // hitung total obat berdasarkan harga di DB
        $totalObat = 0;

        // normalisasi: obatData 
        $normalized = [];
        if (count($obatData) > 0) {
            $first = $obatData[0];
            if (is_array($first) && array_key_exists('id', $first)) {
                $normalized = $obatData;
            } else {
                foreach ($obatData as $idOnly) {
                    $normalized[] = ['id' => $idOnly, 'qty' => 1];
                }
            }
        }

        foreach ($normalized as $item) {
    $idObat = $item['id'] ?? null;
    if (!$idObat) continue;

    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;

    $obat = Obat::find($idObat);
    if (!$obat) continue;

    //  CEK STOK (pengaman)
    if ($obat->stok < $qty) {
        return back()->withErrors([
            'stok' => "Stok obat {$obat->nama_obat} tidak mencukupi"
        ]);
    }

    //  HITUNG BIAYA
    $subtotal = $obat->harga * $qty;
    $totalObat += $subtotal;

    //  KURANGI STOK
    $obat->decrement('stok', $qty);

    // SIMPAN DETAIL
    DetailPeriksa::create([
        'id_periksa' => $periksa->id,
        'id_obat' => $idObat,
    ]);
}


        // grand total = total obat (server) + jasa dokter
        $grandTotal = $totalObat + $JASA_DOKTER;

        // update periksa dengan biaya yang benar
        $periksa->update([
            'biaya_periksa' => $grandTotal,
        ]);

        return redirect()->route('dokter.periksa-pasien.index')->with('success', 'Data periksa berhasil disimpan.');
    }

    /**
     * Tampilkan daftar riwayat pemeriksaan untuk dokter yang login
     */
    public function riwayat()
    {
        $dokterId = Auth::id();

        $periksas = Periksa::with(['daftarPoli.pasien', 'detailPeriksas.obat'])
            ->whereHas('daftarPoli.jadwalPeriksa', function($q) use ($dokterId) {
                $q->where('id_dokter', $dokterId);
            })
            ->orderBy('tgl_periksa', 'desc')
            ->get();

        return view('dokter.periksa-pasien.riwayat', compact('periksas'));
    }

    /**
     * Tampilkan semua pemeriksaan (Periksa) untuk 1 daftar_poli (id = daftar_poli.id)
     */
    public function show($id)
    {
        $daftar = DaftarPoli::with('pasien','jadwalPeriksa')->findOrFail($id);

        $periksas = Periksa::with('detailPeriksas.obat')
            ->where('id_daftar_poli', $id)
            ->orderBy('tgl_periksa', 'desc')
            ->get();

        return view('dokter.periksa-pasien.show', compact('daftar', 'periksas'));
    }
}
