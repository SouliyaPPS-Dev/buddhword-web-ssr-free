#!/usr/bin/env python3
"""
Re-clones the CV timeline deck (slides 8 → end) into views/pages/book/slides.php.

    python3 scripts/sync-book-slides.py [path/to/cv/views/pages/timeline.php]

Only the slide markup, the slide titles and missing slide CSS are replaced; the book deck's own
head, top bar, controls, zoom/fullscreen and narration code are kept. CV slide k (0-based) becomes
book slide k - 7, so every index (i===N, go(N), titles[N]) and visible slide number is shifted.
"""
import re
import sys
from pathlib import Path

FIRST = 7  # CV slide index that becomes book slide 0
ROOT = Path(__file__).resolve().parent.parent
CV = Path(sys.argv[1] if len(sys.argv) > 1 else ROOT.parent / 'cv/views/pages/timeline.php')
BOOK = ROOT / 'views/pages/book/slides.php'
START = '        <!-- 4. The present we feel'
END = '      <!-- Image zoom'


def shift(m, fmt):
    n = int(m.group(1)) - FIRST
    if n < 0:
        raise SystemExit(f'reference to a slide before {FIRST + 1}: {m.group(0)!r}')
    return fmt.format(n)


cv = CV.read_text(encoding='utf-8')
book = BOOK.read_text(encoding='utf-8')

region = cv[cv.index(START):cv.index(END)]
region = re.sub(r'\bi===(\d+)', lambda m: shift(m, 'i==={}'), region)
region = re.sub(r'\bgo\((\d+)\)', lambda m: shift(m, 'go({})'), region)
region = re.sub(r'\btitles\[(\d+)\]', lambda m: shift(m, 'titles[{}]'), region)
# PHP lists of slide links: foreach ([[13, 'fa-…'], [15, 'fa-…']] as [$n, $ic])
region = re.sub(r"\[(\d+), ('fa-[\w-]+')\]", lambda m: shift(m, '[{}, ' + m.group(2) + ']'), region)
# Visible 1-based slide numbers: "'13 · ' + titles[…]" and "ສະໄລ 12"
region = re.sub(r"'(\d+) · '", lambda m: f"'{int(m.group(1)) - FIRST} · '", region)
region = re.sub(r'ສະໄລ (\d+)', lambda m: f'ສະໄລ {int(m.group(1)) - FIRST}', region)
region = re.sub(r"asset\('([^']+)'\)", r"url('assets/images/\1')", region)
region = re.sub(r'(?i)\s*souliya\.?|ສຸລິຍາ', '', region)

book = book[:book.index(START)] + region + book[book.index(END):]

titles = re.search(r"^      titles: \[(.*)\],$", cv, re.M).group(1)
titles = re.findall(r"'((?:[^'\\]|\\.)*)'", titles)[FIRST:]
book = re.sub(r"^      titles: \[.*\],$", lambda _: '      titles: [' + ', '.join(f"'{t}'" for t in titles) + '],', book, count=1, flags=re.M)

# Slide CSS the CV deck has gained since the last sync.
if '.grab {' not in book:
    css = re.search(r'  /\* SN 12\.61 monkey.*?\n(?:  [.@].*\n)+', cv).group(0)
    book = book.replace('\n  @media print {', '\n' + css + '\n  @media print {', 1)

BOOK.write_text(book, encoding='utf-8')
print(f'{len(titles)} slides written to {BOOK.relative_to(ROOT)}')
