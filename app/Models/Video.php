<?php
namespace App\Models;

require_once __DIR__ . '/../Helpers/cache.php';

class Video {
    public static function getAll($refresh = false) {
        $cacheFile = 'video_api.json';
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

        $url = $_ENV['VIDEO_API_URL'] ?? getenv('VIDEO_API_URL') ?: '';
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
                $link = $rowObject['link'] ?? '';
                preg_match('/(?:youtu\.be\/|youtube\.com\/(?:.*v=|.*\/|.*embed\/|.*shorts\/))([\w-]+)/', $link, $m);
                $rowObject['_ytId'] = $m[1] ?? '';
                $rowObject['_thumbnail'] = '';
                if ($rowObject['_ytId']) {
                    $rowObject['_thumbnail'] = "https://img.youtube.com/vi/{$rowObject['_ytId']}/hqdefault.jpg";
                } elseif (strpos($link, 'drive.google.com') !== false) {
                    preg_match('/(?:drive\.google\.com\/(?:.*\/d\/|file\/d\/))([a-zA-Z0-9_-]+)/', $link, $dm);
                    $fileId = $dm[1] ?? '';
                    if ($fileId) {
                        $rowObject['_thumbnail'] = "https://lh3.googleusercontent.com/d/{$fileId}=s320?authuser=0";
                    }
                }
                $transformed[] = $rowObject;
            }
        }

        return array_reverse($transformed);
    }
}
