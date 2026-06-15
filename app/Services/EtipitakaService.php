<?php
namespace App\Services;

class EtipitakaService {
    private static array $databases = [];

    private static array $labelMap = [
        'thai' => 'ไทย (ฉบับหลวง)',
        'pali' => 'บาลี (สยามรัฐ)',
        'thaimm' => 'ไทย (มหามกุฏฯ)',
        'thaimc' => 'ไทย (มหาจุฬาฯ)',
        'thaipb' => 'พุทธวจน-หมวดธรรม',
        'thaibt' => 'ชุดจากพระโอษฐ์ ๕ เล่ม',
        'thaiwn' => 'ไทย (wn)',
        'thaict' => 'ไทย (ct)',
        'romanct' => 'โรมัน (ct)',
        'palimc' => 'บาลี (mc)',
        'thaims' => 'ไทย (ms)',
        'thaivn' => 'ไทย (vn)',
        'palinew' => 'บาลี (new)',
    ];

    private static array $excludedCodes = [
        'thaiwn', 'thaict', 'romanct', 'palimc', 'thaims', 'thaivn', 'palinew',
    ];

    private static string $dbDir = '';

    private static function init(): void {
        self::$dbDir = __DIR__ . '/../../databases/';
    }

    public static function getCategories(): array {
        self::init();
        $categories = [];
        $files = glob(self::$dbDir . '*.sqlite.gz');
        foreach ($files as $file) {
            $code = basename($file, '.sqlite.gz');
            if (in_array($code, self::$excludedCodes)) continue;
            $label = self::$labelMap[$code] ?? $code;
            $categories[] = ['code' => $code, 'label' => $label];
        }
        return $categories;
    }

    private static function getDbPath(string $code): ?string {
        self::init();
        $gzPath = self::$dbDir . $code . '.sqlite.gz';
        if (!file_exists($gzPath)) return null;
        $cacheDir = __DIR__ . '/../../storage/cache/sqlite/';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
            if (!is_dir($cacheDir)) {
                error_log('EtipitakaService: cannot create cache dir ' . $cacheDir);
                return null;
            }
        }
        $dbPath = $cacheDir . $code . '.sqlite';
        if (file_exists($dbPath) && filemtime($dbPath) >= filemtime($gzPath)) {
            return $dbPath;
        }
        try {
            $gzData = file_get_contents($gzPath);
            if ($gzData === false) {
                error_log('EtipitakaService: cannot read ' . $gzPath);
                return null;
            }
            $data = gzdecode($gzData);
            if ($data === false) {
                error_log('EtipitakaService: gzdecode failed for ' . $gzPath);
                return null;
            }
            file_put_contents($dbPath, $data);
        } catch (\Throwable $e) {
            error_log('EtipitakaService: decompression error: ' . $e->getMessage());
            return null;
        }
        return $dbPath;
    }

    private static function getDb(string $code): ?\SQLite3 {
        if (isset(self::$databases[$code])) {
            return self::$databases[$code];
        }
        $dbPath = self::getDbPath($code);
        if (!$dbPath) return null;
        $db = new \SQLite3($dbPath);
        $db->exec('PRAGMA journal_mode=OFF');
        $db->exec('PRAGMA query_only=ON');
        self::$databases[$code] = $db;
        return $db;
    }

    public static function search(string $code, string $query, int $limit = 200): array {
        $db = self::getDb($code);
        if (!$db) return [];
        $stmt = $db->prepare("SELECT volume, page, items, content FROM main WHERE content LIKE :q ORDER BY CAST(volume AS INTEGER), CAST(page AS INTEGER) LIMIT :lim");
        $stmt->bindValue(':q', '%' . $query . '%', SQLITE3_TEXT);
        $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public static function getContent(string $code, int $volume, int $page): ?array {
        $db = self::getDb($code);
        if (!$db) return null;
        $volStr = str_pad((string)$volume, 2, '0', STR_PAD_LEFT);
        $pageStr = str_pad((string)$page, 4, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("SELECT volume, page, items, content FROM main WHERE volume = :vol AND page = :page LIMIT 1");
        $stmt->bindValue(':vol', $volStr, SQLITE3_TEXT);
        $stmt->bindValue(':page', $pageStr, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }

    public static function getTotalPages(string $code, int $volume): int {
        $db = self::getDb($code);
        if (!$db) return 0;
        $volStr = str_pad((string)$volume, 2, '0', STR_PAD_LEFT);
        $result = $db->querySingle("SELECT COUNT(*) FROM main WHERE volume = '{$volStr}'");
        return (int)$result;
    }

    public static function getVolumes(string $code): array {
        $db = self::getDb($code);
        if (!$db) return [];
        $result = $db->query("SELECT DISTINCT volume FROM main ORDER BY CAST(volume AS INTEGER)");
        $volumes = [];
        while ($row = $result->fetchArray(SQLITE3_NUM)) {
            $volumes[] = (int)$row[0];
        }
        return $volumes;
    }

    public static function getLabel(string $code): string {
        return self::$labelMap[$code] ?? $code;
    }

    public static function isAvailable(string $code): bool {
        self::init();
        return file_exists(self::$dbDir . $code . '.sqlite.gz');
    }
}
