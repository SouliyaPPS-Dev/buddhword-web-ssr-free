<?php
namespace App\Controllers;

class AnakameController {
    private const BASE_URL = 'http://anakame.com/page/1_Sutas';

    public function index() {
        $items = $this->fetchListing();
        $query = $_GET['q'] ?? '';

        if ($query) {
            $items = array_filter($items, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
        }

        return view('pages.anakame.index', [
            'items' => array_values($items),
            'query' => $query,
            'seo' => [
                'title' => 'ອານາຄົມສູດ (ພາສາໄທ) - ຄຳສອນພຸດທະ',
                'description' => 'ອານາຄົມສູດ ພາສາໄທ ຮວບຮວມພຣະສູດສຳຄັນ',
                'keywords' => 'ອານາຄົມສູດ, ພຣະສູດ, ພາສາໄທ',
            ]
        ]);
    }

    public function apiList() {
        $items = $this->fetchListing();
        $query = $_GET['q'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;

        if ($query) {
            $items = array_filter($items, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
            $items = array_values($items);
        }

        $total = count($items);
        $offset = ($page - 1) * $perPage;
        $paged = array_slice($items, $offset, $perPage);

        $this->json([
            'items' => $paged,
            'total' => $total,
            'page' => $page,
            'hasMore' => ($offset + $perPage) < $total,
        ]);
    }

    public function apiContent() {
        $url = $_GET['url'] ?? '';
        $path = $_GET['path'] ?? '';

        if ($path) {
            $url = self::BASE_URL . '/' . ltrim($path, '/');
        }

        if (!$url) {
            http_response_code(400);
            $this->json(['error' => 'Missing url parameter']);
            return;
        }

        $html = @file_get_contents($url);
        if ($html === false) {
            http_response_code(404);
            $this->json(['error' => 'Content not found']);
            return;
        }

        $content = $this->extractContent($html);
        $title = $this->extractTitle($html);

        $this->json([
            'title' => trim($title),
            'content' => trim($content),
            'url' => $url,
        ]);
    }

    public function read() {
        $href = $_GET['href'] ?? '';
        if (!$href) {
            header('Location: ' . url('/anakame'));
            exit;
        }

        $url = self::BASE_URL . '/' . ltrim($href, '/');
        $html = @file_get_contents($url);
        $content = '';
        $title = 'ອານາຄົມສູດ';

        if ($html !== false) {
            $content = $this->extractContent($html);
            $title = $this->extractTitle($html);
        }

        $allItems = $this->fetchListing();
        $allHrefs = array_column($allItems, 'href');
        $currentIndex = array_search($href, $allHrefs);
        $prevHref = $currentIndex !== false && $currentIndex > 0 ? $allItems[$currentIndex - 1]['href'] : null;
        $nextHref = $currentIndex !== false && $currentIndex < count($allItems) - 1 ? $allItems[$currentIndex + 1]['href'] : null;

        return view('pages.anakame.show', [
            'href' => $href,
            'content' => $content,
            'title' => trim($title),
            'prevHref' => $prevHref,
            'nextHref' => $nextHref,
            'seo' => [
                'title' => trim($title) . ' - ອານາຄົມສູດ',
                'description' => 'ອ່ານອານາຄົມສູດ ພາສາໄທ',
            ]
        ]);
    }

    private function extractContent($html): string {
        $html = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/(p|div|tr|h[1-6])>/i', "\n", $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        return trim($text);
    }

    private function extractTitle($html): string {
        preg_match('/<title>(.*?)<\/title>/si', $html, $m);
        return $m[1] ?? 'ອານາຄົມສູດ';
    }

    private function fetchListing(): array {
        $html = @file_get_contents(self::BASE_URL . '/main/1_Sutta_number.htm');
        if ($html === false) return [];

        $items = [];
        preg_match_all('/<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>/i', $html, $matches, PREG_SET_ORDER);
        $seen = [];

        foreach ($matches as $m) {
            $href = trim($m[1]);
            $title = trim(strip_tags($m[2]));
            if (empty($title) || $title === '' || !str_contains($href, '.htm')) continue;

            $normalized = str_replace('../', '', $href);
            $normalized = preg_replace('/#.*$/', '', $normalized);
            $key = $normalized . '|' . $title;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $items[] = [
                'href' => $normalized,
                'title' => $title,
            ];
        }

        return $items;
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
