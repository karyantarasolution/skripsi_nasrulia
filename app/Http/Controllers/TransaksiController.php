<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    // --- KHUSUS KASIR & ADMIN ---
    public function index()
    {
        // Ambil semua transaksi beserta detail produknya, urutkan dari yang terbaru
        $transaksi = Transaksi::with('detail.produk')->latest()->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function konfirmasi($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => 'Lunas']); // Ubah status jadi Lunas

        return redirect()->back()->with('success', 'Pesanan ' . $transaksi->kode_transaksi . ' berhasil dikonfirmasi Lunas!');
    }

    // --- KHUSUS PELANGGAN ---
    public function pesananSaya()
    {
        // Ambil transaksi khusus milik user yang sedang login
        $pesanan = Transaksi::with('detail.produk')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();
                    
        return view('pelanggan.pesanan', compact('pesanan'));
    }
}