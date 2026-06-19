<?php
namespace App\Controllers;

class UttayarndhamController {
    private const BASE_URL = 'https://uttayarndham.org';

    public function index() {
        $items = $this->fetchTags(0);
        $query = $_GET['q'] ?? '';

        if ($query) {
            $items = array_filter($items, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
        }

        return view('pages.uttayarndham.index', [
            'items' => array_values($items),
            'query' => $query,
            'seo' => [
                'title' => 'Uttayarndham (ທັມມະ) - ຄຳສອນພຸດທະ',
                'description' => 'Uttayarndham ຮວບຮວມບົດຄວາມທັມມະ',
                'keywords' => 'uttayarndham, ທັມມະ, ທັມ, ຄຳສອນພຸດທະ',
            ]
        ]);
    }

    public function apiList() {
        $page = max(0, intval($_GET['page'] ?? 0));
        $items = $this->fetchTags($page);

        $html = @file_get_contents(
            self::BASE_URL . '/keyword/tags' . ($page > 0 ? '?page=' . $page : ''),
            false,
            $this->getStreamContext()
        );
        $hasMore = $html !== false && preg_match('/rel="next"/', $html) === 1;

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
        $html = @file_get_contents($fullUrl, false, $this->getStreamContext());
        if ($html === false) {
            http_response_code(404);
            $this->json(['error' => 'Content not found']);
            return;
        }

        preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $titleMatches);
        $title = $titleMatches[1] ?? '';

        $content = '';
        preg_match('/<article[^>]*>(.*?)<\/article>/s', $html, $articleMatches);
        if (!empty($articleMatches[1])) {
            $content = $articleMatches[1];
        }

        if (empty($content)) {
            preg_match('/<div class="content"[^>]*>(.*?)<\/div>/s', $html, $divMatches);
            $content = $divMatches[1] ?? '';
        }

        if (empty($content)) {
            preg_match('/<div class="view-content[^>]*>(.*?)<\/div>/s', $html, $viewMatches);
            $content = $viewMatches[1] ?? '';
        }

        if (empty($content)) {
            preg_match('/<div id="content"[^>]*>(.*?)<\/div>/s', $html, $idMatches);
            $content = $idMatches[1] ?? '';
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
        $html = @file_get_contents($fullUrl, false, $this->getStreamContext());
        $title = 'Uttayarndham';
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

            if (empty($content)) {
                preg_match('/<div class="view-content[^>]*>(.*?)<\/div>/s', $html, $viewMatches);
                $content = $viewMatches[1] ?? '';
            }

            if (empty($content)) {
                preg_match('/<div id="content"[^>]*>(.*?)<\/div>/s', $html, $idMatches);
                $content = $idMatches[1] ?? '';
            }
        }

        $content = preg_replace('/<[^>]*class="[^"]*\brdf-meta\b[^"]*"[^>]*>.*?<\/[^>]+>/is', '', $content);
        $content = preg_replace('/<div[^>]*class="[^"]*\bfield-name-title\b[^"]*"[^>]*>.*?<\/div>/is', '', $content);
        $content = strip_tags($content, '<p><br><b><i><u><h2><h3><ul><ol><li><strong><em><a><img><blockquote><table><tr><td><th>');
        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*"((?!https?:|\/\/))([^"]+)"/i', '<img $1src="' . self::BASE_URL . '/$3"', $content);
        $content = preg_replace('/<img\s+([^>]*?)src\s*=\s*\'((?!https?:|\/\/))([^\']+)\'/i', '<img $1src=\'' . self::BASE_URL . '/$3\'', $content);

        return view('pages.uttayarndham.show', [
            'title' => trim(strip_tags($title)),
            'content' => $content,
            'url' => $url,
            'seo' => [
                'title' => trim(strip_tags($title)) . ' - Uttayarndham',
                'description' => 'ອ່ານບົດຄວາມທັມມະ',
            ]
        ]);
    }

    public function tag() {
        $url = $_GET['url'] ?? '';
        if (!$url) {
            header('Location: ' . url('/uttayarndham'));
            exit;
        }

        $fullUrl = strpos($url, 'http') === 0 ? $url : self::BASE_URL . $url;
        $html = @file_get_contents($fullUrl, false, $this->getStreamContext());
        $tagTitle = 'Uttayarndham';
        $items = [];

        if ($html !== false) {
            preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $h1);
            if (!empty($h1[1])) {
                $tagTitle = trim(strip_tags($h1[1]));
            }
            if (empty($tagTitle) || $tagTitle === 'Uttayarndham') {
                preg_match('/<h3[^>]*>(.*?)<\/h3>/s', $html, $h3);
                $tagTitle = !empty($h3[1]) ? trim(strip_tags($h3[1])) : 'Uttayarndham';
            }

            $items = $this->parseTagItems($html);
        }

        $query = $_GET['q'] ?? '';
        if ($query) {
            $items = array_filter($items, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
            $items = array_values($items);
        }

        return view('pages.uttayarndham.tag', [
            'tagTitle' => $tagTitle,
            'items' => array_values($items),
            'query' => $query,
            'url' => $url,
            'seo' => [
                'title' => $tagTitle . ' - Uttayarndham',
                'description' => 'ລາຍການໃນ ' . $tagTitle,
            ]
        ]);
    }

    private function parseTagItems(string $html): array {
        $items = [];
        $seen = [];

        preg_match('/<div class="view-content[^>]*>(.*?)<\/div>/s', $html, $viewMatch);
        $listingHtml = $viewMatch[1] ?? $html;

        preg_match_all('/<a\s+href="\s*([^"]+)"[^>]*>\s*([^<]+?)\s*<\/a>/s', $listingHtml, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $title = trim($m[2]);
            $href = trim($m[1]);
            if (empty($title) || $title === $href) continue;
            if (str_contains($href, '#') || str_starts_with($href, 'javascript')) continue;
            $title = preg_replace('/\s+/', ' ', $title);
            if (!empty($title) && !isset($seen[$href])) {
                $seen[$href] = true;
                $items[] = [
                    'title' => $title,
                    'url' => $href,
                ];
            }
        }

        if (empty($items)) {
            $text = strip_tags($html);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', $text);
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = trim($line);
                if (mb_strlen($line) >= 10 && !isset($seen[$line])) {
                    $seen[$line] = true;
                    $items[] = [
                        'title' => mb_strlen($line) > 80 ? mb_substr($line, 0, 80) . '...' : $line,
                        'url' => '',
                    ];
                }
            }
        }

        return $items;
    }

    private function getStreamContext() {
        return stream_context_create(['http' => ['timeout' => 5]]);
    }

    private function fetchTags(int $page): array {
        $url = self::BASE_URL . '/keyword/tags' . ($page > 0 ? '?page=' . $page : '');

        $html = @file_get_contents($url, false, $this->getStreamContext());
        if ($html === false) return [];

        $items = [];
        $seen = [];

        preg_match_all('/<a\s+href="(\/taxonomy\/term\/\d+)"[^>]*>\s*([^<]+?)\s*<\/a>/s', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $name = trim($m[2]);
            $href = trim($m[1]);
            if (!empty($name) && !isset($seen[$href])) {
                $seen[$href] = true;
                $items[] = [
                    'title' => $name,
                    'url' => $href,
                ];
            }
        }

        return $items;
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
