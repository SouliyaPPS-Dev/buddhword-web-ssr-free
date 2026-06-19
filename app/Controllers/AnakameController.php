<?php
namespace App\Controllers;

class AnakameController {
    private const BASE_URL = 'http://anakame.com/page/1_Sutas';

    private static array $categories = [
        ['name' => 'Sutta', 'url' => 'http://anakame.com/page/1_Sutas/main/1_Sutta.htm', 'icon' => 'menu_book'],
        ['name' => 'Sutta Set', 'url' => 'http://anakame.com/page/4_Suta_Set/Main/Main_Set01.htm', 'icon' => 'library_books'],
        ['name' => 'Short Sutta', 'url' => 'http://anakame.com/page/4_Short_Sutta.htm', 'icon' => 'auto_stories'],
        ['name' => 'Person', 'url' => 'http://anakame.com/page/7_person.htm', 'icon' => 'person'],
        ['name' => 'Misc', 'url' => 'http://anakame.com/page/8_Misc.htm', 'icon' => 'category'],
    ];

    public function index() {
        $allItems = $this->fetchListings();
        $query = $_GET['q'] ?? '';
        $perPage = 20;

        if ($query) {
            $allItems = array_filter($allItems, function($item) use ($query) {
                return mb_stripos($item['title'], $query) !== false;
            });
            $allItems = array_values($allItems);
        }

        $items = array_slice($allItems, 0, $perPage);

        return view('pages.anakame.index', [
            'items' => array_values($items),
            'query' => $query,
            'categories' => self::$categories,
            'seo' => [
                'title' => 'Anakame (ພາສາໄທ) - ຄຳສອນພຸດທະ',
                'description' => 'Anakame ພາສາໄທ ຮວບຮວມພຣະສູດສຳຄັນ',
                'keywords' => 'Anakame, ພຣະສູດ, ພາສາໄທ',
            ]
        ]);
    }

    public function apiList() {
        $items = $this->fetchListings();
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
            'categories' => self::$categories,
            'total' => $total,
            'page' => $page,
            'hasMore' => ($offset + $perPage) < $total,
        ]);
    }

    public function apiContent() {
        $url = $_GET['url'] ?? '';
        if (!$url) {
            http_response_code(400);
            $this->json(['error' => 'Missing url parameter']);
            return;
        }

        $html = @file_get_contents($url, false, $this->getStreamContext());
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

        $url = $href;
        if (!str_starts_with($href, 'http')) {
            $url = self::BASE_URL . '/' . ltrim($href, '/');
        }
        $html = @file_get_contents($url, false, $this->getStreamContext());
        $content = '';
        $title = 'Anakame';

        if ($html !== false) {
            $content = $this->extractContent($html);
            $title = $this->extractTitle($html);
        }

        return view('pages.anakame.show', [
            'href' => $href,
            'content' => $content,
            'title' => trim($title),
            'seo' => [
                'title' => trim($title) . ' - Anakame',
                'description' => 'ອ່ານAnakame ພາສາໄທ',
            ]
        ]);
    }

    private function getStreamContext(int $timeout = 5) {
        return stream_context_create(['http' => ['timeout' => $timeout]]);
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
        return $m[1] ?? 'Anakame';
    }

    private function fetchListings(): array {
        $allItems = [];
        $seen = [];
        $siteUp = true;

        foreach (self::$categories as $catIndex => $category) {
            if (!$siteUp) break;
            $html = @file_get_contents($category['url'], false, $this->getStreamContext($catIndex === 0 ? 5 : 2));
            if ($html === false) {
                if ($catIndex === 0) $siteUp = false;
                continue;
            }

            preg_match_all('/<a\s+href="([^"]+)"[^>]*>(.*?)<\/a>/i', $html, $matches, PREG_SET_ORDER);

            foreach ($matches as $m) {
                $href = trim($m[1]);
                $inner = $m[2];

                if (str_contains($inner, '<img') || empty(trim(strip_tags($inner)))) continue;
                if ($href === '#' || str_starts_with($href, 'javascript')) continue;
                if (str_contains($href, 'index.htm') || str_contains($href, 'favicon')) continue;
                if (!str_contains($href, '.htm') && !str_contains($href, '.html')) continue;

                $title = trim(strip_tags($inner));
                $title = preg_replace('/\s+/', ' ', $title);
                if (mb_strlen($title) < 3) continue;

                $normalized = $this->resolveUrl($href, $category['url']);
                $key = $normalized . '|' . $title;
                if (isset($seen[$key])) continue;
                $seen[$key] = true;

                $allItems[] = [
                    'href' => $normalized,
                    'title' => $title,
                    'category' => $category['name'],
                    'categoryIndex' => $catIndex,
                ];
            }
        }

        return $allItems;
    }

    private function resolveUrl(string $href, string $baseUrl): string {
        if (str_starts_with($href, 'http')) return $href;
        if (str_starts_with($href, '/')) {
            $parsed = parse_url($baseUrl);
            return ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? 'anakame.com') . $href;
        }
        $base = dirname($baseUrl);
        return $base . '/' . ltrim($href, '/');
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
