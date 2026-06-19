<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('uttayarndhamSearch', () => ({
        items: <?= json_encode(array_values($items), JSON_UNESCAPED_UNICODE) ?>,
        allItems: <?= json_encode(array_values($items), JSON_UNESCAPED_UNICODE) ?>,
        filteredItems: <?= json_encode(array_values($items), JSON_UNESCAPED_UNICODE) ?>,
        searchQuery: '<?= htmlspecialchars($query, ENT_QUOTES) ?>',
        isLoadingMore: false,
        currentPage: 0,
        hasMore: <?= count($items) >= 20 ? 'true' : 'false' ?>,
        observer: null,

        init() {
            this.$nextTick(() => this.setupScroll());
        },

        setupScroll() {
            if (this.observer) this.observer.disconnect();
            this.observer = new IntersectionObserver((entries) => {
                const sentinel = document.getElementById('scroll-sentinel');
                if (entries[0].isIntersecting && sentinel?.dataset.active === 'true') {
                    this.loadMore();
                }
            }, { rootMargin: '200px' });
            this.$nextTick(() => {
                const sentinel = document.getElementById('scroll-sentinel');
                if (sentinel) this.observer.observe(sentinel);
            });
        },

        async loadMore() {
            if (this.isLoadingMore || !this.hasMore) return;
            this.isLoadingMore = true;
            const nextPage = this.currentPage + 1;
            try {
                const resp = await fetch('<?= url('/api/uttayarndham/list') ?>?page=' + nextPage);
                const data = await resp.json();
                if (data.items && data.items.length > 0) {
                    const existingUrls = new Set(this.allItems.map(i => i.url));
                    const newItems = data.items.filter(i => !existingUrls.has(i.url));
                    if (newItems.length > 0) {
                        this.allItems = this.allItems.concat(newItems);
                        this.filteredItems = this.searchQuery.trim()
                            ? this.allItems.filter(item => item.title.toLowerCase().includes(this.searchQuery.trim().toLowerCase()))
                            : this.allItems;
                        this.currentPage = nextPage;
                        this.hasMore = data.hasMore;
                    } else {
                        this.hasMore = false;
                    }
                } else {
                    this.hasMore = false;
                }
            } catch(e) {
                console.error('Load more failed', e);
            } finally {
                this.isLoadingMore = false;
            }
        },

        filterItems() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) {
                this.filteredItems = this.allItems;
            } else {
                this.filteredItems = this.allItems.filter(item =>
                    item.title.toLowerCase().includes(q)
                );
            }
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

<section x-data="uttayarndhamSearch" class="flex flex-col items-center px-4 sm:px-6 lg:px-8 page-enter">

    <div class="w-full max-w-4xl mt-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-white text-center Lao-font">ອຸດທະຍານທັມ (ທັມມະ)</h1>
        <p class="text-white/70 text-sm text-center Lao-font">Uttayarndham - ບົດຄວາມທັມມະ</p>
    </div>

    <div class="w-full max-w-lg mx-auto mb-4">
        <div class="relative">
            <input type="search"
                   x-model="searchQuery"
                   @input.debounce.300ms="filterItems()"
                   placeholder="ຄົ້ນຫາຫົວຂໍ້..."
                   class="w-full bg-white/90 backdrop-blur-md border-none rounded-lg py-2.5 pl-9 pr-3 text-sm shadow-lg focus:ring-2 focus:ring-brown-500 outline-none transition-all Lao-font">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="w-full max-w-3xl mb-20">
        <div class="grid gap-1.5">
            <template x-for="(item, index) in filteredItems" :key="item.url + index">
                <a :href="'<?= url('/uttayarndham/tag') ?>?url=' + encodeURIComponent(item.url)"
                   class="bg-white/95 backdrop-blur-md hover:bg-white rounded-xl px-4 py-2.5 shadow-md transition-all hover:shadow-lg flex items-center gap-3">
                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-[#795548] text-white flex items-center justify-center text-xs font-bold"
                          x-text="index + 1"></span>
                    <div>
                        <span class="text-sm font-medium text-gray-800 Lao-font" x-html="highlight(item.title)"></span>
                    </div>
                </a>
            </template>
        </div>

        <div class="text-center mt-4">
            <button @click="loadMore()"
                    x-show="hasMore && !isLoadingMore && !searchQuery.trim()"
                    class="bg-white/90 hover:bg-white text-gray-800 px-6 py-2 rounded-xl font-medium shadow-md transition-all hover:shadow-lg Lao-font text-sm">
                ໂຫຼດເພີ່ມເຕີມ...
            </button>
            <div x-show="isLoadingMore" class="flex justify-center p-4">
                <div class="loader"></div>
            </div>
            <div x-show="!hasMore && filteredItems.length > 0 && !isLoadingMore" class="text-center py-4">
                <p class="text-white/50 text-sm Lao-font">— ທັງໝົດ —</p>
            </div>
            <div id="scroll-sentinel" data-active="true" class="h-1"></div>
        </div>

        <div x-show="filteredItems.length === 0 && !isLoadingMore" class="text-center py-10">
            <p class="text-white text-xl font-bold Lao-font">ບໍ່ພົບຂໍ້ມູນ</p>
        </div>
    </div>
</section>
