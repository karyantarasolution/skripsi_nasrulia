<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ServisDetail;
use App\Models\JasaServis;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index()
    {
        $transaksi = Transaksi::with('detail.produk')->latest()->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function konfirmasi($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => 'Lunas']);

        // Notif WhatsApp ke pelanggan (jika terdaftar sebagai user)
        try {
            $user = $transaksi->user ?? \App\Models\User::find($transaksi->user_id);
            if ($user && !empty($user->no_whatsapp)) {
                $this->wa->sendTransaksiNotif(
                    $user->no_whatsapp,
                    $transaksi->nama_pelanggan,
                    $transaksi->kode_transaksi,
                    $transaksi->total_bayar,
                    'Lunas'
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        // Jika transaksi tipe servis, update status servis jadi selesai
        if ($transaksi->tipe == 'servis') {
            ServisDetail::where('transaksi_id', $transaksi->id)->update(['status' => 'selesai']);
        }

        return redirect()->back()->with('success', 'Pesanan ' . $transaksi->kode_transaksi . ' berhasil dikonfirmasi Lunas!');
    }

    public function pesananSaya()
    {
        $pesanan = Transaksi::with('detail.produk', 'servisDetail.jasaServis')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pelanggan.pesanan', compact('pesanan'));
    }

    public function invoice($id)
    {
        $transaksi = Transaksi::with('detail.produk', 'servisDetail.jasaServis', 'user')->findOrFail($id);
        $kasir = Auth::user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-invoice', compact('transaksi', 'kasir'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Invoice_' . $transaksi->kode_transaksi . '.pdf');
    }
}
