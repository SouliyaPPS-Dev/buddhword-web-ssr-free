<?php
namespace App\Models;

require_once __DIR__ . '/../Helpers/cache.php';

class Calendar {
    public static function getAll($refresh = false) {
        $cacheFile = 'calendar_api.json';
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

        $url = $_ENV['CALENDAR_API_URL'] ?? getenv('CALENDAR_API_URL') ?: '';
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
        foreach ($rows as $index => $row) {
            $rowObject = [];
            foreach ($headers as $colIndex => $header) {
                $rowObject[$header] = $row[$colIndex] ?? "";
            }

            if (!isset($rowObject['ID']) || empty($rowObject['ID'])) {
                $rowObject['ID'] = (string)($index + 1);
            }

            $rowObject['startDateISO'] = self::convertToISO($rowObject['startDateTime'] ?? '');
            $rowObject['endDateISO'] = self::convertToISO($rowObject['endDateTime'] ?? '');

            if (!empty(array_filter($rowObject))) {
                $transformed[] = $rowObject;
            }
        }

        return $transformed;
    }

    private static function convertToISO($dateStr) {
        if (empty($dateStr)) return '';

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})/', $dateStr, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $dateStr, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
        }

        return $dateStr;
    }

    public static function getById($id) {
        $events = self::getAll();
        foreach ($events as $event) {
            if ($event['ID'] == $id) {
                return $event;
            }
        }
        return null;
    }
}
