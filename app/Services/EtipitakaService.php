<?php
namespace App\Services;

class EtipitakaService {
    private static array $databases = [];
    private static array $schemaCache = [];

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

    private static function getTableInfo(string $code): ?array {
        if (isset(self::$schemaCache[$code])) {
            return self::$schemaCache[$code];
        }
        $db = self::getDb($code);
        if (!$db) return null;

        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tables = [];
        while ($row = $result->fetchArray(SQLITE3_NUM)) {
            $tables[] = $row[0];
        }

        $table = null;
        if (in_array('main', $tables)) $table = 'main';
        elseif (in_array('speech', $tables)) $table = 'speech';

        if (!$table) {
            self::$schemaCache[$code] = null;
            return null;
        }

        $columns = [];
        $colTypes = [];
        $result = $db->query("PRAGMA table_info(\"$table\")");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $columns[] = $row['name'];
            $colTypes[$row['name']] = $row['type'];
        }

        $volumeCol = in_array('volume', $columns) ? 'volume' : (in_array('book', $columns) ? 'book' : null);
        $isPadded = $volumeCol === 'volume' && isset($colTypes['volume']) && stripos($colTypes['volume'], 'VARCHAR') === 0;

        $info = [
            'table' => $table,
            'columns' => $columns,
            'volumeCol' => $volumeCol,
            'itemsCol' => in_array('items', $columns) ? 'items' : (in_array('title', $columns) ? 'title' : null),
            'hasContent' => in_array('content', $columns),
            'hasPage' => in_array('page', $columns),
            'isPadded' => $isPadded,
        ];
        self::$schemaCache[$code] = $info;
        return $info;
    }

    public static function search(string $code, string $query, int $limit = 200): array {
        $db = self::getDb($code);
        if (!$db) return [];

        $info = self::getTableInfo($code);
        if (!$info || !$info['hasContent'] || !$info['hasPage']) return [];

        $table = $info['table'];
        $volumeCol = $info['volumeCol'] ?? 'NULL';
        $itemsCol = $info['itemsCol'] ?? 'NULL';
        $selectVol = $volumeCol !== 'NULL' ? $volumeCol : 'NULL';
        $selectItems = $itemsCol !== 'NULL' ? $itemsCol : "''";

        $sql = "SELECT {$selectVol} AS volume, page, {$selectItems} AS items, content FROM \"{$table}\" WHERE content LIKE :q";
        if ($volumeCol !== 'NULL') {
            $sql .= " ORDER BY CAST({$volumeCol} AS INTEGER), CAST(page AS INTEGER)";
        } else {
            $sql .= " ORDER BY CAST(page AS INTEGER)";
        }
        $sql .= " LIMIT :lim";

        $stmt = @$db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bindValue(':q', '%' . $query . '%', SQLITE3_TEXT);
        $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);

        $result = @$stmt->execute();
        if (!$result) return [];

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public static function getContent(string $code, int $volume, int $page): ?array {
        $db = self::getDb($code);
        if (!$db) return null;

        $info = self::getTableInfo($code);
        if (!$info || !$info['hasContent'] || !$info['hasPage']) return null;

        $table = $info['table'];
        $volumeCol = $info['volumeCol'] ?? null;
        $itemsCol = $info['itemsCol'] ?? 'NULL';
        $selectItems = $itemsCol !== 'NULL' ? $itemsCol : "''";

        if ($volumeCol === 'book') {
            $stmt = @$db->prepare("SELECT {$volumeCol} AS volume, page, {$selectItems} AS items, content FROM \"{$table}\" WHERE {$volumeCol} = :vol AND page = :page LIMIT 1");
            if (!$stmt) return null;
            $stmt->bindValue(':vol', $volume, SQLITE3_INTEGER);
            $stmt->bindValue(':page', $page, SQLITE3_INTEGER);
        } elseif ($volumeCol !== null) {
            $volVal = $info['isPadded'] ? str_pad((string)$volume, 2, '0', STR_PAD_LEFT) : (string)$volume;
            $pageVal = $info['isPadded'] ? str_pad((string)$page, 4, '0', STR_PAD_LEFT) : (string)$page;
            $stmt = @$db->prepare("SELECT {$volumeCol} AS volume, page, {$selectItems} AS items, content FROM \"{$table}\" WHERE {$volumeCol} = :vol AND page = :page LIMIT 1");
            if (!$stmt) return null;
            $stmt->bindValue(':vol', $volVal, SQLITE3_TEXT);
            $stmt->bindValue(':page', $pageVal, SQLITE3_TEXT);
        } else {
            $stmt = @$db->prepare("SELECT page, {$selectItems} AS items, content FROM \"{$table}\" WHERE page = :page LIMIT 1");
            if (!$stmt) return null;
            $stmt->bindValue(':page', (string)$page, SQLITE3_TEXT);
        }

        $result = @$stmt->execute();
        if (!$result) return null;
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }

    public static function getTotalPages(string $code, int $volume): int {
        $db = self::getDb($code);
        if (!$db) return 0;

        $info = self::getTableInfo($code);
        if (!$info) return 0;

        $table = $info['table'];
        $volumeCol = $info['volumeCol'] ?? null;
        if ($volumeCol === null) return 0;

        $volVal = $info['isPadded'] ? str_pad((string)$volume, 2, '0', STR_PAD_LEFT) : (string)$volume;
        $stmt = @$db->prepare("SELECT COUNT(*) FROM \"{$table}\" WHERE {$volumeCol} = :vol");
        if (!$stmt) return 0;
        $stmt->bindValue(':vol', $volVal, SQLITE3_TEXT);
        $result = @$stmt->execute();
        if (!$result) return 0;
        $row = $result->fetchArray(SQLITE3_NUM);
        return $row ? (int)$row[0] : 0;
    }

    public static function getVolumes(string $code): array {
        $db = self::getDb($code);
        if (!$db) return [];

        $info = self::getTableInfo($code);
        if (!$info) return [];

        $table = $info['table'];
        $volumeCol = $info['volumeCol'] ?? null;
        if ($volumeCol === null) return [];

        $sql = "SELECT DISTINCT {$volumeCol} FROM \"{$table}\" ORDER BY CAST({$volumeCol} AS INTEGER)";
        $result = @$db->query($sql);
        if (!$result) return [];

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
