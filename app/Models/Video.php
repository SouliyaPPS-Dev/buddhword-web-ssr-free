<?php
namespace App\Models;

class Video {
    public static function getAll($refresh = false) {
        $cacheFile = __DIR__ . '/../../storage/cache/video_api.json';
        $cacheTime = 86400; // 24 hours

        if (!$refresh && file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
            $json = file_get_contents($cacheFile);
        } else {
            $url = $_ENV['VIDEO_API_URL'] ?? getenv('VIDEO_API_URL') ?: '';
            if (!$url) {
                if (file_exists($cacheFile)) {
                    $json = file_get_contents($cacheFile);
                } else {
                    return [];
                }
            } else {
                $json = httpGet($url);
                if ($json !== null) {
                    if (!is_dir(dirname($cacheFile))) mkdir(dirname($cacheFile), 0777, true);
                    file_put_contents($cacheFile, $json);
                } elseif (file_exists($cacheFile)) {
                    $json = file_get_contents($cacheFile);
                } else {
                    return [];
                }
            }
        }

        $data = json_decode($json, true);
        if (!isset($data['values'])) return [];

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

        return $transformed;
    }
}
