<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }
        $produk = $query->latest()->get();
        $kategori = Kategori::all();
        return view('pelanggan.katalog', compact('produk', 'kategori'));
    }

    public function tambahKeKeranjang($id)
    {
        $produk = Produk::findOrFail($id);
        $keranjang = session()->get('keranjang', []);

        if (isset($keranjang[$id])) {
            $keranjang[$id]['jumlah']++;
        } else {
            $keranjang[$id] = [
                "nama" => $produk->nama_produk,
                "jumlah" => 1,
                "harga" => $produk->harga_jual,
                "foto" => $produk->foto,
            ];
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', 'Produk masuk keranjang!');
    }

    public function tampilkanKeranjang()
    {
        return view('pelanggan.keranjang');
    }

    public function hapusItem($id)
    {
        $keranjang = session()->get('keranjang');
        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
            session()->put('keranjang', $keranjang);
        }
        return redirect()->back()->with('success', 'Item dihapus!');
    }

    public function checkout(Request $request)
    {
        $keranjang = session()->get('keranjang');
        if (!$keranjang) return redirect()->back();

        $total = 0;
        foreach ($keranjang as $details) {
            $total += $details['harga'] * $details['jumlah'];
        }

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-' . time(),
            'user_id' => Auth::id(),
            'nama_pelanggan' => Auth::user()->name,
            'tipe' => 'penjualan',
            'total_bayar' => $total,
            'status' => 'Pending',
        ]);

        foreach ($keranjang as $id => $details) {
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $id,
                'jumlah' => $details['jumlah'],
                'harga_satuan' => $details['harga'],
                'subtotal' => $details['harga'] * $details['jumlah'],
            ]);

            $p = Produk::find($id);
            $p->stok -= $details['jumlah'];
            $p->save();
        }

        // Notif WhatsApp ke admin
        try {
            $nomor_admin = config('whatsapp.nomor_admin');
            if (!empty($nomor_admin)) {
                $this->wa->sendTransaksiNotif(
                    $nomor_admin,
                    Auth::user()->name,
                    $transaksi->kode_transaksi,
                    $total,
                    'Pending - Menunggu Konfirmasi'
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        session()->forget('keranjang');
        return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dikirim! Silakan hubungi Admin untuk pembayaran.');
    }
}
