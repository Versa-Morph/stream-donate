<?php

namespace Database\Seeders;

use App\Models\BannedWord;
use Illuminate\Database\Seeder;

class BannedWordsSeeder extends Seeder
{
    /**
     * Global banned words (streamer_id = NULL).
     *
     * Categories:
     *   A. Kata kasar / umpatan umum bahasa Indonesia
     *   B. Istilah SARA / ujaran kebencian
     *   C. Promosi judi online (judol) — slot, togel, kasino online
     *   D. Konten dewasa / tidak pantas
     *   E. Kata kasar bahasa Inggris yang umum
     */
    public function run(): void
    {
        $words = [
            // ── A. Kata kasar / umpatan bahasa Indonesia ─────────────────────
            'anjing',
            'anjir',
            'anying',
            'bangsat',
            'babi',
            'bajingan',
            'brengsek',
            'bedebah',
            'celaka',
            'sialan',
            'sial',
            'keparat',
            'kampret',
            'kurang ajar',
            'tolol',
            'goblok',
            'bodoh',
            'idiot',
            'dungu',
            'pantat',
            'memek',
            'kontol',
            'pepek',
            'ngentot',
            'jancok',
            'jancuk',
            'cok',
            'dancok',
            'asu',
            'bajing',
            'taek',
            'tai',
            'tahi',
            'setan',
            'iblis',
            'laknat',
            'mampus',
            'matamu',
            'persetan',
            'brengsek',
            'bacot',
            'mulut sampah',
            'bego',
            'geblek',
            'pekok',
            'budek',
            'buta',
            'lonte',
            'pelacur',
            'sundal',
            'PSK',
            'jalang',
            'murahan',

            // ── B. Istilah SARA / ujaran kebencian ───────────────────────────
            'kafir',
            'pribumi',
            'cina babi',
            'inlander',
            'bumiputera hina',

            // ── C. Promosi judi online (judol) ───────────────────────────────
            // Istilah slot / mesin judi
            'slot gacor',
            'slot online',
            'situs slot',
            'daftar slot',
            'link slot',
            'gacor hari ini',
            'gacor maxwin',
            'maxwin',
            'scatter hitam',
            'scatter emas',
            'scatter mahjong',
            'mahjong ways',
            'mahjong wins',
            'gates of olympus',
            'starlight princess',
            'sweet bonanza',
            'wild west gold',
            'pragmatic play',
            'pgsoft',
            'pg soft',
            'habanero',
            'joker123',
            'joker gaming',
            'slot88',
            'slot777',
            'nexus slot',
            'server thailand',
            'server luar',
            // Istilah togel / lotere ilegal
            'togel',
            'toto gelap',
            'shio togel',
            'prediksi sgp',
            'prediksi hk',
            'prediksi sdy',
            'angka jitu',
            'bocoran togel',
            'colok bebas',
            'colok jitu',
            // Istilah kasino / taruhan online
            'casino online',
            'kasino online',
            'live casino',
            'baccarat online',
            'roulette online',
            'poker online',
            'domino online',
            'domino qiu',
            'ceme online',
            'capsa susun',
            'bandar online',
            'agen sbobet',
            'sbobet',
            'ibcbet',
            'maxbet',
            'sportbook',
            'taruhan bola',
            'judi bola',
            'judi online',
            'judol',
            'deposit pulsa',
            'deposit dana',
            'wd lancar',
            'wd cepat',
            'withdraw cepat',
            'bonus new member',
            'bonus deposit',
            'promo slot',
            'rtp live',
            'rtp slot',
            'bocoran rtp',
            'anti rungkad',
            'pola gacor',
            'pola slot',
            'trik slot',
            'hack slot',
            'cheat slot',

            // ── D. Konten dewasa ─────────────────────────────────────────────
            'bokep',
            'porno',
            'pornografi',
            'seks bebas',
            'video mesum',
            'film dewasa',

            // ── E. Kata kasar bahasa Inggris (umum) ─────────────────────────
            'fuck',
            'fucking',
            'motherfucker',
            'shit',
            'bullshit',
            'asshole',
            'bitch',
            'bastard',
            'damn',
            'cunt',
            'dickhead',
            'cock',
            'faggot',
            'whore',
            'slut',
            'nigger',
            'nigga',
        ];

        $now = now();
        $rows = [];

        foreach ($words as $word) {
            $rows[] = [
                'word'        => mb_strtolower(trim($word)),
                'streamer_id' => null,
                'created_by'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Insert ignoring duplicates (safe to re-run seeder)
        foreach (array_chunk($rows, 50) as $chunk) {
            BannedWord::upsert($chunk, ['word', 'streamer_id'], ['updated_at']);
        }

        $this->command->info('BannedWordsSeeder: ' . count($rows) . ' global banned words seeded.');
    }
}
