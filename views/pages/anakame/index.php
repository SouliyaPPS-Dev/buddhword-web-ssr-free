<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('anakameSearch', () => ({
        items: <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>,
        filteredItems: <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>,
        searchQuery: '<?= htmlspecialchars($query, ENT_QUOTES) ?>',
        displayCount: 20,
        pageSize: 20,

        get visibleItems() {
            return this.filteredItems.slice(0, this.displayCount);
        },

        loadMore() {
            this.displayCount = Math.min(this.displayCount + this.pageSize, this.filteredItems.length);
        },

        filterItems() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) {
                this.filteredItems = this.items;
            } else {
                this.filteredItems = this.items.filter(item =>
                    item.title.toLowerCase().includes(q)
                );
            }
            this.displayCount = 20;
        },

        highlight(text) {
            if (!text || !this.searchQuery.trim()) return this.escapeHtml(text);
            const escaped = this.searchQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(' + escaped + ')', 'gi');
            return this.escapeHtml(text).replace(regex, '<span class="bg-yellow-200 font-bold text-black px-0.5">$1</span>');
        },

        escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    }));
});
</script>

<section x-data="anakameSearch" class="flex flex-col items-center px-4 sm:px-6 lg:px-8 page-enter">

    <!-- Header -->
    <div class="w-full max-w-4xl mt-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-white text-center Lao-font">ອານາຄົມສູດ (ພາສາໄທ)</h1>
        <p class="text-white/70 text-sm text-center Lao-font">Anakame - ພຣະສູດພາສາໄທ</p>
    </div>

    <!-- Search -->
    <div class="w-full max-w-lg mx-auto mb-4">
        <div class="relative">
            <input type="search"
                   x-model="searchQuery"
                   @input.debounce.300ms="filterItems()"
                   placeholder="ຄົ້ນຫາອານາຄົມສູດ..."
                   class="w-full bg-white/90 backdrop-blur-md border-none rounded-lg py-2.5 pl-9 pr-3 text-sm shadow-lg focus:ring-2 focus:ring-brown-500 outline-none transition-all Lao-font">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="w-full max-w-3xl mb-20">
        <div class="grid gap-2">
            <template x-for="(item, index) in visibleItems" :key="item.href + index">
                <a :href="'<?= url('/anakame/read') ?>?href=' + encodeURIComponent(item.href)"
                   class="bg-white/95 backdrop-blur-md hover:bg-white rounded-xl px-4 py-3 shadow-md transition-all hover:shadow-lg flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-[#795548] text-white flex items-center justify-center text-xs font-bold"
                          x-text="index + 1"></span>
                    <span class="text-sm font-medium text-gray-800 Lao-font" x-html="highlight(item.title)"></span>
                </a>
            </template>
        </div>

        <!-- Load More -->
        <div x-show="displayCount < filteredItems.length" class="text-center mt-4">
            <button @click="loadMore()"
                    class="bg-white/90 hover:bg-white text-gray-800 px-6 py-2 rounded-xl font-medium shadow-md transition-all hover:shadow-lg Lao-font text-sm">
                ໂຫຼດເພີ່ມເຕີມ...
            </button>
        </div>

        <!-- Empty -->
        <div x-show="filteredItems.length === 0" class="text-center py-10">
            <p class="text-white text-xl font-bold Lao-font">ບໍ່ພົບຂໍ້ມູນ</p>
        </div>
    </div>
</section>
