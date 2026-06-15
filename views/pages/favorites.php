<section class="flex flex-col items-center justify-center mb-4 p-2 sm:p-4" x-data="{ favorites: JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]'), init() { window.addEventListener('sync-complete', () => { const sutras = JSON.parse(localStorage.getItem('buddhaword_sutras') || '[]'); const favs = JSON.parse(localStorage.getItem('buddhaword_favorites') || '[]'); this.favorites = favs.map(fav => { const updated = sutras.find(s => s.ID === fav.ID); return updated || fav; }); }); } }">
    <h1 class="text-xl sm:text-2xl font-bold text-[#795548] mb-6 bg-white/80 px-4 py-2 rounded-xl shadow-sm Lao-font">ລາຍການທີ່ຖືກໃຈ</h1>

    <div class="flex flex-col gap-3 sm:gap-4 w-full max-w-4xl">
        <template x-if="favorites.length > 0">
            <div class="flex justify-end">
                <button @click="if(confirm('ຕ້ອງການລ້າງລາຍການທີ່ຖືກໃຈທັງໝົດ?')) { favorites = []; localStorage.setItem('buddhaword_favorites', '[]'); window.dispatchEvent(new CustomEvent('sync-complete')); }" class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors Lao-font">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    ລ້າງທັງໝົດ
                </button>
            </div>
        </template>
        <template x-if="favorites.length === 0">
            <div class="text-center py-16 sm:py-20 bg-white/50 backdrop-blur-sm rounded-2xl sm:rounded-3xl border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <p class="text-gray-500 text-base sm:text-lg Lao-font">ຍັງບໍ່ມີລາຍການທີ່ຖືກໃຈ</p>
                <a href="<?= url('/sutra') ?>" class="mt-4 inline-block px-6 py-2 bg-[#795548] text-white rounded-xl font-bold Lao-font transition-transform hover:scale-105 active:scale-95">ໄປເບິ່ງພຣະສູດ</a>
            </div>
        </template>

        <template x-for="item in favorites" :key="item.ID || item.id || item.href">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-3 sm:p-4 flex justify-between items-center gap-3 sm:gap-4">
                    <!-- Sutra items -->
                    <template x-if="item['ຊື່ພຣະສູດ']">
                        <a :href="'<?= url('/sutra/details/') ?>' + (item.ID || '')" class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight Lao-font truncate sm:whitespace-normal" x-text="item['ຊື່ພຣະສູດ']"></h3>
                            <p class="text-[10px] sm:text-sm text-gray-500 mt-1 Lao-font" x-text="item['ໝວດທັມ']"></p>
                        </a>
                    </template>
                    <!-- E-Tipitaka items -->
                    <template x-if="!item['ຊື່ພຣະສູດ'] && item.code">
                        <a :href="item.url" class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight Lao-font truncate sm:whitespace-normal" x-text="item.title"></h3>
                            <p class="text-[10px] sm:text-sm text-gray-500 mt-1 Lao-font">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs">E-Tipitaka</span>
                                <span class="ml-1" x-text="'ເຫຼັ້ມທີ່ ' + item.volume + ' ຫນ້າ ' + item.page"></span>
                            </p>
                        </a>
                    </template>
                    <!-- Anakame items -->
                    <template x-if="!item['ຊື່ພຣະສູດ'] && item.href && !item.code">
                        <a :href="item.href" class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight Lao-font truncate sm:whitespace-normal" x-text="item.title"></h3>
                            <p class="text-[10px] sm:text-sm text-gray-500 mt-1 Lao-font">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs">ອະນະຄຳ</span>
                            </p>
                        </a>
                    </template>
                    <!-- PDF Book items -->
                    <template x-if="!item['ຊື່ພຣະສູດ'] && !item.code && !item.href && item.url">
                        <a :href="item.url" class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight Lao-font truncate sm:whitespace-normal" x-text="item.title"></h3>
                            <p class="text-[10px] sm:text-sm text-gray-500 mt-1 Lao-font">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs">ປຶ້ມ</span>
                            </p>
                        </a>
                    </template>
                    <!-- Uttayarndham items -->
                    <template x-if="!item['ຊື່ພຣະສູດ'] && !item.code && !item.href && !item.url">
                        <a :href="item.id ? '/' + item.id : '#'" class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight Lao-font truncate sm:whitespace-normal" x-text="item.title"></h3>
                            <p class="text-[10px] sm:text-sm text-gray-500 mt-1 Lao-font">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs">ອຸດທະຍອນທັມ</span>
                            </p>
                        </a>
                    </template>
                    
                    <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                        <button @click="favorites = favorites.filter(function(f) { return (f.ID || f.id || f.href) !== (item.ID || item.id || item.href); }); localStorage.setItem('buddhaword_favorites', JSON.stringify(favorites))" class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.657 0L10 6.343l1.172-1.171a4 4 0 115.657 5.657L10 18.343l-8.686-8.686a4 4 0 010-5.657z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</section>
