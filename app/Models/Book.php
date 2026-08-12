<?php
namespace App\Models;

require_once __DIR__ . '/../Helpers/cache.php';

class Book {
    public static function getAll($refresh = false) {
        $cacheFile = 'book_api.json';
        $cacheTime = 86400; // 24 hours

        if (!$refresh) {
            $json = readCache($cacheFile, $cacheTime);
            if ($json !== null) {
                $data = json_decode($json, true);
                if (isset($data['values'])) {
                    return self::transformData($data);
                }
            }
        }

        $url = $_ENV['BOOK_API_URL'] ?? getenv('BOOK_API_URL') ?: '';
        if (!$url) {
            $json = readCache($cacheFile, $cacheTime * 365);
            if ($json !== null) {
                $data = json_decode($json, true);
                if (isset($data['values'])) {
                    return self::transformData($data);
                }
            }
            return [];
        }

        $json = httpGet($url);
        if ($json !== null) {
            writeCache($cacheFile, $json);
            $data = json_decode($json, true);
            if (isset($data['values'])) {
                return self::transformData($data);
            }
        }

        $json = readCache($cacheFile, $cacheTime * 365);
        if ($json !== null) {
            $data = json_decode($json, true);
            if (isset($data['values'])) {
                return self::transformData($data);
            }
        }

        return [];
    }

    private static function transformData($data) {
        $headers = array_shift($data['values']);
        $rows = $data['values'];

        $transformed = [];
        foreach ($rows as $row) {
            $rowObject = [];
            foreach ($headers as $index => $header) {
                $rowObject[$header] = $row[$index] ?? "";
            }
            if (!empty(array_filter($rowObject))) {
                $transformed[] = $rowObject;
            }
        }

        return $transformed;
    }
}
