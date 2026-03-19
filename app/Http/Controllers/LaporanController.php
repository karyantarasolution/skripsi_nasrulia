<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\JasaServis;
use App\Models\AturanChatbot;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function cetakPDF($tipe)
    {
        $data = [];
        $judul = '';
        $view = 'laporan.pdf';

        // Set Waktu WITA (Banjarmasin)
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        switch ($tipe) {
            case 'produk-semua':
                $data = Produk::with('kategori')->get();
                $judul = 'Laporan Seluruh Data Produk';
                break;
            case 'produk-menipis':
                $data = Produk::with('kategori')->where('stok', '<', 5)->get();
                $judul = 'Laporan Stok Produk Menipis (< 5)';
                break;
            case 'jasa':
                $data = JasaServis::all();
                $judul = 'Laporan Data Layanan Jasa Servis';
                break;
            case 'chatbot':
                $data = AturanChatbot::all();
                $judul = 'Laporan Knowledge Base AI (Aturan Chatbot)';
                break;
            case 'transaksi-semua':
                $data = Transaksi::latest()->get();
                $judul = 'Laporan Seluruh Riwayat Transaksi';
                break;
            case 'transaksi-lunas':
                $data = Transaksi::where('status', 'Lunas')->latest()->get();
                $judul = 'Laporan Transaksi Berhasil (Lunas)';
                break;
            case 'transaksi-pending':
                $data = Transaksi::where('status', '!=', 'Lunas')->latest()->get();
                $judul = 'Laporan Transaksi Pending (Belum Lunas)';
                break;
            case 'pendapatan':
                $data = Transaksi::where('status', 'Lunas')->latest()->get();
                $judul = 'Laporan Rekap Pendapatan Keseluruhan';
                break;
            default:
                abort(404);
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, compact('data', 'judul', 'tipe', 'waktu_cetak'));
        
        // Ubah ukuran kertas ke A4 (Landscape/Portrait sesuai kebutuhan)
        $pdf->setPaper('A4', 'portrait');

        // Langsung tampilkan di browser (Stream) atau download langsung (Download)
        return $pdf->stream('Laporan_'.$tipe.'_'.time().'.pdf');
    }
}