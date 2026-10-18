<?php
namespace App\Services;

require_once __DIR__ . '/../Helpers/cache.php';

/**
 * Pre-generated Lao sutra audio (Gemini Charon), generated once by scripts/generate-lao-library.php.
 *
 * Layout (same locally and on the `tts-library` branch of the Space repo, see scripts/upload-lao-library.sh):
 *   lo/<key>.mp3, lo/<key>.json  {text, duration, timepoints, voice, created}
 *   index.json                   {voice, updated, keys: {<key>: duration}}
 *
 * Lookup: storage/tts-library (local copy) → TTS_LIBRARY_URL (downloaded into the local copy).
 * The key is independent of TtsService::cacheKey() so bumping that cache version never orphans this audio.
 */
class LaoAudioLibrary {
    const LANG = 'lo-LA';
    const DEFAULT_REMOTE = 'https://huggingface.co/spaces/buddhaword/web/resolve/tts-library';
    const INDEX_CACHE = 'tts_library_index.json';
    const INDEX_MAX_AGE = 21600; // 6 hours

    private static $remoteIndex = null;

    public static function dir() {
        return dirname(__DIR__, 2) . '/storage/tts-library';
    }

    public static function key($text) {
        return md5(TtsService::normalizeText($text) . '|' . self::LANG . '|charon');
    }

    /** @return array|null ['audioContent' => base64, 'timepoints' => [...]] */
    public static function find($text) {
        $key = self::key($text);
        $local = self::readLocal($key);
        if ($local) return $local;

        $remote = getenv('TTS_LIBRARY_URL');
        if ($remote === 'off') return null;
        $base = rtrim($remote ?: self::DEFAULT_REMOTE, '/');

        $index = self::remoteIndex($base);
        if (!isset($index[$key])) return null;

        $mp3 = self::fetch("{$base}/lo/{$key}.mp3");
        $json = self::fetch("{$base}/lo/{$key}.json");
        if (!$mp3 || strlen($mp3) < 100 || !$json) return null;

        // Keep a local copy so the dataset is hit once per chunk, not once per listen
        $dir = self::dir() . '/lo';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        if (is_writable($dir)) {
            @file_put_contents("{$dir}/{$key}.mp3", $mp3);
            @file_put_contents("{$dir}/{$key}.json", $json);
        }

        $meta = json_decode($json, true) ?: [];
        return ['audioContent' => base64_encode($mp3), 'timepoints' => $meta['timepoints'] ?? [], 'library' => true];
    }

    public static function has($text) {
        return file_exists(self::dir() . '/lo/' . self::key($text) . '.mp3');
    }

    /** Store one generated chunk locally (used by the generator script). */
    public static function save($text, $audio, $duration, $timepoints) {
        $dir = self::dir() . '/lo';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $key = self::key($text);
        file_put_contents("{$dir}/{$key}.json", json_encode([
            'text' => TtsService::normalizeText($text),
            'duration' => $duration,
            'timepoints' => $timepoints,
            'voice' => GeminiTts::VOICE,
            'created' => gmdate('Y-m-d\TH:i:s\Z'),
        ], JSON_UNESCAPED_UNICODE));
        // mp3 last: its presence marks the chunk as done
        file_put_contents("{$dir}/{$key}.mp3", $audio);
        return $key;
    }

    /** Rebuild index.json from the local files. */
    public static function writeIndex() {
        $keys = [];
        foreach (glob(self::dir() . '/lo/*.mp3') ?: [] as $mp3) {
            $key = basename($mp3, '.mp3');
            $meta = json_decode((string) @file_get_contents(substr($mp3, 0, -4) . '.json'), true);
            $keys[$key] = $meta['duration'] ?? 0;
        }
        ksort($keys);
        file_put_contents(self::dir() . '/index.json', json_encode([
            'voice' => GeminiTts::VOICE,
            'updated' => gmdate('Y-m-d\TH:i:s\Z'),
            'keys' => (object) $keys,
        ]));
        return count($keys);
    }

