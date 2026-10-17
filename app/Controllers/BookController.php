<?php
namespace App\Controllers;

use App\Models\Book;

class BookController {
    public function index() {
        $books = Book::getAll();

        $dharmaCategories = array_values(array_unique(array_filter(array_map(fn($b) => $b['ໝວດຟາຍ'] ?? '', $books))));
        sort($dharmaCategories);

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'ປື້ມ - ຄຳສອນພຸດທະ',
            'description' => 'ລວມປື້ມທັມມະ ແລະ ຄຳສອນຂອງພຣະພຸດທະເຈົ້າ',
            'url' => canonicalUrl(),
            'inLanguage' => 'lo',
        ];

        return view('pages.book.index', [
            'books' => $books,
            'dharmaCategories' => $dharmaCategories,
            'seo' => [
                'title' => 'ປື້ມ - ຄຳສອນພຸດທະ',
                'description' => 'ລວມປື້ມທັມມະ ແລະ ຄຳສອນຂອງພຣະພຸດທະເຈົ້າ',
                'keywords' => 'ປື້ມ, ທັມມະ, ຄຳສອນພຸດທະ, ພຸດທະສາສະໜາ',
                'json_ld' => $jsonLd,
            ]
        ]);
    }

    // The 12 links in clock order (hour 12 = ອະວິຊາ), with their round (ວັດຕະ: ກິເລສ/ກຳ/ວິບາກ),
    // Pali formula and the period (ກາລະ) each belongs to.
    public static function paticca() {
        return [
            ['hour' => 12, 'vatta' => 'ກິເລສ', 'pali' => 'ອະວິຊຊາ ປັດຈະຍາ ສັງຂາຣາ', 'name' => 'ອະວິຊາ', 'mean' => 'ຄວາມບໍ່ຮູ້ຕາມເປັນຈິງ', 'detail' => 'ບໍ່ຮູ້ອະລິຍະສັດ 4: ທຸກ, ເຫດຂອງທຸກ, ຄວາມດັບທຸກ ແລະ ຂໍ້ປະຕິບັດເຖິງຄວາມດັບທຸກ.', 'kala' => 'past'],
            ['hour' => 1, 'vatta' => 'ກຳ', 'pali' => 'ສັງຂາຣະ ປັດຈະຍາ ວິນຍານັງ', 'name' => 'ສັງຂານ', 'mean' => 'ການປຸງແຕ່ງ (ກຳ)', 'detail' => 'ການປຸງແຕ່ງ 3 ທາງ: ກາຍ, ວາຈາ ແລະ ໃຈ.', 'kala' => 'past'],
            ['hour' => 2, 'vatta' => 'ວິບາກ', 'pali' => 'ວິນຍານະ ປັດຈະຍາ ນາມະຣູປັງ', 'name' => 'ວິນຍານ', 'mean' => 'ທາດຮູ້ແຈ້ງອາລົມ', 'detail' => 'ວິນຍານ 6: ທາງຕາ, ຫູ, ດັງ, ລີ້ນ, ກາຍ ແລະ ໃຈ.', 'kala' => 'present-result'],
            ['hour' => 3, 'vatta' => 'ວິບາກ', 'pali' => 'ນາມະຣູປະ ປັດຈະຍາ ສະລາຍະຕະນັງ', 'name' => 'ນາມຮູບ', 'mean' => 'ຈິດໃຈ ແລະ ຮ່າງກາຍ', 'detail' => 'ນາມ: ເວທະນາ, ສັນຍາ, ສັງຂານ — ຮູບ: ດິນ, ນ້ຳ, ໄຟ, ລົມ.', 'kala' => 'present-result'],
            ['hour' => 4, 'vatta' => 'ວິບາກ', 'pali' => 'ສະລາຍະຕະນະ ປັດຈະຍາ ຜັສໂສ', 'name' => 'ສະລາຍະຕະນະ', 'mean' => 'ຊ່ອງທາງຮັບຮູ້ 6', 'detail' => 'ຕາ, ຫູ, ດັງ, ລີ້ນ, ກາຍ, ໃຈ — ປະຕູທີ່ວິນຍານໄປຮັບຮູ້.', 'kala' => 'present-result'],
            ['hour' => 5, 'vatta' => 'ວິບາກ', 'pali' => 'ຜັສສະ ປັດຈະຍາ ເວທະນາ', 'name' => 'ຜັດສະ', 'mean' => 'ການກະທົບອາລົມ', 'detail' => 'ຜັດສະ = ອາຍະຕະນະໃນ + ອາລົມ + ວິນຍານ ມາປະຈວບກັນ.', 'kala' => 'present-result'],
            ['hour' => 6, 'vatta' => 'ວິບາກ', 'pali' => 'ເວທະນາ ປັດຈະຍາ ຕັນຫາ', 'name' => 'ເວທະນາ', 'mean' => 'ການສະເຫວີຍອາລົມ', 'detail' => 'ຮູ້ສຶກ ສຸກ, ທຸກ ຫຼື ເສີຍໆ ຫຼັງຈາກການກະທົບ.', 'kala' => 'present-result'],
            ['hour' => 7, 'vatta' => 'ກິເລສ', 'pali' => 'ຕັນຫາ ປັດຈະຍາ ອຸປາທານັງ', 'name' => 'ຕັນຫາ', 'mean' => 'ຄວາມທະຍານຢາກ', 'detail' => 'ຄວາມຢາກໃນເວທະນາ: ກາມຕັນຫາ, ພະວະຕັນຫາ, ວິພະວະຕັນຫາ.', 'kala' => 'present-cause'],
            ['hour' => 8, 'vatta' => 'ກິເລສ', 'pali' => 'ອຸປາທານະ ປັດຈະຍາ ພະໂວ', 'name' => 'ອຸປາທານ', 'mean' => 'ຄວາມຍຶດຕິດ', 'detail' => 'ຄວາມຍຶດໝັ້ນຖືໝັ້ນ — ລວມທັງການຍຶດວ່າ “ນີ້ແມ່ນຕົວເຮົາ”.', 'kala' => 'present-cause'],
            ['hour' => 9, 'vatta' => 'ກຳ', 'pali' => 'ພະວະ ປັດຈະຍາ ຊາຕິ', 'name' => 'ພົບ', 'mean' => 'ພາວະຊີວິດເປັນຢູ່', 'detail' => 'ຄວາມເປັນ: ກຳມະພົບ (ກຳ) ແລະ ອຸປັດຕິພົບ (ຜົນຂອງກຳ).', 'kala' => 'present-cause'],
            ['hour' => 10, 'vatta' => 'ວິບາກ', 'pali' => 'ຊາຕິ ປັດຈະຍາ ຊະຣາມະຣະນັງ', 'name' => 'ຊາດ', 'mean' => 'ຄວາມເກີດ', 'detail' => 'ຄວາມປາກົດຂຶ້ນຂອງຂັນທ໌ 5 — ການເກີດຂອງ “ຕົວເຮົາ” ໃໝ່.', 'kala' => 'future'],
            ['hour' => 11, 'vatta' => 'ວິບາກ', 'pali' => 'ໂສກະ ປະລິເທວະ ທຸກຂະ ໂທມະນັດສຸປາຍາສາ ສຳພະວັນຕິ', 'name' => 'ຊະລາມໍລະນະ', 'mean' => 'ຄວາມແກ່ ແລະ ຄວາມຕາຍ', 'detail' => 'ທຸກ: ໂສກະ, ປະລິເທວະ, ທຸກຂະ, ໂທມະນັດ, ອຸປາຍາສະ — ແລ້ວວົນກັບໄປຫາອະວິຊາ.', 'kala' => 'future'],
        ];
    }

    // Full-screen slide deck (no site layout), opened from the cover card at the start of the book list.
    public function slides() {
        $paticca = self::paticca();
        $seo = [
            'title' => 'ວົງລໍ້ ກິເລສ · ກຳ · ວິບາກ - ຄຳສອນພຸດທະ',
            'description' => 'ປະຕິຈຈະສະມຸບບາດ · ອາການ 20 — ເວລາກັບຈິດ, ແສງຈາກດວງດາວ ແລະ ວົງລໍ້ ກິເລສ · ກຳ · ວິບາກ',
            'image' => absoluteUrl('assets/images/paticca-wheel-cover.png'),
            'canonical' => canonicalUrl(),
        ];
        $narration = self::slidesNarration();
        require __DIR__ . '/../../views/pages/book/slides.php';
    }

    // [lang][slide] => [['t' => sentence, 'a' => pre-generated mp3 URL or null], ...].
    // A missing file (sentence edited, script not re-run) falls back to /api/tts/synthesize in the browser.
    public static function slidesNarration() {
        require_once __DIR__ . '/../Data/slides_audio.php';
        $out = [];
        foreach (require __DIR__ . '/../Data/slides_narration.php' as $lang => $slides) {
            foreach ($slides as $s => $lines) {
                foreach ($lines as $text) {
                    $file = slidesAudioFile($lang, $text);
                    $out[$lang][$s][] = ['t' => $text, 'a' => is_file(SLIDES_AUDIO_DIR . '/' . $file) ? url('assets/audio/slides/' . $file) : null];
                }
            }
        }
        return $out;
    }

    public function show($id) {
        $books = Book::getAll();
        $book = null;
        foreach ($books as $item) {
            if ($item['ID'] == $id) {
                $book = $item;
                break;
            }
        }

        if (!$book) {
            header("HTTP/1.0 404 Not Found");
            echo "Book not found";
            return;
        }

        $linkBook = $book['link'] ?? '';
        $pdfEmbedLink = str_replace('/view?usp=sharing', '/preview', $linkBook);
        $pdfDownloadUrl = '';
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $linkBook, $matches)) {
            $fileId = $matches[1];
            $pdfDownloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        }
 
        $canonicalUrl = canonicalUrl();
        $bookTitle = $book['ຊື່'] ?? 'ປື້ມ';

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $bookTitle,
            'url' => $canonicalUrl,
            'inLanguage' => 'lo',
            'isPartOf' => [
                '@type' => 'Collection',
                'name' => 'ຄຳສອນພຸດທະ', 
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'ໜ້າຫຼັກ', 'item' => absoluteUrl('')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'ປື້ມ', 'item' => absoluteUrl('/book')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $bookTitle],
                ],
            ],
        ];

        if (!empty($book['imageURL'])) {
            $jsonLd['image'] = $book['imageURL'];
        }

        $bookDescription = strip_tags($book['description'] ?? $book['detail'] ?? '');
        $bookDescription = !empty($bookDescription)
            ? (mb_strlen($bookDescription) > 160 ? mb_substr($bookDescription, 0, 157) . '...' : $bookDescription)
            : 'ປຶ້ມ: ' . $bookTitle;

        return view('pages.book.show', [
            'book' => $book,
            'pdfEmbedLink' => $pdfEmbedLink,
            'pdfDownloadUrl' => $pdfDownloadUrl,
            'seo' => [
                'title' => $bookTitle . ' - ຄຳສອນພຸດທະ',
                'description' => $bookDescription,
                'keywords' => ($book['ໝວດທັມ'] ?? '') . ', ປື້ມ, ທັມມະ, ຄຳສອນພຸດທະ',
                'image' => $book['imageURL'] ?? absoluteUrl('assets/images/logo_shared.png'),
                'json_ld' => $jsonLd,
                'og_type' => 'book',
            ]
        ]);
    }
}

   