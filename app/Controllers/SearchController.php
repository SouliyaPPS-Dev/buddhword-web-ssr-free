<?php
namespace App\Controllers;

use App\Models\Sutra;
use App\Models\Book;
use App\Models\Video;
use App\Models\Calendar;
use App\Models\PdfBook;
use App\Services\EtipitakaService;

class SearchController {
    private const MAX_PER_TYPE = 20;
    private const MAX_TOTAL = 100;

    private const ANAKAME_CATEGORIES = [
        'Sutta'       => 'http://anakame.com/page/1_Sutas/main/1_Sutta.htm',
        'Sutta Set'   => 'http://anakame.com/page/4_Suta_Set/Main/Main_Set01.htm',
        'Short Sutta' => 'http://anakame.com/page/4_Short_Sutta.htm',
        'Person'      => 'http://anakame.com/page/7_person.htm',
        'Misc'        => 'http://anakame.com/page/8_Misc.htm',
    ];

    private const UTTAYARNDHAM_BASE = 'https://uttayarndham.org';

    private static function getStreamContext(int $timeout = 5) {
        return stream_context_create(['http' => ['timeout' => $timeout]]);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        $q = mb_strtolower(trim($query));

        if (mb_strlen($q) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        $results = [];
        $countByType = [
            'sutra' => 0,
            'etipitaka' => 0,
            'book-page' => 0,
            'book' => 0,
            'video' => 0,
            'calendar' => 0,
            'anakame' => 0,
            'uttayarndham' => 0,
        ];

        // 1. Search Sutras
        $sutras = Sutra::getAll();
        foreach ($sutras as $item) {
            if (($countByType['sutra'] ?? 0) >= self::MAX_PER_TYPE) break;
            if ($this->match($q, $item['ຊື່ພຣະສູດ'] ?? '') || $this->match($q, $item['ພຣະສູດ'] ?? '')) {
                $results[] = [
                    'type' => 'sutra',
                    'title' => $item['ຊື່ພຣະສູດ'] ?? '',
                    'detail' => mb_substr(strip_tags($item['ພຣະສູດ'] ?? ''), 0, 150) . '...',
                    'url' => url('/sutra/details/' . ($item['ID'] ?? '')),
                    'category' => $item['ໝວດທັມ'] ?? ''
                ];
                $countByType['sutra'] = ($countByType['sutra'] ?? 0) + 1;
            }
        }

        // 2. Search E-Tipitaka (all databases)
        $etipitakaCodes = EtipitakaService::getCategories();
        foreach ($etipitakaCodes as $cat) {
            $code = $cat['code'];
            if (($countByType['etipitaka'] ?? 0) >= self::MAX_PER_TYPE) break;
            try {
                $etResults = EtipitakaService::search($code, $q, 5);
                foreach ($etResults as $er) {
                    if (($countByType['etipitaka'] ?? 0) >= self::MAX_PER_TYPE) break;
                    $volume = (int)$er['volume'];
                    $page = (int)$er['page'];
                    $content = trim(preg_replace('/\s+/', ' ', $er['content'] ?? ''));
                    $detail = mb_strlen($content) > 150 ? mb_substr($content, 0, 150) . '...' : $content;
                    $label = $cat['label'];
                    $results[] = [
                        'type' => 'etipitaka',
                        'title' => 'ເຫຼັ້ມທີ່ ' . $volume . ' ຫນ້າ ' . $page . ' (' . $label . ')',
                        'detail' => $detail,
                        'url' => url('/etipitaka/' . $code . '/' . $volume . '/' . $page),
                        'category' => $label,
                    ];
                    $countByType['etipitaka'] = ($countByType['etipitaka'] ?? 0) + 1;
                }
            } catch (\Throwable $e) {
                error_log('Search etipitaka error (' . $code . '): ' . $e->getMessage());
            }
        }

        // 3. Search PDF/DOCX Book Text
        $pdfBooks = PdfBook::getBooks();
        foreach ($pdfBooks as $book) {
            if (($countByType['book-page'] ?? 0) >= self::MAX_PER_TYPE) break;
            $slug = $book['slug'];
            $pageResults = PdfBook::search($slug, $q);
            foreach ($pageResults as $pr) {
                if (($countByType['book-page'] ?? 0) >= self::MAX_PER_TYPE) break;
                $results[] = [
                    'type' => 'book-page',
                    'title' => $book['title'] . ' - ໜ້າ ' . $pr['page'],
                    'detail' => $pr['snippet'],
                    'url' => url('/search-books/' . $slug . '/page/' . $pr['page']),
                    'category' => $book['title'],
                    'matches' => $pr['matches']
                ];
                $countByType['book-page'] = ($countByType['book-page'] ?? 0) + 1;
            }
        }

        // 4. Search Books
        $books = Book::getAll();
        foreach ($books as $item) {
            if (($countByType['book'] ?? 0) >= self::MAX_PER_TYPE) break;
            if ($this->match($q, $item['ຊື່'] ?? '') || $this->match($q, $item['ໝວດຟາຍ'] ?? '') || $this->match($q, $item['ໝວດທັມ'] ?? '')) {
                $results[] = [
                    'type' => 'book',
                    'title' => $item['ຊື່'] ?? '',
                    'detail' => ($item['ໝວດຟາຍ'] ?? '') . ' | ' . ($item['ໝວດທັມ'] ?? ''),
                    'url' => url('/book/view/' . ($item['ID'] ?? '')),
                    'category' => 'ປື້ມ'
                ];
                $countByType['book'] = ($countByType['book'] ?? 0) + 1;
            }
        }

        // 5. Search Videos
        $videos = Video::getAll();
        foreach ($videos as $item) {
            if (($countByType['video'] ?? 0) >= self::MAX_PER_TYPE) break;
            if ($this->match($q, $item['ຊື່ພຣະສູດ'] ?? '') || $this->match($q, $item['ພຣະສູດ'] ?? '') || $this->match($q, $item['ໝວດທັມ'] ?? '')) {
                $link = $item['link'] ?? '';
                preg_match('/(?:youtu\.be\/|youtube\.com\/(?:.*v=|.*\/|.*embed\/|.*shorts\/))([\w-]+)/', $link, $matches);
                $videoId = $matches[1] ?? ($item['ID'] ?? '');
                $results[] = [
                    'type' => 'video',
                    'title' => $item['ຊື່ພຣະສູດ'] ?? '',
                    'detail' => mb_substr(strip_tags($item['ພຣະສູດ'] ?? ''), 0, 150) . '...',
                    'url' => url('/video/view/' . $videoId),
                    'category' => 'Video'
                ];
                $countByType['video'] = ($countByType['video'] ?? 0) + 1;
            }
        }

        // 6. Search Calendar
        $events = Calendar::getAll();
        foreach ($events as $item) {
            if (($countByType['calendar'] ?? 0) >= self::MAX_PER_TYPE) break;
            if ($this->match($q, $item['title'] ?? '') || $this->match($q, $item['details'] ?? '') || $this->match($q, $item['startDateTime'] ?? '') || $this->match($q, $item['endDateTime'] ?? '')) {
                $results[] = [
                    'type' => 'calendar',
                    'title' => $item['title'] ?? '',
                    'detail' => ($item['startDateTime'] ?? '') . ' - ' . mb_substr(strip_tags($item['details'] ?? ''), 0, 100) . '...',
                    'url' => url('/calendar/view/' . ($item['ID'] ?? '')),
                    'category' => 'ປະຕິທິນ'
                ];
                $countByType['calendar'] = ($countByType['calendar'] ?? 0) + 1;
            }
        }

        // 7. Search Anakame (all 5 categories)
        if (($countByType['anakame'] ?? 0) < self::MAX_PER_TYPE) {
            $allItems = [];
            $seen = [];
            $siteUp = true;

            foreach (self::ANAKAME_CATEGORIES as $catName => $catUrl) {
                if (!$siteUp) break;
                $html = @file_get_contents($catUrl, false, self::getStreamContext(3));
                if ($html === false) {
                    if ($catName === 'Sutta') $siteUp = false;
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

                    if (isset($seen[$href])) continue;
                    $seen[$href] = true;

                    if ($this->match($q, $title)) {
                        $allItems[] = [
                            'title' => $title,
                            'href' => self::resolveAnakameUrl($href, $catUrl),
                            'category' => $catName,
                        ];
                    }
                }
            }

            foreach ($allItems as $item) {
                if (($countByType['anakame'] ?? 0) >= self::MAX_PER_TYPE) break;
                $results[] = [
                    'type' => 'anakame',
                    'title' => $item['title'],
                    'detail' => 'Anakame (' . $item['category'] . ')',
                    'url' => url('/anakame/read?href=' . urlencode($item['href'])),
                    'category' => 'Anakame',
                ];
                $countByType['anakame'] = ($countByType['anakame'] ?? 0) + 1;
            }
        }

        // 8. Search Uttayarndham (keyword/tags all pages)
        if (($countByType['uttayarndham'] ?? 0) < self::MAX_PER_TYPE) {
            $allTags = [];
            $seen = [];
            $page = 0;
            $maxPages = 5;

            while ($page < $maxPages) {
                $tagUrl = self::UTTAYARNDHAM_BASE . '/keyword/tags' . ($page > 0 ? '?page=' . $page : '');
                $html = @file_get_contents($tagUrl, false, self::getStreamContext(5));
                if ($html === false) break;

                preg_match_all('/<a\s+href="(\/taxonomy\/term\/\d+)"[^>]*>\s*([^<]+?)\s*<\/a>/s', $html, $matches, PREG_SET_ORDER);

                $foundNew = false;
                foreach ($matches as $m) {
                    $name = trim($m[2]);
                    $href = trim($m[1]);
                    if (!empty($name) && !isset($seen[$href])) {
                        $seen[$href] = true;
                        if ($this->match($q, $name)) {
                            $allTags[] = [
                                'title' => $name,
                                'url' => $href,
                            ];
                        }
                        $foundNew = true;
                    }
                }

                $hasMore = preg_match('/rel="next"/', $html) === 1;
                if (!$hasMore || !$foundNew) break;
                $page++;
            }

            foreach ($allTags as $tag) {
                if (($countByType['uttayarndham'] ?? 0) >= self::MAX_PER_TYPE) break;
                $results[] = [
                    'type' => 'uttayarndham',
                    'title' => $tag['title'],
                    'detail' => 'ອຸດທະຍານທັມ (ທັມມະ)',
                    'url' => url('/uttayarndham/tag?url=' . urlencode($tag['url'])),
                    'category' => 'Uttayarndham',
                ];
                $countByType['uttayarndham'] = ($countByType['uttayarndham'] ?? 0) + 1;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'results' => array_slice($results, 0, self::MAX_TOTAL),
            'counts' => $countByType,
        ], JSON_UNESCAPED_UNICODE);
    }

    private static function resolveAnakameUrl(string $href, string $baseUrl): string {
        if (str_starts_with($href, 'http')) return $href;
        if (str_starts_with($href, '/')) {
            $parsed = parse_url($baseUrl);
            return ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? 'anakame.com') . $href;
        }
        $base = dirname($baseUrl);
        return $base . '/' . ltrim($href, '/');
    }

    private function match($query, $text) {
        if (empty($text)) return false;
        return mb_strpos(mb_strtolower($text), $query) !== false;
    }
}
