<?php
namespace App\Models;

require_once __DIR__ . '/../Helpers/cache.php';

class Sutra {
    public static function getAll($refresh = false) {
        $sutraData = self::fetchSutraApi($refresh);
        $buddhaNatureData = self::fetchBuddhaNatureJson();
        
        return array_merge($sutraData, $buddhaNatureData);
    }

    private static function fetchSutraApi($refresh = false) {
        $cacheFile = 'sutra_api.json';
        $cacheTime = 86400; // 24 hours

        if (!$refresh) {
            $json = readCache($cacheFile, $cacheTime);
            if ($json !== null) {
                $data = json_decode($json, true);
                if (isset($data['values']) && !empty($data['values'])) {
                    return self::transformData($data);
                }
            }
        }

        $url = $_ENV['SUTRA_API_URL'] ?? getenv('SUTRA_API_URL') ?: null;
        if (!$url) {
            $json = readCache($cacheFile, $cacheTime * 365);
            if ($json !== null) {
                $data = json_decode($json, true);
                if (isset($data['values']) && !empty($data['values'])) {
                    return self::transformData($data);
                }
            }
            return [];
        }

        $json = httpGet($url);
        if ($json !== null) {
            writeCache($cacheFile, $json);
            $data = json_decode($json, true);
            if (isset($data['values']) && !empty($data['values'])) {
                return self::transformData($data);
            }
        }

        $json = readCache($cacheFile, $cacheTime * 365);
        if ($json !== null) {
            $data = json_decode($json, true);
            if (isset($data['values']) && !empty($data['values'])) {
                return self::transformData($data);
            }
        }

        if ($refresh) {
            throw new \Exception("ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບ API ໄດ້");
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
                $rowObject[$header] = trim($row[$index] ?? "");
            }
            if (!isset($rowObject['ສຽງ'])) {
                $rowObject['ສຽງ'] = "";
            }
            
            if (!empty(array_filter($rowObject))) {
                $transformed[] = $rowObject;
            }
        }

        return $transformed;
    }

    private static function fetchBuddhaNatureJson() {
        $filePath = __DIR__ . '/../../public/assets/buddha-nature.json';
        if (!file_exists($filePath)) return [];

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        $transformed = [];
        foreach ($data as $item) {
            $categoryName = "";
            switch ($item['category']) {
                case "627515988b61fc33c0d0ea97":
                    $categoryName = "ທໍາໃນເບື້ອງຕົ້ນ";
                    break;
                case "627515918b61fc33c0d0ea94":
                    $categoryName = "ທໍາໃນທ່າມກາງ";
                    break;
                case "627515888b61fc33c0d0ea91":
                    $categoryName = "ທໍາໃນທີສຸດ";
                    break;
                default:
                    $categoryName = $item['category'];
                    break;
            }

            $transformed[] = [
                'ID' => $item['_id'],
                'ຊື່ພຣະສູດ' => $item['title'],
                'ພຣະສູດ' => $item['content'],
                'ຮູບ' => $item['thumbnail'] ?? "",
                'ໝວດທັມ' => $categoryName,
                'ສຽງ' => "",
            ];
        }

        return $transformed;
    }
}