    /**
     * Reproduce getSutraText() in views/pages/sutra/show.php: the sutra field rendered via
     * x-html (\n → <br>), read with innerText, whitespace collapsed with JS /\s+/g, then trim().
     */
    public static function browserText($field) {
        $html = str_replace("\n", '<br>', (string) $field);
        // innerText turns line breaks and block boundaries into newlines, which then collapse to spaces
        $html = preg_replace('#<br\s*/?>|</?(p|div|li|ul|ol|h[1-6]|blockquote|tr|table|section|article|pre|hr)\b[^>]*>#i', ' ', $html);
        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return self::jsTrim(preg_replace('/' . self::JS_WS . '+/u', ' ', $text));
    }

    /**
     * Port of splitTextChunks() in views/pages/sutra/show.php. JS string indices count UTF-16
     * code units (an emoji is 2), so the text is split as an array of UTF-16 units.
     */
    public static function splitChunks($text, $maxLen = 800) {
        $units = str_split(mb_convert_encoding($text, 'UTF-16BE', 'UTF-8'), 2);
        if (count($units) <= $maxLen) return [$text];
        $chunks = [];
        while (count($units) > 0) {
            if (count($units) <= $maxLen) { $chunks[] = self::fromUnits($units); break; }
            $idx = self::unitsLastIndexOf($units, '.', $maxLen);
            if ($idx < $maxLen * 0.3) $idx = self::unitsLastIndexOf($units, ' ', $maxLen);
            if ($idx < $maxLen * 0.3) $idx = $maxLen;
            else $idx++;
            $chunks[] = self::jsTrim(self::fromUnits(array_slice($units, 0, $idx)));
            $rest = self::jsTrim(self::fromUnits(array_slice($units, $idx)));
            $units = $rest === '' ? [] : str_split(mb_convert_encoding($rest, 'UTF-16BE', 'UTF-8'), 2);
        }
        return $chunks;
    }

    /** Same test as detectLanguage() in the sutra page. */
    public static function isLao($text) {
        $lao = preg_match_all('/[\x{0E80}-\x{0EFF}]/u', $text);
        $thai = preg_match_all('/[\x{0E00}-\x{0E7F}]/u', $text);
        $eng = preg_match_all('/[a-zA-Z]/', $text);
        return $lao > $thai && $lao > $eng;
    }

    // Characters matched by JavaScript's \s (and stripped by String.prototype.trim)
    const JS_WS = '[\t\n\x{0B}\f\r \x{A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}]';

    private static function jsTrim($s) {
        return preg_replace('/^' . self::JS_WS . '+|' . self::JS_WS . '+$/u', '', $s);
    }

    /** String.prototype.lastIndexOf(char, fromIndex) over UTF-16 units. */
    private static function unitsLastIndexOf(array $units, $char, $fromIndex) {
        $needle = "\0" . $char;
        for ($i = min($fromIndex, count($units) - 1); $i >= 0; $i--) {
            if ($units[$i] === $needle) return $i;
        }
        return -1;
    }

    private static function fromUnits(array $units) {
        return mb_convert_encoding(implode('', $units), 'UTF-8', 'UTF-16BE');
    }

    private static function readLocal($key) {
        $mp3 = self::dir() . "/lo/{$key}.mp3";
        if (!file_exists($mp3)) return null;
        $meta = json_decode((string) @file_get_contents(self::dir() . "/lo/{$key}.json"), true) ?: [];
        return ['audioContent' => base64_encode(file_get_contents($mp3)), 'timepoints' => $meta['timepoints'] ?? [], 'library' => true];
    }

    private static function remoteIndex($base) {
        if (self::$remoteIndex !== null) return self::$remoteIndex;
        $json = readCache(self::INDEX_CACHE, self::INDEX_MAX_AGE);
        if ($json === null) {
            $json = self::fetch("{$base}/index.json");
            if ($json) writeCache(self::INDEX_CACHE, $json);
            else {
                // Dataset unreachable or not created yet: use the stale index, or cache "empty"
                // so misses don't re-fetch index.json on every request for the next 6 hours
                $json = readCache(self::INDEX_CACHE, PHP_INT_MAX) ?? '{"keys":{}}';
                writeCache(self::INDEX_CACHE, $json);
            }
        }
        $data = $json ? json_decode($json, true) : null;
        return self::$remoteIndex = (array) ($data['keys'] ?? []);
    }

    private static function fetch($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 6,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && $body !== false) ? $body : null;
    }
}
