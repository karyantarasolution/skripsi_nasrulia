<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\JasaServis;
use App\Models\Ekspedisi;
use App\Models\AturanChatbot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    protected string $geminiApiKey;
    protected string $geminiModel;
    protected string $geminiBaseUrl;

    protected string $deepSeekApiKey;
    protected string $deepSeekModel;
    protected string $deepSeekBaseUrl;

    public function __construct()
    {
        $this->apiKey = (string) (config('services.groq.api_key', '') ?: (env('GROQ_API_KEY', '') ?: ''));
        $this->model = (string) config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->baseUrl = rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
        $this->timeout = (int) config('services.groq.timeout', 30);

        $this->geminiApiKey = (string) (config('services.gemini.api_key', '') ?: (env('GEMINI_API_KEY', '') ?: ''));
        $this->geminiModel = (string) config('services.gemini.model', 'gemini-3.6-flash');
        $this->geminiBaseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        $this->deepSeekApiKey = (string) (config('services.deepseek.api_key', '') ?: (env('DEEPSEEK_API_KEY', '') ?: ''));
        $this->deepSeekModel = (string) config('services.deepseek.model', 'deepseek-chat');
        $this->deepSeekBaseUrl = rtrim((string) config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
    }

    public function isConfigured(): bool
    {
        return !empty(trim($this->apiKey));
    }

    /**
     * Menghasilkan balasan AI. Prioritas: Groq -> Gemini -> DeepSeek -> fallback lokal DB.
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @return array ['jawaban' => string, 'rekomendasi_produk' => Collection, 'rekomendasi_jasa' => Collection]
     */
    public function chat(string $userMessage, array $chatHistory = []): array
    {
        $systemPrompt = $this->buildSystemPrompt();

        if ($this->isConfigured()) {
            $groqResult = $this->callGroqApi($userMessage, $chatHistory, $systemPrompt);
            if ($groqResult !== null) {
                return $groqResult;
            }
        }

        if (!empty(trim($this->geminiApiKey))) {
            $geminiResult = $this->callGeminiApi($userMessage, $chatHistory, $systemPrompt);
            if ($geminiResult !== null) {
                return $geminiResult;
            }
        }

        if (!empty(trim($this->deepSeekApiKey))) {
            $deepSeekResult = $this->callDeepSeekApi($userMessage, $chatHistory, $systemPrompt);
            if ($deepSeekResult !== null) {
                return $deepSeekResult;
            }
        }

        $notice = $this->isConfigured()
            ? "Maaf, server AI Groq sedang mengalami sedikit kendala."
            : "Layanan AI belum dihubungkan (GROQ_API_KEY belum dikonfigurasi).";

        return $this->handleFallback($userMessage, $notice);
    }

    /**
     * Memanggil Groq API (OpenAI-compatible: /chat/completions).
     */
    protected function callGroqApi(string $userMessage, array $chatHistory, string $systemPrompt): ?array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            $recentHistory = array_slice($chatHistory, -6);
            foreach ($recentHistory as $msg) {
                if (isset($msg['role'], $msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => (string) $msg['content'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.5,
                    'max_tokens' => 1500,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyText = $responseData['choices'][0]['message']['content'] ?? '';

                if (!empty(trim($replyText))) {
                    $recommended = $this->findRelevantRecommendations($userMessage, $replyText);

                    return [
                        'jawaban' => $replyText,
                        'rekomendasi_produk' => $recommended['produk'],
                        'rekomendasi_jasa' => $recommended['jasa'],
                    ];
                }
            }

            Log::warning('Groq API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Groq API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Memanggil Google Gemini API.
     */
    protected function callGeminiApi(string $userMessage, array $chatHistory, string $systemPrompt): ?array
    {
        try {
            $contents = [];
            foreach (array_slice($chatHistory, -6) as $msg) {
                if (isset($msg['role'], $msg['content'])) {
                    $contents[] = [
                        'role' => ($msg['role'] === 'user') ? 'user' : 'model',
                        'parts' => [['text' => (string) $msg['content']]]
                    ];
                }
            }
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            $endpoint = "{$this->geminiBaseUrl}/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";
            $payload = [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.5,
                    'maxOutputTokens' => 1500,
                ]
            ];

            $response = Http::timeout($this->timeout)->post($endpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

                if (!empty(trim($replyText))) {
                    $recommended = $this->findRelevantRecommendations($userMessage, $replyText);

                    return [
                        'jawaban' => $replyText,
                        'rekomendasi_produk' => $recommended['produk'],
                        'rekomendasi_jasa' => $recommended['jasa'],
                    ];
                }
            }

            Log::warning('Gemini API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Memanggil DeepSeek / OpenAI-compatible API.
     */
    protected function callDeepSeekApi(string $userMessage, array $chatHistory, string $systemPrompt): ?array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            foreach (array_slice($chatHistory, -6) as $msg) {
                if (isset($msg['role'], $msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => (string) $msg['content'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::withToken($this->deepSeekApiKey)
                ->timeout($this->timeout)
                ->post("{$this->deepSeekBaseUrl}/chat/completions", [
                    'model' => $this->deepSeekModel,
                    'messages' => $messages,
                    'temperature' => 0.5,
                    'max_tokens' => 1500,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyText = $responseData['choices'][0]['message']['content'] ?? '';

                if (!empty(trim($replyText))) {
                    $recommended = $this->findRelevantRecommendations($userMessage, $replyText);

                    return [
                        'jawaban' => $replyText,
                        'rekomendasi_produk' => $recommended['produk'],
                        'rekomendasi_jasa' => $recommended['jasa'],
                    ];
                }
            }

            Log::warning('DeepSeek API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('DeepSeek API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Membangun System Prompt dinamis dengan data database REAL (Produk, JasaServis, Ekspedisi,
     * Aturan Chatbot & info toko). Model AI "fine-tuned" via konteks data yang diambil dari database.
     */
    public function buildSystemPrompt(): string
    {
        $products = Produk::with('kategori:id,nama_kategori')
            ->select('id', 'kategori_id', 'merk', 'nama_produk', 'stok', 'harga_jual', 'deskripsi')
            ->get();

        $services = JasaServis::select('id', 'nama_jasa', 'biaya_jasa')->get();
        $ekspedisis = Ekspedisi::select('id', 'nama_ekspedisi')->get();

        $productListStr = "";
        foreach ($products as $p) {
            $kat = $p->kategori->nama_kategori ?? 'Umum';
            $stokStr = $p->stok > 0 ? "Ready Stock ({$p->stok} unit)" : "Stok Habis";
            $hargaStr = "Rp " . number_format($p->harga_jual, 0, ',', '.');
            $desc = $p->deskripsi ? " - Spec/Ket: " . str_replace(["\r", "\n"], ' ', substr($p->deskripsi, 0, 150)) : "";
            $productListStr .= "- [ID: {$p->id}] {$p->nama_produk} ({$kat}, Merk: {$p->merk}) | Harga: {$hargaStr} | Status: {$stokStr}{$desc}\n";
        }

        $serviceListStr = "";
        foreach ($services as $s) {
            $biayaStr = "Rp " . number_format($s->biaya_jasa, 0, ',', '.');
            $serviceListStr .= "- [ID: {$s->id}] {$s->nama_jasa} | Biaya: {$biayaStr}\n";
        }

        $ekspedisiListStr = "";
        foreach ($ekspedisis as $e) {
            $ekspedisiListStr .= "- {$e->nama_ekspedisi}\n";
        }

        $aturanListStr = "";
        $aturanRules = AturanChatbot::select('kata_kunci', 'jawaban')->get();
        foreach ($aturanRules as $ar) {
            $aturanListStr .= "- Kata kunci \"{$ar->kata_kunci}\" => {$ar->jawaban}\n";
        }

        $userContext = '';
        if (Auth::check()) {
            $userContext = "Pengguna saat ini adalah \"{$this->esc(Auth::user()->name)}\" dengan peran \"{$this->esc(Auth::user()->peran)}\".";
        }

        return <<<PROMPT
Kamu adalah "NJK Assistant", asisten AI resmi dari toko komputer "Nusantara Jaya Computer".

=== INFORMASI RESMI TOKO NUSANTARA JAYA COMPUTER ===
- Alamat & Google Maps: https://share.google/xrwq12yHe0uMzcoFv
- Nomor WhatsApp / Kontak Resmi Toko: 0851-8239-2525 dan 0851-8239-2526
- Jam Operasional Toko: Senin s/d Sabtu, Pukul 09.00 - 17.00 WITA (Hari Minggu Libur).

=== KONTEKS PENGGUNA ===
{$userContext}

=== ATURAN & BATASAN KETAT (GUARDRAILS) ===
1. RUANG LINGKUP PERTANYAAN (SCOPE):
   - Kamu HANYA boleh menjawab pertanyaan seputar barang/produk yang dijual di toko (laptop, PC, printer, sparepart, monitor, keyboard, mouse, aksesoris, dll.) dan layanan servis/perbaikan perangkat komputer, printer, atau jaringan.
   - Jika pengguna menanyakan hal di luar topik komputer/produk/layanan toko (seperti politik, topik umum, resep masakan, dll.), tolak dengan santun dan ramah, lalu arahkan kembali ke produk atau layanan Nusantara Jaya Computer.

2. PENANGANAN KERUSAKAN & BANTUAN TEKNISI REAL:
   - Apabila pelanggan mengalami kendala teknis rumit, kerusakan fisik (seperti mati total, korsleting, layar pecah, engsel hancur, kena air, butuh pengecekan komponen langsung), atau pelanggan meminta bantuan/konsultasi dengan teknisi sungguhan (real human technician), SELALU berikan nomor kontak WhatsApp Toko (0851-8239-2525 / 0851-8239-2526) atau sarankan membawa unit ke toko offline kami di jam operasional.

3. SIFAT READ-ONLY (DILARANG UBAH DATA):
   - Kamu adalah asisten informasi yang bersifat READ-ONLY. Kamu TIDAK BISA mengubah harga, stok, atau data lainnya. Data produk/jasa pada prompt ini diambil langsung dari DATABASE (real-time), jawablah berdasarkan data tersebut dan jangan pernah berhalusinasi.

4. PERLINDUNGAN DATA INTERNAL & KEUANGAN:
   - Kamu DILARANG KERAS memberikan data internal seperti: harga beli/modal (HPP), margin, data supplier, omset, atau laporan keuangan, KECUALI peran pengguna adalah admin/pimpinan dan memintanya secara resmi melalui dashboard.

5. FORMAT & GAYA KOMUNIKASI:
   - Gunakan Bahasa Indonesia yang ramah, santun, jelas, dan profesional.
   - Gunakan format Markdown (seperti **bold** untuk nama produk/harga/nomor HP, dan bullet list) agar mudah dibaca.
   - Informasikan status stok dengan jujur (Ready Stock atau Stok Habis).
   - Jika ada data yang tidak tersedia di daftar, jawab sejujurnya bahwa data tersebut tidak ditemukan.

=== DAFTAR PRODUK YANG DIJUAL TOKO (DATA REAL DARI DATABASE) ===
{$productListStr}

=== DAFTAR LAYANAN SERVIS TOKO (DATA REAL DARI DATABASE) ===
{$serviceListStr}

=== DAFTAR EKSPEDISI & PENGIRIMAN (DATA REAL DARI DATABASE) ===
{$ekspedisiListStr}

=== PANDUAN JAWABAN CEPAT (DARI ATURAN CHATBOT TOKO) ===
Gunakan panduan berikut bila pertanyaan cocok, tetapi tetaplah menyesuaikan jawaban secara natural:
{$aturanListStr}
PROMPT;
    }

    protected function esc(string $value): string
    {
        return str_replace(["\r", "\n"], ' ', trim($value));
    }

    /**
     * Mencocokkan produk & jasa relevan untuk kartu rekomendasi di UI.
     */
    protected function findRelevantRecommendations(string $userMessage, string $aiReply): array
    {
        $text = strtolower($userMessage . ' ' . $aiReply);

        $products = Produk::with('kategori')->get();
        $services = JasaServis::all();

        $matchedProducts = collect();
        $matchedServices = collect();

        foreach ($products as $p) {
            $nameLower = strtolower($p->nama_produk);
            $merkLower = strtolower($p->merk ?? '');

            if (str_contains($text, $nameLower)) {
                $matchedProducts->push($p);
            } elseif (!empty($merkLower) && str_contains($text, $merkLower) && (
                str_contains($text, strtolower($p->kategori->nama_kategori ?? '')) ||
                str_contains($nameLower, 'laptop') || str_contains($nameLower, 'printer')
            )) {
                $matchedProducts->push($p);
            }
        }

        foreach ($services as $s) {
            $jasaLower = strtolower($s->nama_jasa);
            if (str_contains($text, $jasaLower)) {
                $matchedServices->push($s);
            } else {
                if ((str_contains($text, 'install') || str_contains($text, 'instal') || str_contains($text, 'windows')) && str_contains($jasaLower, 'instal')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'lcd') || str_contains($text, 'layar')) && str_contains($jasaLower, 'lcd')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'pembersihan') || str_contains($text, 'panas') || str_contains($text, 'thermal')) && str_contains($jasaLower, 'pembersihan')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'engsel') || str_contains($text, 'casing')) && str_contains($jasaLower, 'engsel')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'recovery') || str_contains($text, 'data')) && str_contains($jasaLower, 'recovery')) {
                    $matchedServices->push($s);
                }
            }
        }

        return [
            'produk' => $matchedProducts->unique('id')->take(4)->values(),
            'jasa' => $matchedServices->unique('id')->take(3)->values(),
        ];
    }

    /**
     * Fallback cerdas: pencarian database lokal & aturan chatbot.
     */
    protected function handleFallback(string $userMessage, string $notice = ''): array
    {
        $pesanLower = strtolower($userMessage);

        if (str_contains($pesanLower, 'teknisi') || str_contains($pesanLower, 'kontak') || str_contains($pesanLower, 'nomor') || str_contains($pesanLower, 'wa') || str_contains($pesanLower, 'lokasi') || str_contains($pesanLower, 'alamat')) {
            $jawaban = "Untuk konsultasi langsung dengan teknisi atau informasi toko **Nusantara Jaya Computer**, silakan hubungi kami melalui:\n\n" .
                "📱 **WhatsApp / Kontak Toko:**\n- **0851-8239-2525**\n- **0851-8239-2526**\n\n" .
                "📍 **Alamat & Google Maps Toko:**\nhttps://share.google/xrwq12yHe0uMzcoFv\n\n" .
                "🕒 **Jam Buka:** Senin - Sabtu, 09.00 - 17.00 WITA.";

            return [
                'jawaban' => $jawaban,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ];
        }

        $ruleMatch = AturanChatbot::all()->first(function ($rule) use ($pesanLower) {
            return str_contains($pesanLower, strtolower(trim($rule->kata_kunci)));
        });
        if ($ruleMatch) {
            return [
                'jawaban' => $ruleMatch->jawaban,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ];
        }

        $words = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $pesanLower)), fn($w) => strlen($w) > 2);
        $matchedProducts = collect();
        $matchedServices = collect();

        foreach ($words as $w) {
            $prods = Produk::with('kategori')
                ->where('nama_produk', 'LIKE', "%{$w}%")
                ->orWhere('deskripsi', 'LIKE', "%{$w}%")
                ->get();
            $matchedProducts = $matchedProducts->merge($prods);

            $srvs = JasaServis::where('nama_jasa', 'LIKE', "%{$w}%")->get();
            $matchedServices = $matchedServices->merge($srvs);
        }

        $matchedProducts = $matchedProducts->unique('id')->take(4)->values();
        $matchedServices = $matchedServices->unique('id')->take(3)->values();

        $jawaban = "Halo! Selamat datang di **Nusantara Jaya Computer**.\n\n";
        if (!empty($notice)) {
            $jawaban .= "_{$notice}_\n\n";
        }

        if ($matchedProducts->isNotEmpty() || $matchedServices->isNotEmpty()) {
            $jawaban .= "Berikut informasi produk dan layanan yang kami temukan untuk Anda di toko kami:";
        } else {
            $jawaban .= "Ada yang bisa kami bantu seputar produk komputer, laptop, printer, atau layanan servis kami?\n\n" .
                "Jika Anda memerlukan bantuan teknisi secara langsung, silakan hubungi WhatsApp toko kami di **0851-8239-2525** / **0851-8239-2526**.";
        }

        return [
            'jawaban' => $jawaban,
            'rekomendasi_produk' => $matchedProducts,
            'rekomendasi_jasa' => $matchedServices,
        ];
    }
}