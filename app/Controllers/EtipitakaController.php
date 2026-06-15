<?php
namespace App\Controllers;

use App\Services\EtipitakaService;

class EtipitakaController {
    public function index() {
        $categories = EtipitakaService::getCategories();
        $currentCode = $_GET['code'] ?? 'thai';
        $query = $_GET['q'] ?? '';

        $results = [];
        $volumes = [];
        $groupedResults = [];

        if ($query && mb_strlen($query) >= 2) {
            try {
                $results = EtipitakaService::search($currentCode, $query);
            } catch (\Throwable $e) {
                error_log('Etipitaka index search error: ' . $e->getMessage());
                $results = [];
            }
            foreach ($results as $r) {
                $vol = (int)$r['volume'];
                if (!isset($groupedResults[$vol])) $groupedResults[$vol] = [];
                $groupedResults[$vol][] = $r;
            }
        }

        ksort($groupedResults);

        $seoTitle = 'E-Tipitaka - ' . EtipitakaService::getLabel($currentCode);

        return view('pages.tipitaka.index', [
            'categories' => $categories,
            'currentCode' => $currentCode,
            'query' => $query,
            'results' => $results,
            'groupedResults' => $groupedResults,
            'currentLabel' => EtipitakaService::getLabel($currentCode),
            'seo' => [
                'title' => $seoTitle . ' - ຄຳສອນພຸດທະ',
                'description' => 'ຄົ້ນຫາພຣະໄຕຣປິດກ ຫຼາຍສະບັບພາສາຕ່າງໆ',
                'keywords' => 'tipitaka, tripitaka, ພຣະໄຕຣປິດກ, ພຸດທະສາສະໜາ',
            ]
        ]);
    }

    public function search() {
        $code = $_GET['code'] ?? 'thai';
        $query = $_GET['q'] ?? '';

        if (mb_strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        try {
            $results = EtipitakaService::search($code, $query);
        } catch (\Throwable $e) {
            error_log('Etipitaka search error: ' . $e->getMessage());
            $this->json(['results' => [], 'error' => 'Search failed']);
            return;
        }
        $items = [];
        foreach ($results as $r) {
            $volume = (int)$r['volume'];
            $page = (int)$r['page'];
            $content = $r['content'];
            $cleaned = preg_replace('/\s+/', ' ', str_replace("\t", ' ', $content));
            $cleaned = trim($cleaned);
            $idx = mb_stripos($cleaned, $query);
            $excerpt = '';
            if ($idx !== false) {
                $start = max(0, $idx - 60);
                $end = min(mb_strlen($cleaned), $idx + mb_strlen($query) + 90);
                $prefix = $start > 0 ? '...' : '';
                $suffix = $end < mb_strlen($cleaned) ? '...' : '';
                $excerpt = $prefix . mb_substr($cleaned, $start, $end - $start) . $suffix;
            } else {
                $excerpt = mb_strlen($cleaned) > 150 ? mb_substr($cleaned, 0, 150) . '...' : $cleaned;
            }
            $items[] = [
                'volume' => $volume,
                'page' => $page,
                'items' => $r['items'],
                'title' => 'ເຫຼັ້ມທີ່ ' . $volume . ' ຫນ້າ ' . $page,
                'excerpt' => $excerpt,
            ];
        }

        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['volume']][] = $item;
        }
        ksort($grouped);

        $this->json([
            'results' => $items,
            'grouped' => $grouped,
            'total' => count($items),
        ]);
    }

    public function show($code, $volume, $page) {
        $volume = (int)$volume;
        $page = (int)$page;

        if (!EtipitakaService::isAvailable($code)) {
            http_response_code(404);
            echo 'Edition not found';
            return;
        }

        $content = EtipitakaService::getContent($code, $volume, $page);
        if (!$content) {
            http_response_code(404);
            echo 'Content not found';
            return;
        }

        $totalPages = EtipitakaService::getTotalPages($code, $volume);
        $label = EtipitakaService::getLabel($code);
        $prevPage = $page > 1 ? $page - 1 : null;
        $nextPage = $page < $totalPages ? $page + 1 : null;
        $volumes = EtipitakaService::getVolumes($code);

        return view('pages.tipitaka.show', [
            'content' => $content,
            'code' => $code,
            'label' => $label,
            'volume' => $volume,
            'page' => $page,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
            'totalPages' => $totalPages,
            'volumes' => $volumes,
            'seo' => [
                'title' => 'E-Tipitaka - ເຫຼັ້ມທີ່ ' . $volume . ' ຫນ້າ ' . $page . ' (' . $label . ')',
                'description' => 'ພຣະໄຕຣປິດກ ເຫຼັ້ມທີ່ ' . $volume . ' ຫນ້າ ' . $page,
            ]
        ]);
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
