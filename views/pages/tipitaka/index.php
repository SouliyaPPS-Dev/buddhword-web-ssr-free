<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tipitakaSearch', () => ({
        query: '<?= htmlspecialchars($query, ENT_QUOTES) ?>',
        currentCode: '<?= htmlspecialchars($currentCode, ENT_QUOTES) ?>',
        results: [],
        groupedResults: {},
        isSearching: false,
        hasSearched: false,
        searchTerm: '<?= htmlspecialchars($query, ENT_QUOTES) ?>',

        init() {
            if (this.searchTerm.length >= 2) {
                this.performSearch();
            }
        },

        highlight(text) {
            if (!text || !this.searchTerm) return this.escapeHtml(text);
            const escaped = this.searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(' + escaped + ')', 'gi');
            return this.escapeHtml(text).replace(regex, '<span class="bg-yellow-200 font-bold text-black px-0.5">$1</span>');
        },

        escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        },

        buildExcerpt(content, query) {
            let cleaned = content.replace(/\t/g, ' ').replace(/\s+/g, ' ').trim();
            const idx = cleaned.toLowerCase().indexOf(query.toLowerCase());
            if (idx === -1) {
                return cleaned.length > 150 ? cleaned.substring(0, 150) + '...' : cleaned;
            }
            let start = idx > 60 ? idx - 60 : 0;
            let end = (idx + query.length + 90) > cleaned.length ? cleaned.length : (idx + query.length + 90);
            let prefix = start > 0 ? '...' : '';
            let suffix = end < cleaned.length ? '...' : '';
            return prefix + this.escapeHtml(cleaned.substring(start, end)) + suffix;
        },

        async performSearch() {
            const q = this.searchTerm.trim();
            if (q.length < 2) return;
            this.isSearching = true;
            this.hasSearched = true;
            try {
                const resp = await fetch('<?= url('/api/etipitaka/search') ?>?code=' + this.currentCode + '&q=' + encodeURIComponent(q));
                const data = await resp.json();
                this.results = data.results || [];
                this.groupedResults = data.grouped || {};
            } catch(e) {
                console.error('Search failed', e);
                this.results = [];
                this.groupedResults = {};
            } finally {
                this.isSearching = false;
            }
        },

        selectCategory(code) {
            this.currentCode = code;
            this.searchTerm = '';
            this.results = [];
            this.groupedResults = {};
            this.hasSearched = false;
        }
    }));
});
</script>

<section x-data="tipitakaSearch" class="flex flex-col items-center px-4 sm:px-6 lg:px-8 page-enter">

    <!-- Header -->
    <div class="w-full max-w-4xl mt-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-white text-center Lao-font mb-2">E-Tipitaka</h1>
        <p class="text-white/70 text-sm text-center Lao-font">ຄົ້ນຫາພຣະໄຕຣປິດກ ຫຼາຍສະບັບ</p>
    </div>

    <!-- Category Selector -->
    <div class="w-full max-w-4xl mb-4">
        <div class="flex flex-wrap gap-2 justify-center">
            <?php foreach ($categories as $cat): ?>
                <button @click="selectCategory('<?= $cat['code'] ?>')"
                        :class="currentCode === '<?= $cat['code'] ?>' ? 'bg-[#795548] text-white shadow-md' : 'bg-white/80 text-gray-700 hover:bg-white'"
                        class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 Lao-font">
                    <?= htmlspecialchars($cat['label']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="w-full max-w-lg mx-auto mb-4">
        <div class="relative">
            <input type="search"
                   x-model="searchTerm"
                   @input.debounce.500ms="performSearch()"
                   placeholder="ຄົ້ນຫາໃນ E-Tipitaka..."
                   class="w-full bg-white/90 backdrop-blur-md border-none rounded-lg py-2.5 pl-9 pr-3 text-sm shadow-lg focus:ring-2 focus:ring-brown-500 outline-none transition-all Lao-font">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="isSearching" class="flex justify-center p-8">
        <div class="loader"></div>
    </div>

    <!-- No Results -->
    <div x-show="!isSearching && hasSearched && Object.keys(groupedResults).length === 0"
         class="text-center py-10 w-full max-w-2xl">
        <p class="text-white text-xl font-bold Lao-font">ບໍ່ພົບຂໍ້ມູນ</p>
    </div>

    <!-- Results by Volume -->
    <div x-show="!isSearching && Object.keys(groupedResults).length > 0"
         class="w-full max-w-3xl mb-20 space-y-6">

        <template x-for="(items, volume) in groupedResults" :key="volume">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-[#795548] text-white px-4 py-2 font-bold Lao-font text-lg"
                     x-text="'ເຫຼັ້ມທີ່ ' + volume"></div>
                <div class="divide-y divide-gray-100">
                    <template x-for="item in items" :key="item.volume + '-' + item.page">
                        <a :href="'<?= url('/etipitaka') ?>/' + currentCode + '/' + item.volume + '/' + item.page"
                           class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                            <div class="text-sm font-semibold text-gray-800 Lao-font mb-1" x-text="item.title"></div>
                            <p class="text-xs text-gray-500 line-clamp-2" x-html="buildExcerpt(item.content || item.excerpt || '', searchTerm)"></p>
                        </a>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Initial state -->
    <div x-show="!hasSearched && !isSearching" class="text-center py-16 w-full max-w-2xl">
        <p class="text-white/60 text-lg Lao-font">ພິມຄຳຄົ້ນຫາ ຢ່າງໜ້ອຍ 2 ຕົວອັກສອນ</p>
    </div>
</section>
