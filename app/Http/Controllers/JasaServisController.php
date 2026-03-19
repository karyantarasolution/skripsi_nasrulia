<?php

namespace App\Http\Controllers;

use App\Models\JasaServis;
use Illuminate\Http\Request;

class JasaServisController extends Controller
{
    public function index()
    {
        $jasa = JasaServis::latest()->get();
        return view('jasa.index', compact('jasa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'biaya_jasa' => 'required|numeric|min:0'
        ]);

        JasaServis::create($request->all());

        return redirect()->route('jasa.index')->with('success', 'Jasa Servis baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'biaya_jasa' => 'required|numeric|min:0'
        ]);

        $jasa = JasaServis::findOrFail($id);
        $jasa->update($request->all());

        return redirect()->route('jasa.index')->with('success', 'Data Jasa Servis berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jasa = JasaServis::findOrFail($id);
        $jasa->delete();

        return redirect()->route('jasa.index')->with('success', 'Jasa Servis berhasil dihapus!');
    }
}