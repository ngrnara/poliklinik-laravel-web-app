<?php

namespace App\Http\Controllers;

// Semua 'use' statement harus di sini, SETELAH namespace
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * Sesuai Modul 10.2.1
     */
    public function showLogin()
    {
        return view('auth.login'); 
    }

    /**
     * Memproses login.
     * Sesuai Modul 10.2.2
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password'); 

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); 

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard'); 
            } elseif ($user->role == 'dokter') {
                return redirect()->route('dokter.dashboard'); 
            } else {
                return redirect()->route('pasien.dashboard'); 
            }
        }
        
        // Jika login gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors(['email' => 'Email atau Password Salah!']); 
    }

    /**
     * Menampilkan halaman form register.
     * Sesuai Modul 10.3.2 (tertulis 10.3.2 di PDF, harusnya 10.2.3)
     */
    public function showRegister()
    {
        return view('auth.register'); 
    }

    /**
     * Memproses registrasi pasien baru.
     * Sesuai Modul 10.2.4 dan instruksi generate no_rm
     */
    public function register(Request $request)
    {
        // 1. Validasi input 
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_ktp' => 'required|string|max:30|unique:users,no_ktp', // Mengikuti validasi Modul 10 
            'no_hp' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // 2. Logika Generate NO_RM (Nomor Rekam Medis) 
        $tanggal_sekarang = now()->format('Ym');
        
        // Cek pasien terakhir di bulan ini
        $pasien_terakhir = User::where('role', 'pasien')
                                ->where('no_rm', 'like', $tanggal_sekarang . '%')
                                ->orderBy('no_rm', 'desc')
                                ->first();

        if ($pasien_terakhir) {
            // Ambil nomor urut dari no_rm terakhir (3 digit terakhir)
            $nomor_urut = (int)substr($pasien_terakhir->no_rm, -3);
            $nomor_urut++; // Tambah 1
        } else {
            // Jika tidak ada pasien di bulan ini, mulai dari 1
            $nomor_urut = 1;
        }

        // Format nomor urut menjadi 3 digit (misal: 001, 012)
        $nomor_rm_baru = $tanggal_sekarang . '-' . str_pad($nomor_urut, 3, '0', STR_PAD_LEFT); 

        // 3. Buat user baru 
        User::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_ktp' => $request->no_ktp,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'no_rm' => $nomor_rm_baru, // Masukkan no_rm yang baru
        ]);

        // 4. Redirect ke login 
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Memproses logout.
     * Sesuai Modul 10.2.5
     */
    public function logout(Request $request) // Tambahkan Request $request
    {
        Auth::logout(); 
        
        // Baris ini penting untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); 
    }
}