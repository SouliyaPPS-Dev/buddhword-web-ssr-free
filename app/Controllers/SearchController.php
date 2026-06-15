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

    public function search() {
        $query = $_GET['q'] ?? '';
        $q = mb_strtolower(trim($query));

        if (mb_strlen($q) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        $results = [];
        $countByType = [];

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

        // 2. Search E-Tipitaka
        $etipitakaCodes = ['thai', 'thaimm'];
        foreach ($etipitakaCodes as $code) {
            if (($countByType['etipitaka'] ?? 0) >= self::MAX_PER_TYPE) break;
            try {
                $etResults = EtipitakaService::search($code, $q, 10);
                foreach ($etResults as $er) {
                    if (($countByType['etipitaka'] ?? 0) >= self::MAX_PER_TYPE) break;
                    $volume = (int)$er['volume'];
                    $page = (int)$er['page'];
                    $content = trim(preg_replace('/\s+/', ' ', $er['content'] ?? ''));
                    $detail = mb_strlen($content) > 150 ? mb_substr($content, 0, 150) . '...' : $content;
                    $label = EtipitakaService::getLabel($code);
                    $results[] = [
                        'type' => 'etipitaka',
                        'title' => 'ເຫຼັ້ມທີ່ ' . $volume . ' ຫນ້າ ' . $page . ' (' . $label . ')',
                        'detail' => $detail,
                        'url' => url('/etipitaka/' . $code . '/' . $volume . '/' . $page),
                        'category' => 'E-Tipitaka',
                    ];
                    $countByType['etipitaka'] = ($countByType['etipitaka'] ?? 0) + 1;
                }
            } catch (\Throwable $e) {
                error_log('Search etipitaka error: ' . $e->getMessage());
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
                $results[] = [
                    'type' => 'video',
                    'title' => $item['ຊື່ພຣະສູດ'] ?? '',
                    'detail' => mb_substr(strip_tags($item['ພຣະສູດ'] ?? ''), 0, 150) . '...',
                    'url' => url('/video'),
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

        // 7. Search Anakame
        if (($countByType['anakame'] ?? 0) < self::MAX_PER_TYPE) {
            $anakameHtml = @file_get_contents('http://anakame.com/page/1_Sutas/main/1_Sutta_number.htm');
            if ($anakameHtml !== false) {
                preg_match_all('/<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>/i', $anakameHtml, $aMatches, PREG_SET_ORDER);
                $seen = [];
                foreach ($aMatches as $am) {
                    if (($countByType['anakame'] ?? 0) >= self::MAX_PER_TYPE) break;
                    $title = trim(strip_tags($am[2]));
                    if (empty($title) || !str_contains($am[1], '.htm')) continue;
                    $normalized = str_replace('../', '', $am[1]);
                    $normalized = preg_replace('/#.*$/', '', $normalized);
                    $key = $normalized . '|' . $title;
                    if (isset($seen[$key])) continue;
                    $seen[$key] = true;
                    if ($this->match($q, $title)) {
                        $results[] = [
                            'type' => 'anakame',
                            'title' => $title,
                            'detail' => 'ອານາຄົມສູດ ພາສາໄທ',
                            'url' => url('/anakame/read?href=' . urlencode($normalized)),
                            'category' => 'Anakame',
                        ];
                        $countByType['anakame'] = ($countByType['anakame'] ?? 0) + 1;
                    }
                }
            }
        }

        // 8. Search Uttayarndham
        if (($countByType['uttayarndham'] ?? 0) < self::MAX_PER_TYPE) {
            $uttHtml = @file_get_contents('https://uttayarndham.org/dhamma-sharing');
            if ($uttHtml !== false) {
                preg_match_all('/<h4><a\s+href="\s+(\/[^"]+)"[^>]*>\s*([^<]+?)\s*<\/a><\/h4>/s', $uttHtml, $uMatches, PREG_SET_ORDER);
                foreach ($uMatches as $um) {
                    if (($countByType['uttayarndham'] ?? 0) >= self::MAX_PER_TYPE) break;
                    $title = trim($um[2]);
                    if ($this->match($q, $title)) {
                        $results[] = [
                            'type' => 'uttayarndham',
                            'title' => $title,
                            'detail' => 'ອຸດທະຍານທັມ (ທັມມະ)',
                            'url' => url('/uttayarndham/read?url=' . urlencode(trim($um[1]))),
                            'category' => 'Uttayarndham',
                        ];
                        $countByType['uttayarndham'] = ($countByType['uttayarndham'] ?? 0) + 1;
                    }
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(array_slice($results, 0, self::MAX_TOTAL), JSON_UNESCAPED_UNICODE);
    }

    private function match($query, $text) {
        if (empty($text)) return false;
        return mb_strpos(mb_strtolower($text), $query) !== false;
    }
}