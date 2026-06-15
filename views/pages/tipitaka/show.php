<div x-data="{
    touchStartX: 0,
    touchEndX: 0,
    isTurning: false,
    turnDirection: '',
    theme: localStorage.getItem('buddhaword_theme') || 'light',
    prevUrl: '<?= $prevPage ? url("/etipitaka/$code/$volume/$prevPage") : "" ?>',
    nextUrl: '<?= $nextPage ? url("/etipitaka/$code/$volume/$nextPage") : "" ?>',
    favorites: [],
    init() {
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
        this.refreshFavorites();
    },
    get pageId() {
        return 'etipitaka-<?= $code ?>-<?= $volume ?>-<?= $page ?>';
    },
    refreshFavorites() {
        try {
            this.favorites = JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]');
        } catch(e) {
            this.favorites = [];
        }
    },
    get favIndex() {
        var self = this;
        return this.favorites.findIndex(function(f) { return f.id === self.pageId; });
    },
    get isInFavorites() {
        return this.favIndex !== -1;
    },
    toggleFavorite() {
        try {
            var favs = JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]');
            if (this.isInFavorites) {
                favs.splice(this.favIndex, 1);
            } else {
                favs.push({
                    id: this.pageId,
                    title: 'E-Tipitaka - ເຫຼັ້ມທີ່ <?= $volume ?> ຫນ້າ <?= $page ?> (<?= htmlspecialchars($label, ENT_QUOTES) ?>)',
                    label: '<?= htmlspecialchars($label, ENT_QUOTES) ?>',
                    volume: <?= $volume ?>,
                    page: <?= $page ?>,
                    code: '<?= $code ?>',
                    url: window.location.href
                });
            }
            localStorage.setItem('buddhaword_favorites', JSON.stringify(favs));
            this.favorites = favs;
        } catch(e) {}
    },
    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('buddhaword_theme', this.theme);
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    handleTouchStart(e) {
        var el = e.target;
        while (el && el !== e.currentTarget) {
            var tag = el.tagName.toLowerCase();
            if (tag === 'button' || tag === 'a' || tag === 'input' || tag === 'select' || tag === 'textarea' || el.isContentEditable || el.closest('[contenteditable]') || el.getAttribute('role') === 'button') {
                this.touchStartX = 0;
                return;
            }
            el = el.parentElement;
        }
        this.touchStartX = e.changedTouches[0].screenX;
    },
    handleTouchEnd(e) {
        this.touchEndX = e.changedTouches[0].screenX;
        if (this.touchStartX !== 0) {
            this.handleSwipe();
        }
    },
    handleSwipe() {
        var threshold = 80;
        var diff = this.touchStartX - this.touchEndX;
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.navigate('next');
            } else {
                this.navigate('prev');
            }
        }
    },
    navigate(dir) {
        var url = (dir === 'next') ? this.nextUrl : this.prevUrl;
        if (!url || this.isTurning) return;
        this.turnDirection = (dir === 'next') ? 'turn-next' : 'turn-prev';
        this.isTurning = true;
        setTimeout(function() {
            window.location.href = url;
        }, 500);
    }
}"
@touchstart="handleTouchStart($event)"
@touchend="handleTouchEnd($event)"
class="relative overflow-hidden min-h-screen pb-20" style="touch-action: manipulation;">

    <style>
        .page-container {
            transition: transform 0.5s cubic-bezier(0.645, 0.045, 0.355, 1), opacity 0.4s ease;
            transform-origin: center;
            perspective: 1500px;
        }
        .turn-next {
            transform: rotateY(-20deg) translateX(-100%) scale(0.9);
            opacity: 0;
        }
        .turn-prev {
            transform: rotateY(20deg) translateX(100%) scale(0.9);
            opacity: 0;
        }
        .swipe-hint {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(121, 85, 72, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 50;
        }
        .swipe-hint.visible { opacity: 1; }
        .dark .reader-card {
            background: rgba(28, 26, 30, 0.97) !important;
            backdrop-filter: blur(12px);
            border-color: rgba(255,255,255,0.06);
        }
        .dark .reader-content {
            color: #E8DCC8 !important;
        }
        .dark .reader-nav {
            background: rgba(40, 35, 38, 0.6) !important;
            border-color: rgba(255,255,255,0.05);
        }
        .dark .reader-nav a,
        .dark .reader-nav button {
            color: #C4A88A !important;
        }
        .dark .reader-nav a:hover,
        .dark .reader-nav button:hover {
            color: #DDCFBC !important;
        }
        html.dark body,
        html.dark main {
            background-color: #1a181c !important;
            background-image: none !important;
        }
        article:fullscreen,
        article:-webkit-full-screen,
        article:-moz-full-screen {
            overflow-y: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 1rem !important;
            background-color: inherit;
        }
        .tts-w {
            transition: background-color 0.15s ease, color 0.15s ease;
            border-radius: 2px;
        }
        .tts-active {
            background-color: #795548;
            color: #fff;
            border-radius: 4px;
            padding: 0 2px;
        }
        .dark .tts-active {
            background-color: #a0896e;
            color: #1a181c;
        }
    </style>

    <input type="hidden" id="ttsApiUrl" value="<?= url('/api/tts/synthesize') ?>">

    <nav class="max-w-4xl mx-auto px-2 sm:px-6 mt-3 mb-0 z-20 relative" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-white/70 Lao-font" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/') ?>" itemprop="item" class="hover:text-white transition-colors"><span itemprop="name">ໜ້າຫຼັກ</span></a>
                <meta itemprop="position" content="1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mx-1 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/etipitaka') ?>" itemprop="item" class="hover:text-white transition-colors"><span itemprop="name">E-Tipitaka</span></a>
                <meta itemprop="position" content="2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mx-1 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/etipitaka/' . $code) ?>" itemprop="item" class="hover:text-white transition-colors"><span itemprop="name"><?= htmlspecialchars($label) ?></span></a>
                <meta itemprop="position" content="3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mx-1 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-white font-bold">ເຫຼັ້ມທີ່ <?= $volume ?> ຫນ້າ <?= $page ?></span>
                <meta itemprop="position" content="4">
            </li>
        </ol>
    </nav>

    <article id="readerFullscreenTarget" class="max-w-4xl mx-auto p-2 sm:p-6 page-container" :class="isTurning ? turnDirection : ''">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden border border-white/20 ring-1 ring-black/5 reader-card">
            <div class="p-4 sm:p-6 bg-[#795548] text-white">
                <div class="flex justify-between items-start gap-4">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold leading-tight Lao-font"><?= htmlspecialchars($label) ?></h1>
                        <p class="text-white/80 mt-1 text-xs sm:text-base Lao-font">ເຫຼັ້ມທີ່ <?= $volume ?> ຫນ້າ <?= $page ?></p>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-3">
                    <div class="flex items-center gap-1 sm:gap-2">
                        <button onclick="changeFontSize(-2)" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-white/10 hover:bg-white/20 text-white/70 hover:text-white font-bold transition-colors text-xs sm:text-sm">A-</button>
                        <button onclick="changeFontSize(2)" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-white/10 hover:bg-white/20 text-white/70 hover:text-white font-bold transition-colors text-xs sm:text-sm">A+</button>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-2">
                        <button @click="toggleTheme()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white">
                            <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>
                        <button onclick="toggleTTS()" id="ttsBtn" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white" title="ອ່ານອອກສຽງ">
                            <svg id="ttsIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M11 5L6 9H2v6h4l5 4V5z" />
                            </svg>
                        </button>
                        <button onclick="toggleReaderFullscreen()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white">
                            <svg id="fsIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </button>
                        <button onclick="shareReader(this)" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white" title="ແບ່ງປັນ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>
                        <button @click="toggleFavorite()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors" :class="isInFavorites ? 'text-red-400' : 'text-white/50'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" :fill="isInFavorites ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="ttsControls" class="px-4 sm:px-6 py-2 bg-[#DDCFBC]/30 border-b border-[#795548]/10 hidden items-center gap-3" style="display:none">
                <button onclick="toggleTTS()" class="flex-shrink-0 p-1.5 rounded-full hover:bg-black/10 text-[#795548] transition-all" title="ຢຸດຊົ່ວຄາວ/ສືບຕໍ່">
                    <svg id="ttsPlayPauseIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5v14l11-7z" />
                    </svg>
                </button>
                <div class="flex-1 h-1.5 sm:h-2 bg-gray-200 rounded-full overflow-hidden cursor-pointer" onclick="seekTTS(event)">
                    <div id="ttsProgress" class="h-full bg-[#795548] w-0" style="transition: width 0.1s linear"></div>
                </div>
                <span id="ttsTime" class="text-xs sm:text-sm text-gray-500 font-mono whitespace-nowrap">0:00 / 0:00</span>
                <button onclick="stopTTS()" class="flex-shrink-0 p-1.5 rounded-full hover:bg-black/10 text-gray-500 hover:text-red-500 transition-all" title="ຢຸດ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-10 text-lg sm:text-xl md:text-2xl leading-relaxed text-gray-800 Lao-font min-h-[300px] reader-content" id="readerContent">
                <div style="white-space: pre-wrap; word-break: break-word; font-family: 'Phetsarath', 'Noto Sans Lao', sans-serif;">
                    <?= htmlspecialchars($content['content']) ?>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-4 flex justify-between items-center bg-gray-50/50 border-t border-gray-100 reader-nav">
                <div class="flex-1">
                    <template x-if="prevUrl">
                        <button @click="navigate('prev')" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group text-sm sm:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            ຫນ້າ <?= $prevPage ?>
                        </button>
                    </template>
                    <?php if ($prevPage): ?>
                    <a href="<?= url('/etipitaka/' . $code . '/' . $volume . '/' . $prevPage) ?>" x-show="false" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group text-sm sm:text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        ຫນ້າ <?= $prevPage ?>
                    </a>
                    <?php endif; ?>
                </div>
                <span class="text-gray-500 text-xs sm:text-sm Lao-font whitespace-nowrap px-2"><?= $page ?> / <?= $totalPages ?></span>
                <div class="flex-1 flex justify-end">
                    <template x-if="nextUrl">
                        <button @click="navigate('next')" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group text-sm sm:text-base">
                            ຫນ້າ <?= $nextPage ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </template>
                    <?php if ($nextPage): ?>
                    <a href="<?= url('/etipitaka/' . $code . '/' . $volume . '/' . $nextPage) ?>" x-show="false" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group text-sm sm:text-base">
                        ຫນ້າ <?= $nextPage ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>

    <div x-show="!!prevUrl" class="swipe-hint left-4" :class="touchEndX > touchStartX && (touchEndX - touchStartX > 40) ? 'visible' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#795548]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </div>
    <div x-show="!!nextUrl" class="swipe-hint right-4" :class="touchStartX > touchEndX && (touchStartX - touchEndX > 40) ? 'visible' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#795548]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </div>
</div>
<script>
    window.scrollTo(0, 0);
</script>
<script src="<?= url('assets/js/reader.js') ?>"></script>
