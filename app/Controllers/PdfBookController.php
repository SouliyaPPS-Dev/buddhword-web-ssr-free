<?php
namespace App\Controllers;

use App\Models\PdfBook;

class PdfBookController {
    public function index() {
        $books = PdfBook::getBooks();
        if (empty($books)) {
            http_response_code(404);
            echo 'No books found';
            return;
        } 

        $canonicalUrl = canonicalUrl();

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'ຄົ້ນຫາປຶ້ມ',
            'url' => $canonicalUrl,
        ];
 
        return view('pages.pdf-book.index', [
            'books' => $books,
            'seo' => [
                'title' => 'ຄົ້ນຫາປຶ້ມ - ຄຳສອນພຸດທະ',
                'description' => 'ຄົ້ນຫາປຶ້ມ ພ້ອມເນັ້ນຄຳສຳຄັນ',
                'keywords' => 'ຄົ້ນຫາປຶ້ມ, ປຶ້ມ, ທັມມະ, ຄຳສອນພຸດທະ',
                'image' => absoluteUrl('assets/images/logo_shared.png'),
                'json_ld' => $jsonLd,
            ]
        ]);
    }

    public function viewer($slug) {
        $info = PdfBook::getInfo($slug);
        if (!$info) {
            http_response_code(404);
            echo 'Book not found';
            return;
        }

        $canonicalUrl = canonicalUrl();

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $info['title'],
            'url' => $canonicalUrl,
            'inLanguage' => 'lo',
            'numberOfPages' => $info['totalPages'],
        ];
        if (!empty($info['year'])) {
            $jsonLd['datePublished'] = $info['year'];
        }

        $viewFile = $info['type'] === 'pdf' ? 'pages.pdf-book.viewer-pdf' : 'pages.pdf-book.viewer-docx';

        return view($viewFile, [
            'info' => $info,
            'slug' => $slug,
            'seo' => [
                'title' => $info['title'] . ' - ຄຳສອນພຸດທະ',
                'description' => 'ຄົ້ນຫາ ' . $info['title'] . ' ພ້ອມເນັ້ນຄຳສຳຄັນ',
                'keywords' => $info['title'] . 'ຄົ້ນຫາປຶ້ມ, ປຶ້ມ, ທັມມະ, ຄຳສອນພຸດທະ',
                'image' => absoluteUrl('assets/images/logo_shared.png'),
                'json_ld' => $jsonLd,
            ]
        ]);
    }

    public function search() {
        $slug = $_GET['book'] ?? '';
        $query = $_GET['q'] ?? '';
        if (!$slug || mb_strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $results = PdfBook::search($slug, $query);
        $this->json(['results' => $results]);
    }

    public function searchAll() {
        $query = $_GET['q'] ?? '';
        if (mb_strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $books = PdfBook::getBooks();
        $allResults = [];

        foreach ($books as $book) {
            $slug = $book['slug'];
            $results = PdfBook::search($slug, $query);
            foreach ($results as &$r) {
                $r['slug'] = $slug;
                $r['bookTitle'] = $book['title'];
                $r['bookType'] = $book['type'];
            }
            $allResults = array_merge($allResults, $results);
        }

        usort($allResults, function ($a, $b) {
            return $a['page'] - $b['page'];
        });

        $this->json(['results' => $allResults]);
    }

    public function page() {
        $slug = $_GET['book'] ?? '';
        $pageNum = intval($_GET['n'] ?? 0);
        $query = $_GET['q'] ?? '';

        $page = PdfBook::getPage($slug, $pageNum);
        if (!$page) {
            http_response_code(404);
            $this->json(['error' => 'Page not found']);
            return;
        }

        $highlightWords = [];
        if ($query && !empty($page['words'])) {
            $q = mb_strtolower(trim($query));
            foreach ($page['words'] as $word) {
                if (mb_strpos(mb_strtolower($word['w']), $q) !== false) {
                    $highlightWords[] = $word;
                }
            }
        }

        $this->json([
            'page' => $page['page'],
            'text' => $page['text'],
            'words' => $page['words'] ?? [],
            'highlightWords' => $highlightWords,
        ]);
    }

    public function show($slug, $n) {
        $info = PdfBook::getInfo($slug);
        if (!$info) {
            http_response_code(404);
            echo 'Book not found';
            return;
        }

        $pageNum = intval($n);
        $page = PdfBook::getPage($slug, $pageNum);
        if (!$page) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        $query = $_GET['q'] ?? '';

        $prevPage = $pageNum > 1 ? $pageNum - 1 : null;
        $nextPage = $pageNum < $info['totalPages'] ? $pageNum + 1 : null;

        $canonicalUrl = canonicalUrl();

        $description = strip_tags($page['text']);
        $description = mb_strlen($description) > 160 ? mb_substr($description, 0, 157) . '...' : $description;

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $info['title'] . ' - ໜ້າ ' . $pageNum,
            'description' => $description,
            'url' => $canonicalUrl,
            'inLanguage' => 'lo',
            'isPartOf' => [
                '@type' => 'Book',
                'name' => $info['title'],
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'ໜ້າຫຼັກ', 'item' => absoluteUrl('')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $info['title'], 'item' => absoluteUrl('/search-books')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => 'ໜ້າ ' . $pageNum],
                ],
            ],
        ];   

        // Use stored tocOffset from book.json (set during upload) if available
        if (isset($info['tocOffset'])) {
            $tocOffset = (int)$info['tocOffset'];
        } else {
            // Fall back to runtime calculation
            $tocOffset = 0;
            $tocText = $page['text'];
            $isToc = (bool)preg_match('/^(ສາລະບານ|สารບັນ)/mu', $tocText);
            if ($isToc) {
                $lines = explode("\n", $tocText);
                $firstTocPage = null;
                foreach ($lines as $line) {
                    $line = trim(preg_replace('/\s+/', ' ', $line));
                    if (empty($line)) continue;
                    if (preg_match('/^(.*?)[\s\.…]+(\d+)$/u', $line, $m)) { $firstTocPage = intval($m[2]); break; }
                }
                if ($firstTocPage) {
                    $allPages = PdfBook::getAll($slug);
                    $firstContentPage = null;
                    foreach ($allPages as $p) {
                        if ($p['page'] <= $pageNum || $p['page'] > $pageNum + 5) continue;
                        $tocLines = 0; $totalLines = 0;
                        foreach (explode("\n", $p['text']) as $l) {
                            $l = trim(preg_replace('/\s+/', ' ', $l));
                            if (empty($l)) continue;
                            $totalLines++;
                            if (preg_match('/^(.*?)[\s\.…]+(\d+)$/u', $l)) $tocLines++;
                        }
                        if ($totalLines > 0 && ($tocLines / $totalLines) < 0.7) { $firstContentPage = $p['page']; break; }
                    }
                    if ($firstContentPage) $tocOffset = $firstContentPage - $firstTocPage;
                }
            }
        }

        return view('pages.pdf-book.show', [
            'info' => $info,
            'slug' => $slug,
            'page' => $page,
            'query' => $query,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
            'tocOffset' => $tocOffset,
            'seo' => [
                'title' => $info['title'] . ' - ໜ້າ ' . $pageNum,
                'description' => $description,
                'keywords' => $info['title'] . ', ສາທະຍາຍທັມ, ໜ້າ ' . $pageNum,
                'image' => absoluteUrl('assets/images/logo_shared.png'),
                'json_ld' => $jsonLd,
            ]
        ]);
    }

    public function apiList() {
        $books = PdfBook::getBooks();
        if (empty($books)) {
            $this->json(['books' => []]);
            return;
        }

        $result = [];
        foreach ($books as $book) {
            $slug = $book['slug'];
            $coverDir = __DIR__ . '/../../public/assets/' . $slug;
            $coverUrl = null;
            foreach (['png', 'jpg', 'jpeg'] as $ext) {
                if (file_exists($coverDir . '/cover.' . $ext)) {
                    $coverUrl = url('/assets/' . $slug . '/cover.' . $ext);
                    break;
                }
            }
            if (!$coverUrl) {
                $coverUrl = url('assets/images/book-placeholder.svg');
            }

            $result[] = [
                'slug'      => $slug,
                'title'     => $book['title'] ?? $slug,
                'year'      => $book['year'] ?? null,
                'totalPages'=> $book['totalPages'] ?? 0,
                'type'      => $book['type'] ?? 'docx',
                'coverUrl'  => $coverUrl,
            ];
        }

        $this->json(['books' => $result]);
    }

    public function apiDetails() {
        $slug  = $_GET['slug'] ?? '';
        $title = $_GET['title'] ?? '';

        if (!$slug && !$title) {
            http_response_code(400);
            $this->json(['error' => 'Missing slug or title parameter']);
            return;
        }

        if (!$slug && $title) {
            $books = PdfBook::getBooks();
            foreach ($books as $book) {
                if (($book['title'] ?? '') === $title) {
                    $slug = $book['slug'];
                    break;
                }
            }
            if (!$slug) {
                http_response_code(404);
                $this->json(['error' => 'Book not found by title']);
                return;
            }
        }

        $info = PdfBook::getInfo($slug);
        if (!$info) {
            http_response_code(404);
            $this->json(['error' => 'Book not found']);
            return;
        }

        $coverDir = __DIR__ . '/../../public/assets/' . $slug;
        $coverUrl = null;
        foreach (['png', 'jpg', 'jpeg'] as $ext) {
            if (file_exists($coverDir . '/cover.' . $ext)) {
                $coverUrl = url('/assets/' . $slug . '/cover.' . $ext);
                break;
            }
        }
        if (!$coverUrl) {
            $coverUrl = url('assets/images/book-placeholder.svg');
        }

        $firstPage = PdfBook::getPage($slug, 1);
        $preview = null;
        if ($firstPage && !empty($firstPage['text'])) {
            $text = $firstPage['text'];
            $lines = explode("\n", trim($text));
            $previewLines = array_slice($lines, 0, 10);
            $preview = implode("\n", $previewLines);
            if (count($lines) > 10) $preview .= '...';
        }

        $this->json([
            'book' => [
                'slug'       => $slug,
                'title'      => $info['title'] ?? $slug,
                'year'       => $info['year'] ?? null,
                'totalPages' => $info['totalPages'] ?? 0,
                'type'       => $info['type'] ?? 'docx',
                'coverUrl'   => $coverUrl,
                'preview'    => $preview,
            ]
        ]);
    }

    public function apiPageNav() {
        $slug = $_GET['book'] ?? '';
        $pageNum = intval($_GET['n'] ?? 0);
        $query = $_GET['q'] ?? '';

        if (!$slug || $pageNum < 1) {
            http_response_code(400);
            $this->json(['error' => 'Missing or invalid book/n parameter']);
            return;
        }

        $info = PdfBook::getInfo($slug);
        if (!$info) {
            http_response_code(404);
            $this->json(['error' => 'Book not found']);
            return;
        }

        $page = PdfBook::getPage($slug, $pageNum);
        if (!$page) {
            http_response_code(404);
            $this->json(['error' => 'Page not found']);
            return;
        }

        $totalPages = $info['totalPages'] ?? 0;
        $prevPage = $pageNum > 1 ? $pageNum - 1 : null;
        $nextPage = $pageNum < $totalPages ? $pageNum + 1 : null;

        $highlightWords = [];
        if ($query && !empty($page['words'])) {
            $q = mb_strtolower(trim($query));
            foreach ($page['words'] as $word) {
                if (mb_strpos(mb_strtolower($word['w']), $q) !== false) {
                    $highlightWords[] = $word;
                }
            }
        }

        $this->json([
            'book' => [
                'slug'       => $slug,
                'title'      => $info['title'] ?? $slug,
                'totalPages' => $totalPages,
            ],
            'page' => [
                'number' => $page['page'],
                'text'   => $page['text'],
                'words'  => $page['words'] ?? [],
            ],
            'highlightWords' => $highlightWords,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
        ]);
    }

    public function apiFilter() {
        $query  = $_GET['q'] ?? '';
        $year   = $_GET['year'] ?? '';
        $type   = $_GET['type'] ?? '';
        $page   = max(1, intval($_GET['page'] ?? 1));
        $perPage = max(1, min(100, intval($_GET['perPage'] ?? 50)));

        $books = PdfBook::getBooks();
        $result = [];

        foreach ($books as $book) {
            $slug = $book['slug'];

            if ($year && ($book['year'] ?? '') != $year) continue;
            if ($type && ($book['type'] ?? '') !== $type) continue;
            if ($query) {
                $title = $book['title'] ?? $slug;
                if (mb_stripos($title, $query) === false) continue;
            }

            $coverDir = __DIR__ . '/../../public/assets/' . $slug;
            $coverUrl = null;
            foreach (['png', 'jpg', 'jpeg'] as $ext) {
                if (file_exists($coverDir . '/cover.' . $ext)) {
                    $coverUrl = url('/assets/' . $slug . '/cover.' . $ext);
                    break;
                }
            }
            if (!$coverUrl) {
                $coverUrl = url('assets/images/book-placeholder.svg');
            }

            $result[] = [
                'slug'       => $slug,
                'title'      => $book['title'] ?? $slug,
                'year'       => $book['year'] ?? null,
                'totalPages' => $book['totalPages'] ?? 0,
                'type'       => $book['type'] ?? 'docx',
                'coverUrl'   => $coverUrl,
            ];
        }

        $total = count($result);
        $offset = ($page - 1) * $perPage;
        $paged = array_slice($result, $offset, $perPage);

        $this->json([
            'books'    => $paged,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'totalPages' => (int)ceil($total / $perPage),
        ]);
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
