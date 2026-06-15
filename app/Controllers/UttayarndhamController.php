<?php
namespace App\Controllers;

class UttayarndhamController {
    private const BASE_URL = 'https://uttayarndham.org';

    public function index() {
        $items = $this->fetchPage(0);
        $query = $_GET['q'] ?? '';

        if ($query) {
            $items = array_filter($items, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
        }

        return view('pages.uttayarndham.index', [
            'items' => $items,
            'query' => $query,
            'seo' => [
                'title' => 'ອຸດທະຍານທັມ (ທັມມະ) - ຄຳສອນພຸດທະ',
                'description' => 'ອຸດທະຍານທັມ ຮວບຮວມບົດຄວາມທັມມະ',
                'keywords' => 'ອຸດທະຍານທັມ, ທັມມະ, ທັມ, ຄຳສອນພຸດທະ',
            ]
        ]);
    }

    public function apiList() {
        $page = max(0, intval($_GET['page'] ?? 0));
        $items = $this->fetchPage($page);

        $nextItems = $this->fetchPage($page + 1);
        $hasMore = count($nextItems) > 0;

        $this->json([
            'items' => $items,
            'page' => $page,
            'total' => ($page * 20) + count($items),
            'hasMore' => $hasMore,
        ]);
    }

    public function apiContent() {
        $url = $_GET['url'] ?? '';
        if (!$url) {
            http_response_code(400);
            $this->json(['error' => 'Missing url parameter']);
            return;
        }

        $fullUrl = strpos($url, 'http') === 0 ? $url : self::BASE_URL . $url;
        $html = @file_get_contents($fullUrl);
        if ($html === false) {
            http_response_code(404);
            $this->json(['error' => 'Content not found']);
            return;
        }

        preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $titleMatches);
        $title = $titleMatches[1] ?? '';

        preg_match('/<article[^>]*>(.*?)<\/article>/s', $html, $articleMatches);
        $content = $articleMatches[1] ?? '';

        if (empty($content)) {
            preg_match('/<div class="content"[^>]*>(.*?)<\/div>/s', $html, $divMatches);
            $content = $divMatches[1] ?? '';
        }

        $content = strip_tags($content, '<p><br><b><i><u><h2><h3><ul><ol><li><strong><em><a><img><blockquote><table><tr><td><th>');
        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*"((?!https?:|\/\/))([^"]+)"/i', '<img $1src="' . self::BASE_URL . '/$3"', $content);
        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*\'((?!https?:|\/\/))([^\']+)\'/i', '<img $1src=\'' . self::BASE_URL . '/$3\'', $content);

        $this->json([
            'title' => trim(strip_tags($title)),
            'content' => trim($content),
        ]);
    }

    public function read() {
        $url = $_GET['url'] ?? '';
        if (!$url) {
            header('Location: ' . url('/uttayarndham'));
            exit;
        }

        $fullUrl = strpos($url, 'http') === 0 ? $url : self::BASE_URL . $url;
        $html = @file_get_contents($fullUrl);
        $title = 'ອຸດທະຍານທັມ';
        $content = '';

        if ($html !== false) {
            preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $titleMatches);
            $title = $titleMatches[1] ?? '';

            preg_match('/<article[^>]*>(.*?)<\/article>/s', $html, $articleMatches);
            $content = $articleMatches[1] ?? '';

            if (empty($content)) {
                preg_match('/<div class="content"[^>]*>(.*?)<\/div>/s', $html, $divMatches);
                $content = $divMatches[1] ?? '';
            }
        }

        $prevUrl = null;
        $nextUrl = null;
        for ($page = 0; $page <= 5; $page++) {
            $pageItems = $this->fetchPage($page);
            if (empty($pageItems)) break;
            foreach ($pageItems as $idx => $item) {
                if ($item['url'] === $url || $item['url'] === '/' . ltrim($url, '/')) {
                    if ($idx > 0) $prevUrl = $pageItems[$idx - 1]['url'];
                    if ($idx < count($pageItems) - 1) $nextUrl = $pageItems[$idx + 1]['url'];
                    break 2;
                }
            }
        }

        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*"((?!https?:|\/\/))([^"]+)"/i', '<img $1src="' . self::BASE_URL . '/$3"', $content);
        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*\'((?!https?:|\/\/))([^\']+)\'/i', '<img $1src=\'' . self::BASE_URL . '/$3\'', $content);

        return view('pages.uttayarndham.show', [
            'title' => trim(strip_tags($title)),
            'content' => $content,
            'url' => $url,
            'prevUrl' => $prevUrl,
            'nextUrl' => $nextUrl,
            'seo' => [
                'title' => trim(strip_tags($title)) . ' - ອຸດທະຍານທັມ',
                'description' => 'ອ່ານບົດຄວາມທັມມະ',
            ]
        ]);
    }

    private function fetchPage(int $page): array {
        $url = $page === 0
            ? self::BASE_URL . '/dhamma-sharing'
            : self::BASE_URL . '/dhamma-sharing?page=' . $page;

        $html = @file_get_contents($url);
        if ($html === false) return [];

        $items = [];

        preg_match_all('/<h4><a\s+href="\s+(\/[^"]+)"[^>]*>\s*([^<]+?)\s*<\/a><\/h4>/s', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $items[] = [
                'title' => trim($m[2]),
                'url' => trim($m[1]),
            ];
        }

        return $items;
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
