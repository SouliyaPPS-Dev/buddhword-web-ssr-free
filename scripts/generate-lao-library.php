#!/usr/bin/env php
<?php
/**
 * Pre-generate the Lao sutra audio library with Gemini TTS (Charon voice).
 *
 * Each sutra is turned into exactly the chunks the sutra page sends to /api/tts/synthesize
 * (same text extraction + 800-char splitting), so the server finds them by key at play time.
 * Output: storage/tts-library/lo/<key>.mp3|.json and storage/tts-library/index.json.
 * Resumable: chunks that already have an .mp3 are skipped.
 *
 * Usage:
 *   php scripts/generate-lao-library.php --dry-run            # count chunks, audio hours, cost — no API calls
 *   php scripts/generate-lao-library.php --limit=3            # first 3 Lao sutras
 *   php scripts/generate-lao-library.php --ids=ID1,ID2        # specific sutras
 *   php scripts/generate-lao-library.php --shard=1/4          # run 4 terminals (1/4 … 4/4) in parallel
 *   php scripts/generate-lao-library.php --index-only         # rebuild index.json
 *
 * Publish afterwards (pushes to the `tts-library` branch of the Space repo over SSH):
 *   sh scripts/upload-lao-library.sh
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
spl_autoload_register(function ($class) use ($root) {
    if (strncmp($class, 'App\\', 4) !== 0) return;
    $file = $root . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require $file;
});
foreach (['.env', '.env.local'] as $envFile) {
    if (!file_exists("$root/$envFile")) continue;
    foreach (file("$root/$envFile", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
        putenv(trim($k) . '=' . trim($v));
    }
}
require_once $root . '/app/Helpers/cache.php';
require_once $root . '/app/Helpers/http.php';

use App\Models\Sutra;
use App\Services\GeminiTts;
use App\Services\LaoAudioLibrary;

$args = [];
foreach (array_slice($argv, 1) as $a) {
    if (strpos($a, '--') !== 0) continue;
    $parts = explode('=', substr($a, 2), 2);
    $args[$parts[0]] = $parts[1] ?? true;
}

if (isset($args['index-only'])) {
    echo 'index.json: ' . LaoAudioLibrary::writeIndex() . " chunks\n";
    exit(0);
}

// Collect chunks in sutra order, de-duplicated by key
$sutras = array_values(array_filter(Sutra::getAll(), function ($s) {
    $text = LaoAudioLibrary::browserText($s['ພຣະສູດ'] ?? '');
    return $text !== '' && LaoAudioLibrary::isLao($text);
}));
if (isset($args['ids'])) {
    $ids = array_map('trim', explode(',', $args['ids']));
    $sutras = array_values(array_filter($sutras, fn($s) => in_array((string) $s['ID'], $ids, true)));
}
if (isset($args['limit'])) $sutras = array_slice($sutras, 0, (int) $args['limit']);

$chunks = [];
foreach ($sutras as $s) {
    foreach (LaoAudioLibrary::splitChunks(LaoAudioLibrary::browserText($s['ພຣະສູດ'])) as $chunk) {
        $key = LaoAudioLibrary::key($chunk);
        if (!isset($chunks[$key])) $chunks[$key] = ['text' => $chunk, 'sutra' => $s['ID']];
    }
}
if (isset($args['shard'])) {
    [$k, $n] = array_map('intval', explode('/', $args['shard']));
    $i = 0;
    $chunks = array_filter($chunks, function () use (&$i, $k, $n) { return ($i++ % $n) === $k - 1; });
}

$todo = array_filter($chunks, fn($c) => !LaoAudioLibrary::has($c['text']));
$chars = array_sum(array_map(fn($c) => mb_strlen($c['text']), $todo));
// Measured: Charon reads ~5.5 Lao chars/s and Gemini bills ~24 audio tokens per second of audio
$hours = $chars / 5.5 / 3600;
$tokens = $hours * 3600 * 24;
printf("Sutras: %d | chunks: %d | to generate: %d (%s chars)\n", count($sutras), count($chunks), count($todo), number_format($chars));
printf("Estimate: %.1f h of audio, %.1fM audio tokens ≈ $%.0f at $9/1M ($%.0f at $18/1M from 2027), ~%.0f MB at 32 kbps\n",
    $hours, $tokens / 1e6, $tokens / 1e6 * 9, $tokens / 1e6 * 18, $hours * 3600 * 4 / 1024);
if (isset($args['dry-run']) || !$todo) exit(0);

$tts = new GeminiTts();
$done = 0; $failed = 0; $start = microtime(true); $total = count($todo);
foreach ($todo as $key => $c) {
    $attempt = 0;
    while (true) {
        $res = $tts->synthesize($c['text'], (int) ($args['bitrate'] ?? 32));
        if (!isset($res['error'])) break;
        if (!$res['retryable'] || ++$attempt > 5) break;
        $wait = 30 * (2 ** ($attempt - 1)); // 30s, 60s, 120s, 240s, 480s
        echo "\n  [{$key}] {$res['message']} — retry {$attempt}/5 in {$wait}s";
        sleep($wait);
    }
    if (isset($res['error'])) {
        $failed++;
        echo "\n  FAILED sutra {$c['sutra']} [{$key}]: {$res['message']}\n";
        if (!$res['retryable']) { // missing key or daily quota — no point continuing
            echo 'index.json: ' . LaoAudioLibrary::writeIndex() . " chunks. Re-run later to resume.\n";
            exit(1);
        }
        continue;
    }
    LaoAudioLibrary::save($c['text'], $res['audio'], $res['duration'], $res['timepoints']);
    $done++;
    $elapsed = microtime(true) - $start;
    printf("\r  [%d/%d] sutra %s, %.0fs audio | elapsed %s", $done + $failed, $total, $c['sutra'], $res['duration'], gmdate('H:i:s', (int) $elapsed));
    if ($done % 25 === 0) LaoAudioLibrary::writeIndex();
}

echo "\n\nGenerated: {$done}, failed: {$failed} (re-run to retry failures)\n";
echo 'index.json: ' . LaoAudioLibrary::writeIndex() . " chunks in " . LaoAudioLibrary::dir() . "\n";
