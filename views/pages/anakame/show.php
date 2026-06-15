<div x-data="{
    touchStartX: 0,
    touchEndX: 0,
    isTurning: false,
    turnDirection: '',
    theme: localStorage.getItem('buddhaword_theme') || 'light',
    prevHref: <?= $prevHref ? "'" . url('/anakame/read') . '?href=' . urlencode($prevHref) . "'" : "''" ?>,
    nextHref: <?= $nextHref ? "'" . url('/anakame/read') . '?href=' . urlencode($nextHref) . "'" : "''" ?>,
    init() {
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
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
        let el = e.target;
        while (el && el !== e.currentTarget) {
            const tag = el.tagName.toLowerCase();
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
        const threshold = 80;
        const diff = this.touchStartX - this.touchEndX;
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                if (this.nextHref) window.location.href = this.nextHref;
            } else {
                if (this.prevHref) window.location.href = this.prevHref;
            }
        }
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

        article:fullscreen,
        article:-webkit-full-screen,
        article:-moz-full-screen {
            overflow-y: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 1rem !important;
            background-color: inherit;
        }

        html.dark body,
        html.dark main {
            background-color: #1a181c !important;
            background-image: none !important;
        }

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
        .dark .reader-nav button {
            color: #C4A88A !important;
        }
        .dark .reader-nav button:hover {
            color: #DDCFBC !important;
        }
        .dark .reader-toolbar button:first-child {
            background: rgba(255,255,255,0.08) !important;
            color: #C4A88A !important;
        }
        .dark .reader-toolbar button:first-child:hover {
            background: rgba(255,255,255,0.12) !important;
        }
        .dark .progress-bg {
            background: rgba(255,255,255,0.1) !important;
        }
        .dark .tts-time {
            color: #a8977a !important;
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

        #ttsControls {
            transition: all 0.3s ease;
        }
    </style>

    <input type="hidden" id="ttsApiUrl" value="<?= url('/api/tts/synthesize') ?>">

    <!-- Breadcrumb -->
    <nav class="max-w-4xl mx-auto px-2 sm:px-6 mt-3 mb-0 z-20 relative" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-white/70 Lao-font" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/') ?>" itemprop="item" class="hover:text-white transition-colors"><span itemprop="name">ໜ້າຫຼັກ</span></a>
                <meta itemprop="position" content="1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mx-1 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/anakame') ?>" itemprop="item" class="hover:text-white transition-colors"><span itemprop="name">ອານາຄົມສູດ</span></a>
                <meta itemprop="position" content="2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mx-1 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-white/90 truncate max-w-[200px]">
                <span itemprop="name"><?= htmlspecialchars(mb_substr($title, 0, 80)) ?></span>
                <meta itemprop="position" content="3">
            </li>
        </ol>
    </nav>

    <article id="readerFullscreenTarget" class="max-w-4xl mx-auto p-2 sm:p-6 page-container">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden border border-white/20 ring-1 ring-black/5 reader-card">
            <!-- Header -->
            <div class="p-4 sm:p-6 bg-[#795548] text-white">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold leading-tight Lao-font"><?= htmlspecialchars($title) ?></h1>
                <div class="flex justify-between items-center mt-3">
                    <div class="flex items-center gap-1 sm:gap-2 reader-toolbar">
                        <button onclick="changeFontSize(-2)" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-white/10 hover:bg-white/20 text-white/70 hover:text-white font-bold transition-colors text-xs sm:text-sm">A-</button>
                        <button onclick="changeFontSize(2)" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl bg-white/10 hover:bg-white/20 text-white/70 hover:text-white font-bold transition-colors text-xs sm:text-sm">A+</button>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-2">
                        <!-- Theme Toggle -->
                        <button @click="toggleTheme()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white">
                            <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- TTS Button -->
                        <button onclick="toggleTTS()" id="ttsBtn" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white" title="ອ່ານອອກສຽງ">
                            <svg id="ttsIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M11 5L6 9H2v6h4l5 4V5z" />
                            </svg>
                        </button>

                        <!-- Fullscreen Toggle -->
                        <button onclick="toggleReaderFullscreen()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white">
                            <svg id="fsIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </button>

                        <!-- Share Button -->
                        <button onclick="shareReader(this)" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white/70 hover:text-white" title="ແບ່ງປັນ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>

                        <!-- Favorite -->
                        <div x-data="{
                            isFavorite: (function() {
                                try {
                                    var favs = JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]');
                                    var href = '<?= addslashes($href) ?>';
                                    return href ? favs.some(function(f) { return f.href === href; }) : false;
                                } catch(e) {
                                    return false;
                                }
                            })(),
                            toggleFavorite() {
                                try {
                                    var favs = JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]');
                                    var item = { title: '<?= addslashes($title) ?>', href: '<?= addslashes($href) ?>' };
                                    if (this.isFavorite) {
                                        favs = favs.filter(function(f) { return f.href !== item.href; });
                                    } else {
                                        favs.push(item);
                                    }
                                    localStorage.setItem('buddhaword_favorites', JSON.stringify(favs));
                                    this.isFavorite = !this.isFavorite;
                                } catch(e) {}
                            }
                        }">
                            <button @click="toggleFavorite()" class="p-1.5 sm:p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors" :class="isFavorite ? 'text-red-400' : 'text-white/50'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TTS Controls -->
            <div id="ttsControls" class="px-4 sm:px-6 py-2 bg-[#DDCFBC]/30 border-b border-[#795548]/10 hidden items-center gap-3" style="display:none">
                <button onclick="toggleTTS()" class="flex-shrink-0 p-1.5 rounded-full hover:bg-black/10 text-[#795548] transition-all" title="ຢຸດຊົ່ວຄາວ/ສືບຕໍ່">
                    <svg id="ttsPlayPauseIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5v14l11-7z" />
                    </svg>
                </button>
                <div class="flex-1 h-1.5 sm:h-2 bg-gray-200 rounded-full overflow-hidden cursor-pointer progress-bg" onclick="seekTTS(event)">
                    <div id="ttsProgress" class="h-full bg-[#795548] w-0" style="transition: width 0.1s linear"></div>
                </div>
                <span id="ttsTime" class="text-xs sm:text-sm text-gray-500 font-mono whitespace-nowrap tts-time">0:00 / 0:00</span>
                <button onclick="stopTTS()" class="flex-shrink-0 p-1.5 rounded-full hover:bg-black/10 text-gray-500 hover:text-red-500 transition-all" title="ຢຸດ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-4 sm:p-10 reader-content text-lg sm:text-xl md:text-2xl leading-relaxed text-gray-800 min-h-[300px] Lao-font whitespace-pre-wrap break-words" id="readerContent">
                <?php if ($content): ?>
                    <?= htmlspecialchars($content) ?>
                <?php else: ?>
                    <p class="text-gray-500">ບໍ່ສາມາດໂຫຼດເນື້ອຫາໄດ້</p>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <div class="px-6 py-4 flex justify-between items-center bg-gray-50/50 border-t border-gray-100 reader-nav">
                <div class="flex-1">
                    <template x-if="prevHref">
                        <a :href="prevHref" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            ກ່ອນໜ້າ
                        </a>
                    </template>
                </div>
                <div class="flex-1 flex justify-end">
                    <template x-if="nextHref">
                        <a :href="nextHref" class="flex items-center gap-1 text-[#795548] font-bold Lao-font hover:underline group text-right">
                            ຕໍ່ໄປ
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </article>

    <!-- Swipe Hints -->
    <div x-show="prevHref" class="swipe-hint left-4" :class="touchEndX > touchStartX && (touchEndX - touchStartX > 40) ? 'visible' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#795548]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </div>
    <div x-show="nextHref" class="swipe-hint right-4" :class="touchStartX > touchEndX && (touchStartX - touchEndX > 40) ? 'visible' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#795548]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </div>
</div>

<script src="<?= url('/assets/js/reader.js') ?>"></script>
