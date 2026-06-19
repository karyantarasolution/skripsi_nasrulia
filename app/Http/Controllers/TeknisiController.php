<?php

namespace App\Http\Controllers;

use App\Models\ServisDetail;
use App\Models\Transaksi;
use App\Models\JasaServis;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeknisiController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index()
    {
        $servis = ServisDetail::with('transaksi.user', 'jasaServis')
            ->where('teknisi_id', Auth::id())
            ->orWhereNull('teknisi_id')
            ->latest()
            ->get();

        return view('teknisi.index', compact('servis'));
    }

    public function ambilServis($id)
    {
        $servis = ServisDetail::findOrFail($id);
        $servis->update(['teknisi_id' => Auth::id()]);

        return redirect()->back()->with('success', 'Servis berhasil diambil!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:proses,selesai,diambil,garansi,batal',
            'catatan_teknisi' => 'nullable|string',
        ]);

        $servis = ServisDetail::findOrFail($id);
        $data = [
            'status' => $request->status,
            'catatan_teknisi' => $request->catatan_teknisi,
        ];

        if ($request->status == 'selesai') {
            $data['tanggal_selesai'] = now();
        }

        $servis->update($data);

        try {
            $user = $servis->transaksi->user ?? null;
            if ($user && !empty($user->no_whatsapp)) {
                $statusLabel = [
                    'proses' => 'Sedang Diproses',
                    'selesai' => 'Selesai',
                    'diambil' => 'Sudah Diambil',
                    'garansi' => 'Dalam Garansi',
                    'batal' => 'Dibatalkan',
                ];
                $this->wa->sendTransaksiNotif(
                    $user->no_whatsapp,
                    $user->name,
                    $servis->transaksi->kode_transaksi,
                    $servis->transaksi->total_bayar,
                    $statusLabel[$request->status] ?? $request->status
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        return redirect()->back()->with('success', 'Status servis berhasil diperbarui!');
    }

    public function daftarServis()
    {
        $semuaServis = ServisDetail::with('transaksi.user', 'jasaServis', 'teknisi')
            ->latest()
            ->get();
        return view('teknisi.semua-servis', compact('semuaServis'));
    }
}
