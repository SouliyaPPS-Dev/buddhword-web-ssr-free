<section class="page-enter max-w-4xl mx-auto px-4 py-4">
    <!-- Navigation -->
    <div class="flex items-center gap-2 mb-4 text-sm">
        <a href="<?= url('/anakame') ?>" class="text-white/70 hover:text-white transition-colors Lao-font">ອານາຄົມສູດ</a>
        <span class="text-white/40">/</span>
        <span class="text-white font-bold Lao-font"><?= htmlspecialchars(mb_substr($title, 0, 80)) ?></span>
    </div>

    <!-- Content Card -->
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-[#795548] text-white px-4 sm:px-6 py-3">
            <h1 class="text-lg sm:text-xl font-bold Lao-font"><?= htmlspecialchars($title) ?></h1>
        </div>
        <div class="p-4 sm:p-6 Lao-font text-base leading-relaxed break-words" style="font-family: 'Phetsarath', 'Noto Sans Lao', sans-serif;">
            <?php if ($content): ?>
                <?= $content ?>
            <?php else: ?>
                <p class="text-gray-500">ບໍ່ສາມາດໂຫຼດເນື້ອຫາໄດ້</p>
            <?php endif; ?>
        </div>
    </div>
</section>
