<?php
namespace App\Controllers;

class AnakameController {
    private const BASE_URL = 'http://anakame.com/page/1_Sutas/main';

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
        $number = $_GET['number'] ?? '';

        if ($number) {
            $url = self::BASE_URL . '/' . $number . '.htm';
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

        preg_match('/<div class="main">(.*?)<\/div>/s', $html, $matches);
        $content = $matches[1] ?? '';

        preg_match('/<title>(.*?)<\/title>/s', $html, $titleMatches);
        $title = $titleMatches[1] ?? 'ອານາຄົມສູດ';

        $this->json([
            'title' => trim($title),
            'content' => trim($content),
            'url' => $url,
        ]);
    }

    public function read() {
        $number = $_GET['number'] ?? '';
        if (!$number) {
            header('Location: ' . url('/anakame'));
            exit;
        }

        $url = self::BASE_URL . '/' . $number . '.htm';
        $html = @file_get_contents($url);
        $content = '';
        $title = 'ອານາຄົມສູດ';

        if ($html !== false) {
            preg_match('/<div class="main">(.*?)<\/div>/s', $html, $matches);
            $content = $matches[1] ?? '';
            preg_match('/<title>(.*?)<\/title>/s', $html, $titleMatches);
            $title = $titleMatches[1] ?? 'ອານາຄົມສູດ';
        }

        return view('pages.anakame.show', [
            'number' => $number,
            'content' => $content,
            'title' => trim($title),
            'seo' => [
                'title' => trim($title) . ' - ອານາຄົມສູດ',
                'description' => 'ອ່ານອານາຄົມສູດ ພາສາໄທ',
            ]
        ]);
    }

    private function fetchListing(): array {
        $html = @file_get_contents(self::BASE_URL . '/1_Sutta_number.htm');
        if ($html === false) return [];

        $items = [];
        preg_match_all('/<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $href = $m[1];
            $title = trim(strip_tags($m[2]));
            if (empty($title) || $title === '') continue;

            preg_match('/(\d+)_/', $href, $numMatch);
            $number = $numMatch[1] ?? '';

            $items[] = [
                'number' => $number,
                'title' => $title,
                'url' => self::BASE_URL . '/' . $href,
            ];
        }

        return $items;
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
