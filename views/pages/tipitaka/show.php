<section class="page-enter max-w-4xl mx-auto px-4 py-4">
    <!-- Navigation -->
    <div class="flex items-center gap-2 mb-4 text-sm">
        <a href="<?= url('/etipitaka') ?>" class="text-white/70 hover:text-white transition-colors Lao-font">E-Tipitaka</a>
        <span class="text-white/40">/</span>
        <span class="text-white/90 font-medium Lao-font"><?= htmlspecialchars($label) ?></span>
        <span class="text-white/40">/</span>
        <span class="text-white font-bold Lao-font">ເຫຼັ້ມທີ່ <?= $volume ?> ຫນ້າ <?= $page ?></span>
    </div>

    <!-- Content Card -->
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-[#795548] text-white px-4 sm:px-6 py-3">
            <h1 class="text-lg sm:text-xl font-bold Lao-font">ເຫຼັ້ມທີ່ <?= $volume ?> ຫນ້າ <?= $page ?></h1>
            <p class="text-sm text-white/70 Lao-font"><?= htmlspecialchars($label) ?></p>
        </div>
        <div class="p-4 sm:p-6">
            <div class="prose max-w-none Lao-font text-base leading-relaxed break-words" style="font-family: 'Phetsarath', 'Noto Sans Lao', sans-serif; white-space: pre-wrap; word-break: break-word;">
                <?= htmlspecialchars($content['content']) ?>
            </div>
        </div>
    </div>

    <!-- Page Navigation -->
    <div class="flex items-center justify-between mt-4 gap-3">
        <?php if ($prevPage): ?>
            <a href="<?= url('/etipitaka/' . $code . '/' . $volume . '/' . $prevPage) ?>" class="flex-1 bg-white/90 backdrop-blur-md hover:bg-white text-gray-800 text-center py-3 rounded-xl font-medium shadow-md transition-all hover:shadow-lg Lao-font text-sm">
                ← ຫນ້າ <?= $prevPage ?>
            </a>
        <?php else: ?>
            <div class="flex-1"></div>
        <?php endif; ?>

        <span class="text-white/70 text-sm Lao-font"><?= $page ?> / <?= $totalPages ?></span>

        <?php if ($nextPage): ?>
            <a href="<?= url('/etipitaka/' . $code . '/' . $volume . '/' . $nextPage) ?>" class="flex-1 bg-white/90 backdrop-blur-md hover:bg-white text-gray-800 text-center py-3 rounded-xl font-medium shadow-md transition-all hover:shadow-lg Lao-font text-sm">
                ຫນ້າ <?= $nextPage ?> →
            </a>
        <?php else: ?>
            <div class="flex-1"></div>
        <?php endif; ?>
    </div>
</section>
