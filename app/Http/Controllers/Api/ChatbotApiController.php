<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Mua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatbotApiController extends Controller
{
    // Intent keywords map
    private array $intentMap = [
        'cari_mua'      => ['cari', 'rekomendasi', 'mua', 'makeup', 'salon', 'siapa'],
        'booking'       => ['booking', 'pesan', 'jadwal', 'reservasi', 'sewa'],
        'harga'         => ['harga', 'biaya', 'tarif', 'berapa', 'budget'],
        'lokasi'        => ['lokasi', 'daerah', 'kota', 'dimana', 'area', 'dekat'],
        'wedding'       => ['wedding', 'nikah', 'pernikahan', 'pengantin', 'bride'],
        'wisuda'        => ['wisuda', 'graduation', 'kelulusan'],
        'rating'        => ['rating', 'terbaik', 'bagus', 'recommended', 'top'],
        'salam'         => ['halo', 'hai', 'hello', 'selamat', 'hi'],
        'terima_kasih'  => ['terima kasih', 'makasih', 'thanks', 'thank you'],
    ];

    /**
     * POST /api/chatbot/message
     */
    public function message(Request $request)
    {
        $user = JWTAuth::user();

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userMessage = trim($request->message);
        $intent      = $this->detectIntent($userMessage);
        $params      = [];

        [$response, $params] = $this->generateResponse($intent, $userMessage);

        // Log percakapan
        ChatbotLog::create([
            'user_id'         => $user->id,
            'user_message'    => $userMessage,
            'detected_intent' => $intent,
            'bot_response'    => $response,
            'parameters'      => $params,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'intent'    => $intent,
                'message'   => $response,
                'params'    => $params,
            ],
        ]);
    }

    // ─── Intent Detection ────────────────────────────────────────────
    private function detectIntent(string $text): string
    {
        $lower = strtolower($text);
        $scores = [];

        foreach ($this->intentMap as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) $score++;
            }
            if ($score > 0) $scores[$intent] = $score;
        }

        if (empty($scores)) return 'unknown';
        arsort($scores);
        return array_key_first($scores);
    }

    // ─── Response Generator ───────────────────────────────────────────
    private function generateResponse(string $intent, string $text): array
    {
        return match($intent) {
            'salam'       => ["Halo! 👋 Selamat datang di BeautyHub. Saya siap membantu Anda menemukan MUA terbaik untuk kebutuhan Anda. Apa yang bisa saya bantu?", []],
            'terima_kasih'=> ["Sama-sama! 😊 Senang bisa membantu. Ada yang ingin ditanyakan lagi?", []],
            'cari_mua'    => $this->handleCariMua($text),
            'wedding'     => $this->handleWedding(),
            'wisuda'      => $this->handleWisuda(),
            'harga'       => $this->handleHarga(),
            'lokasi'      => $this->handleLokasi($text),
            'rating'      => $this->handleTopRating(),
            'booking'     => ["Untuk melakukan booking, pilih MUA yang Anda inginkan, kemudian klik tombol 'Pesan Sekarang'. Isi detail acara, tanggal, dan lokasi Anda. 📅", []],
            default       => ["Maaf, saya kurang memahami pertanyaan Anda. Coba tanyakan tentang: cari MUA, harga layanan, lokasi, atau cara booking. 😊", []],
        };
    }

    private function handleCariMua(string $text): array
    {
        $muas = Mua::with('user')
            ->where('is_verified', true)
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        if ($muas->isEmpty()) {
            return ["Saat ini belum ada MUA terverifikasi. Coba lagi nanti ya!", []];
        }

        $list = $muas->map(fn($m) =>
            "• **{$m->user->name}** — ⭐ {$m->rating} | {$m->location}"
        )->implode("\n");

        return [
            "Berikut beberapa MUA terbaik yang bisa saya rekomendasikan:\n\n{$list}\n\nKlik profil mereka untuk melihat portfolio dan layanan lengkap! 💄",
            ['mua_ids' => $muas->pluck('id')],
        ];
    }

    private function handleWedding(): array
    {
        $muas = Mua::with('user')
            ->whereJsonContains('style_tags', 'wedding')
            ->orWhereHas('services', fn($q) =>
                $q->where('category', 'wedding')->where('is_active', true)
            )
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        $list = $muas->isNotEmpty()
            ? $muas->map(fn($m) => "• **{$m->user->name}** — ⭐ {$m->rating}")->implode("\n")
            : "Semua MUA kami siap membantu pernikahan Anda!";

        return [
            "💍 Untuk makeup pernikahan, kami merekomendasikan:\n\n{$list}\n\nMUA wedding kami berpengalaman dalam riasan adat dan modern!",
            ['category' => 'wedding'],
        ];
    }

    private function handleWisuda(): array
    {
        return [
            "🎓 Selamat akan wisuda! Kami punya MUA spesialis makeup wisuda yang natural, elegan, dan tahan lama untuk sesi foto panjang.\n\nSilakan cari MUA dengan filter kategori 'Wisuda' di aplikasi ini!",
            ['category' => 'graduation'],
        ];
    }

    private function handleHarga(): array
    {
        $minPrice = \App\Models\Service::where('is_active', true)->min('price') ?? 0;
        $maxPrice = \App\Models\Service::where('is_active', true)->max('price') ?? 0;

        return [
            "💰 Harga layanan MUA di BeautyHub bervariasi mulai dari Rp " .
            number_format($minPrice, 0, ',', '.') . " hingga Rp " .
            number_format($maxPrice, 0, ',', '.') . ".\n\nHarga tergantung jenis layanan, pengalaman MUA, dan jarak lokasi. Cek profil masing-mua untuk detail harga!",
            ['price_range' => ['min' => $minPrice, 'max' => $maxPrice]],
        ];
    }

    private function handleLokasi(string $text): array
    {
        $locations = Mua::select('location')
            ->distinct()
            ->whereNotNull('location')
            ->pluck('location')
            ->unique()
            ->values();

        $list = $locations->map(fn($l) => "• {$l}")->implode("\n");

        return [
            "📍 MUA kami tersedia di berbagai kota:\n\n{$list}\n\nGunakan filter lokasi di halaman pencarian untuk menemukan MUA terdekat Anda!",
            ['available_locations' => $locations],
        ];
    }

    private function handleTopRating(): array
    {
        $muas = Mua::with('user')
            ->where('rating', '>=', 4.5)
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        $list = $muas->map(fn($m) =>
            "⭐ **{$m->user->name}** — {$m->rating}/5.0 ({$m->total_reviews} ulasan)"
        )->implode("\n");

        return [
            "🏆 MUA dengan rating terbaik:\n\n{$list}\n\nSemua MUA ini telah melalui verifikasi BeautyHub!",
            ['top_mua_ids' => $muas->pluck('id')],
        ];
    }
}
