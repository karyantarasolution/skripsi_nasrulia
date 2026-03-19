<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AturanChatbot;
use App\Models\Produk;
use App\Models\JasaServis;

class ChatbotController extends Controller
{
    public function getResponse(Request $request)
    {
        $pesan_user = strtolower($request->pesan);
        
        $jawaban_bot = "Maaf, NJK Assistant belum mengerti maksud Anda. Silakan ketik kata kunci lain seperti 'laptop', 'lcd', atau 'install'. Anda juga bisa langsung ke menu Katalog kami.";
        
        // Kita gunakan Collection agar bisa menggabungkan hasil pencarian tanpa double
        $rekomendasi_produk = collect();
        $rekomendasi_jasa = collect();

        $aturan_ditemukan = false;
        $kata_kunci_cocok = '';

        // 1. Cari kecocokan di tabel aturan_chatbot
        $semua_aturan = AturanChatbot::all();
        foreach ($semua_aturan as $aturan) {
            if (str_contains($pesan_user, strtolower($aturan->kata_kunci))) {
                $jawaban_bot = $aturan->jawaban;
                $kata_kunci_cocok = strtolower($aturan->kata_kunci);
                $aturan_ditemukan = true;
                break;
            }
        }

        // 2. Tentukan kata apa yang mau dicari ke database Produk/Jasa
        $kata_pencarian = $aturan_ditemukan ? [$kata_kunci_cocok] : explode(' ', $pesan_user);

        // 3. FITUR SAKTI UPGRADED (Cari ke Nama, Deskripsi, dan Kategori)
        foreach ($kata_pencarian as $kata) {
            if (strlen($kata) > 2) {
                
                // Cari Produk (Cari di Nama, ATAU Deskripsi, ATAU Nama Kategori)
                $cari_produk = Produk::with('kategori')
                    ->where('nama_produk', 'LIKE', '%' . $kata . '%')
                    ->orWhere('deskripsi', 'LIKE', '%' . $kata . '%')
                    ->orWhereHas('kategori', function($q) use ($kata) {
                        $q->where('nama_kategori', 'LIKE', '%' . $kata . '%');
                    })
                    ->get();
                
                // Gabungkan hasil pencarian produk
                $rekomendasi_produk = $rekomendasi_produk->merge($cari_produk);

                // Cari Jasa Servis
                $cari_jasa = JasaServis::where('nama_jasa', 'LIKE', '%' . $kata . '%')->get();
                
                // Gabungkan hasil pencarian jasa
                $rekomendasi_jasa = $rekomendasi_jasa->merge($cari_jasa);
            }
        }

        // Hilangkan data yang double (duplicate) jika ada
        $hasil_produk = $rekomendasi_produk->unique('id')->values();
        $hasil_jasa = $rekomendasi_jasa->unique('id')->values();

        if (!$aturan_ditemukan && ($hasil_produk->count() > 0 || $hasil_jasa->count() > 0)) {
            $jawaban_bot = "Berikut adalah beberapa produk atau layanan terkait yang berhasil saya temukan untuk Anda:";
        }

        return response()->json([
            'jawaban' => $jawaban_bot,
            'rekomendasi_produk' => $hasil_produk,
            'rekomendasi_jasa' => $hasil_jasa
        ]);
    }
}