<?php
// Shared by BookController::slidesNarration() and scripts/generate-slides-audio.php so both agree on file names.
// Male Edge neural voices, the same as TtsService (lo-LA-Chanthavong, th-TH-Niwat);
// English words inside a Lao/Thai sentence are read by a male English voice, then it switches back.
const SLIDES_VOICES = ['lo' => 'lo-LA-ChanthavongNeural', 'th' => 'th-TH-NiwatNeural', 'en' => 'en-US-ChristopherNeural'];
define('SLIDES_AUDIO_DIR', dirname(__DIR__, 2) . '/public/assets/audio/slides');

// Splits a sentence into voice runs: [['lo', 'ສ່ວນດາວໃກ້ທີ່ສຸດ ຄື'], ['en', 'Proxima Centauri'], ['lo', 'ແສງໃຊ້ເວລາ'], ...].
// An English run is Latin words plus the spaces/punctuation between them ("NASA, NIST"); digits stay with the native text.
function slidesSegments(string $lang, string $text): array {
    $out = [];
    foreach (preg_split("/([A-Za-z][A-Za-z0-9.,&'-]*(?:[ ,]+[A-Za-z][A-Za-z0-9.,&'-]*)*)/u", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $part) {
        $isEn = preg_match('/^[A-Za-z]/', $part) === 1;
        $part = trim($part, " \t,");
        if ($part === '' || !preg_match('/[\p{L}\p{N}]/u', $part)) continue;
        $out[] = [$isEn ? 'en' : $lang, $part];
    }
    return $out;
}

// Relative to SLIDES_AUDIO_DIR, e.g. "lo/3f9a1c2b7d4e.mp3". Mixed sentences also hash the English voice.
function slidesAudioFile(string $lang, string $text): string {
    $mixed = count(array_unique(array_column(slidesSegments($lang, $text), 0))) > 1;
    return $lang . '/' . substr(md5(SLIDES_VOICES[$lang] . ($mixed ? '+' . SLIDES_VOICES['en'] : '') . '|' . $text), 0, 12) . '.mp3';
}
