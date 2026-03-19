<?php

namespace App\Http\Controllers;

use App\Models\AturanChatbot;
use Illuminate\Http\Request;

class AturanChatbotController extends Controller
{
    public function index()
    {
        $aturan = AturanChatbot::latest()->get();
        return view('chatbot.aturan', compact('aturan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kata_kunci' => 'required|string|max:255',
            'jawaban'    => 'required|string'
        ]);

        AturanChatbot::create($request->all());

        return redirect()->route('aturan-chatbot.index')->with('success', 'Aturan Chatbot baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kata_kunci' => 'required|string|max:255',
            'jawaban'    => 'required|string'
        ]);

        $aturan = AturanChatbot::findOrFail($id);
        $aturan->update($request->all());

        return redirect()->route('aturan-chatbot.index')->with('success', 'Aturan Chatbot berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $aturan = AturanChatbot::findOrFail($id);
        $aturan->delete();

        return redirect()->route('aturan-chatbot.index')->with('success', 'Aturan Chatbot berhasil dihapus!');
    }
}