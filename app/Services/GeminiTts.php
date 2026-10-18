<?php
namespace App\Services;

/**
 * Gemini TTS with the Charon voice. Prebuilt Gemini voices are language-agnostic;
 * the style prompt is what gives the Lao (Vientiane) accent.
 * Used offline by scripts/generate-lao-library.php — not called while users listen.
 */
class GeminiTts {
    const MODEL = 'gemini-3.8-flash-tts';
    const VOICE = 'Charon';
    const STYLE = 'Speak in native Lao with natural Vientiane accent. Calm, slow and respectful, like a Lao monk reading a Buddhist sutra.';

    private $apiKey;
    private $timeout;

    public function __construct($apiKey = null, $timeout = 300) {
        $this->apiKey = $apiKey ?: getenv('GEMINI_API_KEY');
        $this->timeout = $timeout;
    }

    /**
     * @return array ['audio' => mp3 bytes (or wav if no encoder), 'duration' => seconds, 'timepoints' => [...]]
     *               or ['error' => true, 'message' => ..., 'retryable' => bool]
     */
    public function synthesize($text, $bitrateKbps = 32) {
        if (!$this->apiKey) return ['error' => true, 'message' => 'GEMINI_API_KEY not set', 'retryable' => false];

        $payload = json_encode([
            'model' => self::MODEL,
            'input' => [[
                'type' => 'user_input',
                'content' => [[
                    'type' => 'text',
                    'text' => $text,
                    'annotations' => [['type' => 'speech_metadata', 'style' => self::STYLE]],
                ]],
            ]],
            'response_format' => ['type' => 'audio'],
            'generation_config' => ['speech_config' => [['voice' => self::VOICE]]],
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/interactions');
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout, CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $this->apiKey],
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($code !== 200 || !$resp) {
            $msg = $resp ? (json_decode($resp, true)['error']['message'] ?? substr($resp, 0, 200)) : $curlErr;
            // Daily quota (free tier: 10 requests/day) won't recover by retrying — stop instead
            if ($code === 429 && stripos($msg, 'per day') !== false) {
                return ['error' => true, 'message' => "HTTP 429: {$msg}", 'retryable' => false];
            }
            // Per-minute rate limit, 5xx, network error — transient, worth retrying
            return ['error' => true, 'message' => "HTTP {$code}: {$msg}", 'retryable' => $code === 429 || $code >= 500 || $code === 0];
        }

        $wav = null;
        foreach ((json_decode($resp, true)['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') !== 'model_output') continue;
            foreach ($step['content'] ?? [] as $part) {
                if (($part['type'] ?? '') === 'audio' && !empty($part['data'])) $wav = base64_decode($part['data']);
            }
        }
        if (!$wav || strlen($wav) < 100) return ['error' => true, 'message' => 'No audio in response', 'retryable' => true];

        // Output is 24 kHz mono 16-bit PCM WAV = 48000 bytes per second
        $duration = round(max(0.1, (strlen($wav) - 44) / 48000), 2);

        return [
            'audio' => self::wavToMp3($wav, $bitrateKbps) ?? $wav,
            'duration' => $duration,
            'timepoints' => self::timepoints($text, $duration),
        ];
    }

    /** Spread word highlights over the real audio length, proportional to word length. */
    public static function timepoints($text, $duration) {
        $words = preg_split('/\s+/u', trim($text));
        $charCount = max(1, mb_strlen(implode(' ', $words)));
        $tps = []; $tc = 0;
        foreach ($words as $w) {
            $tps[] = ['markName' => $w, 'timeSeconds' => round($tc / $charCount * $duration, 3)];
            $tc += mb_strlen($w) + 1;
        }
        return $tps;
    }

    /** Encode WAV to MP3 with lame or ffmpeg; null when neither is installed. */
    public static function wavToMp3($wav, $bitrateKbps = 32) {
        $encoder = null;
        foreach (['lame', 'ffmpeg'] as $bin) {
            $found = trim((string) shell_exec('command -v ' . $bin . ' 2>/dev/null'));
            foreach (array_merge([$found], ['/usr/bin/' . $bin, '/usr/local/bin/' . $bin, '/opt/homebrew/bin/' . $bin]) as $path) {
                if ($path && @is_executable($path)) { $encoder = [$bin, $path]; break 2; }
            }
        }
        if (!$encoder) return null;

        $in = tempnam(sys_get_temp_dir(), 'gtts_');
        $out = $in . '.mp3';
        file_put_contents($in, $wav);
        $b = (int) $bitrateKbps;
        $cmd = $encoder[0] === 'lame'
            ? escapeshellarg($encoder[1]) . " --quiet -b {$b} " . escapeshellarg($in) . ' ' . escapeshellarg($out)
            : escapeshellarg($encoder[1]) . " -loglevel error -y -i " . escapeshellarg($in) . " -b:a {$b}k " . escapeshellarg($out);
        shell_exec($cmd . ' 2>&1');
        $mp3 = (file_exists($out) && filesize($out) > 100) ? file_get_contents($out) : null;
        @unlink($in); @unlink($out);
        return $mp3;
    }
}
