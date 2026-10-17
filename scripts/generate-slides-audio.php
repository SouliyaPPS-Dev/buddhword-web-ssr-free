#!/usr/bin/env php
<?php
/**
 * Pre-generates the book slide deck narration (app/Data/slides_narration.php) as static MP3s
 * with the same male Edge neural voices as TtsLibrary, via the `edge-tts` CLI (pip install edge-tts).
 *
 *   php scripts/generate-slides-audio.php           # only missing files
 *   php scripts/generate-slides-audio.php --force   # regenerate everything
 *   php scripts/generate-slides-audio.php --prune   # also delete files no sentence uses any more
 *
 * Files are named by a hash of voice + text (see slidesAudioFile()), so editing a sentence
 * just needs this script run again. Sentences without a file fall back to /api/tts/synthesize.
 */

require __DIR__ . '/../app/Data/slides_audio.php';

$narration = require __DIR__ . '/../app/Data/slides_narration.php';
$force = in_array('--force', $argv, true);
$prune = in_array('--prune', $argv, true);

$edge = trim((string) shell_exec('command -v edge-tts 2>/dev/null'));
if ($edge === '') {
    fwrite(STDERR, "edge-tts not found — install with: pip install edge-tts\n");
    exit(1);
}

// Mixed Lao/Thai + English sentences are stitched together with ffmpeg (brew install ffmpeg).
$ffmpeg = trim((string) shell_exec('command -v ffmpeg 2>/dev/null'));

function speak(string $edge, string $voice, string $text, string $file): string {
    $tmp = tempnam(sys_get_temp_dir(), 'sl_');
    file_put_contents($tmp, $text);
    $out = shell_exec(escapeshellarg($edge)
        . ' --file ' . escapeshellarg($tmp)
        . ' --voice ' . escapeshellarg(SLIDES_VOICES[$voice])
        . ' --rate=-5% --volume=+40%'
        . ' --write-media ' . escapeshellarg($file) . ' 2>&1');
    unlink($tmp);
    return (string) $out;
}

// Re-encodes the parts into one MP3 (all Edge voices share 24 kHz mono, so the joins are seamless).
function join_mp3(string $ffmpeg, array $parts, string $file): string {
    if ($ffmpeg === '') return 'ffmpeg not found — needed for sentences that mix in English';
    $in = implode(' ', array_map(fn($p) => '-i ' . escapeshellarg($p), $parts));
    $streams = implode('', array_map(fn($k) => "[$k:a]", array_keys($parts)));
    return (string) shell_exec(escapeshellarg($ffmpeg) . " -y -loglevel error $in -filter_complex "
        . escapeshellarg($streams . 'concat=n=' . count($parts) . ':v=0:a=1')
        . ' -ar 24000 -ac 1 -b:a 48k ' . escapeshellarg($file) . ' 2>&1');
}

$keep = [];
$made = $skipped = $failed = 0;
foreach ($narration as $lang => $slides) {
    $dir = SLIDES_AUDIO_DIR . '/' . $lang;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    foreach ($slides as $s => $lines) {
        foreach ($lines as $n => $text) {
            $path = SLIDES_AUDIO_DIR . '/' . slidesAudioFile($lang, $text);
            $keep[$path] = true;
            if (!$force && is_file($path) && filesize($path) > 1000) { $skipped++; continue; }

            // Each run in its own voice (Lao/Thai ↔ English), then joined into one file per sentence.
            $segs = slidesSegments($lang, $text);
            $parts = [];
            $out = '';
            foreach ($segs as [$voice, $seg]) {
                $part = count($segs) > 1 ? sys_get_temp_dir() . '/slp_' . uniqid('', true) . '.mp3' : $path;
                $out .= speak($edge, $voice, $seg, $part);
                $parts[] = $part;
            }
            if (count($parts) > 1) {
                $ok = array_filter($parts, fn($p) => is_file($p) && filesize($p) > 500);
                if (count($ok) === count($parts)) $out .= join_mp3($ffmpeg, $parts, $path);
                foreach ($parts as $p) @unlink($p);
            }

            if (is_file($path) && filesize($path) > 1000) {
                chmod($path, 0644);
                $made++;
                printf("ok   %s slide %2d line %d\n", $lang, $s + 1, $n + 1);
            } else {
                @unlink($path);
                $failed++;
                printf("FAIL %s slide %2d line %d: %s\n", $lang, $s + 1, $n + 1, trim((string) $out));
            }
        }
    }
}

if ($prune) {
    foreach (glob(SLIDES_AUDIO_DIR . '/*/*.mp3') as $f) {
        if (!isset($keep[$f])) { unlink($f); echo "pruned $f\n"; }
    }
}

echo "\nGenerated $made, already had $skipped, failed $failed.\n";
exit($failed ? 1 : 0);
