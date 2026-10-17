<?php /* Full-screen slide deck: copied from cv/views/pages/timeline.php (slides 8 → end). Rendered without the site layout. */ ?>
<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= htmlspecialchars($seo['title']) ?></title>
  <meta name="description" content="<?= htmlspecialchars($seo['description']) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($seo['title']) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($seo['description']) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($seo['image']) ?>">
  <meta name="theme-color" content="#05060d">
  <link rel="canonical" href="<?= htmlspecialchars($seo['canonical']) ?>">
  <link rel="icon" type="image/png" href="<?= url('assets/images/logo.png') ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
<style>
  html, body { margin: 0; height: 100%; background: #05060d; overflow: hidden; }
  [x-cloak] { display: none !important; }

  .deck { --gold: #d4af37; --gold-2: #f5d98b; background: radial-gradient(1200px 600px at 85% -10%, rgba(212,175,55,.14), transparent 60%), radial-gradient(900px 500px at -10% 110%, rgba(99,102,241,.16), transparent 60%), linear-gradient(160deg, #070a18 0%, #05060d 60%, #0d0b07 100%); color: #f4f1ea; }
  .deck { display: flex; flex-direction: column; height: max(620px, calc(100dvh - 190px)); font-family: 'Noto Sans Lao', 'Plus Jakarta Sans', sans-serif; }
  .deck.presenting { position: fixed; inset: 0; z-index: 100; height: 100dvh; border-radius: 0; border: 0; }
  /* Night sky behind every slide — kept dim and slow so it soothes rather than distracts */
  .sky { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
  .sky-milkyway { position: absolute; left: -20%; right: -20%; top: 30%; height: 38%; transform: rotate(-18deg); filter: blur(36px); opacity: .22;
    background: radial-gradient(ellipse at 30% 50%, rgba(160,170,255,.14), transparent 60%), radial-gradient(ellipse at 65% 45%, rgba(245,217,139,.07), transparent 55%), linear-gradient(90deg, transparent, rgba(200,205,255,.05) 30%, rgba(255,240,210,.05) 60%, transparent); }
  .sky-layer { position: absolute; top: 0; left: 0; border-radius: 9999px; background: transparent; }
  .sky-dust { width: 1px; height: 1px; opacity: .3; }
  .sky-mid { width: 1.5px; height: 1.5px; animation: sky-breathe 14s ease-in-out -4s infinite alternate; }
  .sky-bright { width: 1.5px; height: 1.5px; animation: sky-breathe 18s ease-in-out -9s infinite alternate; }
  @keyframes sky-breathe { from { opacity: .25; } to { opacity: .5; } }
  .twinkle { position: absolute; border-radius: 9999px; background: var(--c); box-shadow: 0 0 4px 0 var(--c); animation: twinkle var(--d) ease-in-out var(--delay) infinite; }
  @keyframes twinkle { 0%, 100% { opacity: .12; } 50% { opacity: .55; } }
  @media (prefers-reduced-motion: reduce) { .sky * { animation: none !important; } }
  .slide-stage { flex: 1; min-height: 0; position: relative; }
  .slide { position: absolute; inset: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; scrollbar-color: rgba(212,175,55,.45) transparent; }
  .slide > * { margin: auto; zoom: var(--z, 1); }
  /* The deck handles pinch itself, so the browser never zooms the page out of fullscreen */
  .deck { touch-action: pan-x pan-y; }
  .deck-controls { background: linear-gradient(to top, rgba(5,6,13,.96) 55%, rgba(5,6,13,0)); }
  .lux-title { font-weight: 800; line-height: 1.25; }
  .lux-gold { background: linear-gradient(100deg, #b8892b 0%, #f5d98b 45%, #d4af37 70%, #a87a1f 100%); -webkit-background-clip: text; background-clip: text; color: transparent; }
  .lux-line { height: 1px; background: linear-gradient(90deg, transparent, rgba(212,175,55,.7), transparent); }
  .lux-card { background: linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.015)); border: 1px solid rgba(212,175,55,.18); backdrop-filter: blur(14px); }
  .lux-chip { border: 1px solid rgba(212,175,55,.35); color: var(--gold-2); background: rgba(212,175,55,.08); }
  .lux-btn { background: linear-gradient(120deg, #b8892b, #f5d98b 50%, #c99a32); color: #14110a; }
  .kicker { letter-spacing: .08em; }
  .rise { animation: rise .7s cubic-bezier(.16,1,.3,1) both; }
  @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }

  /* Two layers: measured fact (science, cool blue) vs. analogy for reflection (dharma, gold) */
  .layer { display: inline-flex; align-items: center; gap: .45rem; padding: .3rem .8rem; border-radius: 9999px; font-size: 11px; font-weight: 800; letter-spacing: .04em; }
  .layer-sci { color: #7dd3fc; background: rgba(125,211,252,.10); border: 1px solid rgba(125,211,252,.45); }
  .layer-dharma { color: #f5d98b; background: rgba(212,175,55,.10); border: 1px dashed rgba(212,175,55,.6); }
  .sci-card { background: linear-gradient(160deg, rgba(125,211,252,.08), rgba(125,211,252,.02)); border: 1px solid rgba(125,211,252,.28); }
  .sci-num { font-variant-numeric: tabular-nums; color: #e0f2fe; }
  .src { font-size: 10.5px; color: rgba(255,255,255,.45); }
  .src a { color: rgba(125,211,252,.85); text-decoration: underline; text-underline-offset: 2px; }
  .src-dharma a { color: rgba(245,217,139,.85); }

  /* Light travelling along a path */
  .photon { position: absolute; top: 50%; width: 10px; height: 10px; margin-top: -5px; border-radius: 9999px; background: #f5d98b; box-shadow: 0 0 14px 4px rgba(245,217,139,.7); animation: travel 2.6s linear infinite; }
  @keyframes travel { from { left: 0; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } to { left: calc(100% - 10px); opacity: 0; } }
  /* Moments of mind arising and ceasing */
  .moment { animation: blink 2.4s ease-in-out infinite; }
  @keyframes blink { 0%, 100% { opacity: .12; transform: scale(.7); } 12% { opacity: 1; transform: scale(1); } 30% { opacity: .12; transform: scale(.7); } }

  /* Lute strings */
  .vibrate { animation: vibrate .09s linear infinite alternate; }
  @keyframes vibrate { from { transform: translateY(-2px); } to { transform: translateY(2px); } }
  .note { animation: note 2.4s ease-out infinite; }
  @keyframes note { from { opacity: 0; transform: translateY(10px); } 30% { opacity: 1; } to { opacity: 0; transform: translateY(-12px); } }
  .shake { animation: shake .35s ease-in-out infinite; }
  @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-2px); } 75% { transform: translateX(2px); } }

  /* SN 12.61 monkey: holds one branch (bright), lets go (dim), the next one takes over. */
  .grab { animation: grab 2.4s ease-in-out infinite; opacity: .3; }
  @keyframes grab { 0%, 100% { opacity: .3; transform: translate(-50%, 0) scale(.85); } 10%, 28% { opacity: 1; transform: translate(-50%, -4px) scale(1.1); filter: drop-shadow(0 0 8px rgba(245,217,139,.7)); } 40% { opacity: .3; transform: translate(-50%, 0) scale(.85); } }
  @keyframes blink { 0%, 100% { opacity: .12; transform: scale(.7); } 12% { opacity: 1; transform: scale(1); } 30% { opacity: .12; transform: scale(.7); } }

  @media print {
    @page { size: A4 landscape; margin: 0; }
    header, footer, .no-print, [x-data="chatbot()"], .fixed { display: none !important; }
    body, main, #timeline { background: #05060d !important; padding: 0 !important; margin: 0 !important; }
    .deck { display: block !important; height: auto !important; position: static !important; border-radius: 0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .deck .slide { display: flex !important; position: relative !important; overflow: visible !important; min-height: 100vh; break-after: page; }
    .rise, .photon, .moment, .vibrate, .note, .shake { animation: none !important; }
  }
</style>
</head>
<body>
<section id="timeline" lang="lo" x-data="timelineDeck()" x-init="init()" @keydown.window="onKey($event)">
    <!-- Deck -->
    <div x-ref="deck" class="deck presenting overflow-hidden"
         @touchstart.passive="tStart($event)" @touchend="onSwipe($event)">

      <!-- Night sky: seeded so the stars stay in place between visits -->
      <?php
      mt_srand(2026);
      $starColors = ['#e8ecf5', '#e8ecf5', '#e8ecf5', '#e9dcb4', '#c9d6ee'];
      $shadows = function (int $n, string $blur) use ($starColors) {
          $out = [];
          for ($s = 0; $s < $n; $s++) {
              $out[] = mt_rand(0, 2400) . 'px ' . mt_rand(0, 1500) . 'px ' . $blur . ' ' . $starColors[mt_rand(0, 4)];
          }
          return implode(',', $out);
      };
      $twinkles = [];
      for ($s = 0; $s < 16; $s++) {
          $twinkles[] = [mt_rand(10, 990) / 10, mt_rand(10, 990) / 10, mt_rand(12, 22) / 10, $starColors[mt_rand(0, 4)], mt_rand(80, 140) / 10, -mt_rand(0, 140) / 10];
      }
      ?>
      <div class="sky no-print" aria-hidden="true">
        <div class="sky-milkyway"></div>
        <div class="sky-layer sky-dust" style="box-shadow: <?php echo $shadows(170, '0'); ?>"></div>
        <div class="sky-layer sky-mid" style="box-shadow: <?php echo $shadows(50, '1px'); ?>"></div>
        <div class="sky-layer sky-bright" style="box-shadow: <?php echo $shadows(14, '2px'); ?>"></div>
        <?php foreach ($twinkles as $t): ?>
        <span class="twinkle" style="left:<?php echo $t[0]; ?>%;top:<?php echo $t[1]; ?>%;width:<?php echo $t[2]; ?>px;height:<?php echo $t[2]; ?>px;--c:<?php echo $t[3]; ?>;--d:<?php echo $t[4]; ?>s;--delay:<?php echo $t[5]; ?>s"></span>
        <?php endforeach; ?>
      </div>
      <?php mt_srand(); ?>

      <!-- Top bar -->
      <div class="no-print relative z-30 flex-none flex items-center justify-between gap-3 px-4 sm:px-10 pt-[calc(1rem+env(safe-area-inset-top))] sm:pt-6 pb-2 text-[11px] font-bold text-[#c9b98a]/70">
        <a href="<?= url('/book') ?>" class="flex items-center gap-2 min-w-0 hover:text-[#f5d98b] transition-colors" title="ກັບໄປໜ້າປຶ້ມ">
          <span class="w-9 h-9 shrink-0 rounded-full lux-card flex items-center justify-center text-[#f5d98b]"><i class="fas fa-arrow-left"></i></span>
          <span class="truncate">ຄຳສອນພຸດທະ<span class="text-[#d4af37]">.</span> — <span x-text="titles[i]"></span></span>
        </a>
        <div class="flex items-center gap-1.5 sm:gap-3">
          <span class="whitespace-nowrap"><span x-text="String(i+1).padStart(2,'0')"></span> / <span x-text="String(total).padStart(2,'0')"></span></span>
          <div class="flex items-center rounded-full lux-card text-[#f5d98b]">
            <button data-fs @click="zoomBy(-1)" :disabled="scale<=minScale" class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-[#d4af37]/20 disabled:opacity-30 transition-all" title="ຫຍໍ້ອອກ (Ctrl −)" aria-label="ຫຍໍ້ອອກ"><i class="fas fa-magnifying-glass-minus"></i></button>
            <button data-fs @click="zoomTo(1)" class="hidden sm:block w-11 text-center tabular-nums hover:text-white transition-colors" title="ຂະໜາດປົກກະຕິ (Ctrl 0)" x-text="Math.round(scale*100) + '%'"></button>
            <button data-fs @click="zoomBy(1)" :disabled="scale>=maxScale" class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-[#d4af37]/20 disabled:opacity-30 transition-all" title="ຂະຫຍາຍ (Ctrl +)" aria-label="ຂະຫຍາຍ"><i class="fas fa-magnifying-glass-plus"></i></button>
          </div>
          <div class="flex items-center rounded-full lux-card p-0.5">
            <button @click="toggleNarration()" class="h-8 pl-2.5 pr-3 rounded-full flex items-center gap-1.5 text-[11px] font-black transition-all"
                    :class="narr.on && !narr.paused ? 'lux-btn' : 'text-[#f5d98b] hover:bg-[#d4af37]/20'" :title="t('narrate')" :aria-label="narr.on && !narr.paused ? t('pause') : t('play')">
              <i class="fas" :class="narr.on && !narr.paused ? 'fa-pause' : 'fa-play'"></i><span x-text="narr.on && !narr.paused ? t('pause') : t('play')"></span>
            </button>
            <button data-fs @click="setNarrLang(narr.lang === 'lo' ? 'th' : 'lo')" class="px-2.5 h-8 rounded-full text-[11px] font-black text-[#f5d98b]" :title="t('narrLang')" x-text="NARR_LABELS[narr.lang]"></button>
          </div>
          <button @click="share()" class="w-9 h-9 rounded-full lux-card flex items-center justify-center text-[#f5d98b] hover:bg-[#d4af37]/20 transition-all" title="ແບ່ງປັນລິ້ງ"><i class="fas fa-share-nodes"></i></button>
          <button data-fs @click="toggleFullscreen()" class="w-9 h-9 rounded-full lux-card flex items-center justify-center text-[#f5d98b] hover:bg-[#d4af37]/20 transition-all" :title="fullscreen ? 'ອອກຈາກເຕັມຈໍ' : 'ເຕັມຈໍ'">
            <i class="fas" :class="fullscreen ? 'fa-compress' : 'fa-expand'"></i>
          </button>
        </div>
      </div>

      <div class="slide-stage" :style="`--z:${scale}`">
        <?php
        // Layer badges: each "time & mind" slide states up front whether it shows measured fact or an analogy for reflection.
        $sciTag = '<span class="layer layer-sci"><i class="fas fa-flask"></i>ວິທະຍາສາດ · ຂໍ້ເທັດຈິງ</span>';
        $dharmaTag = '<span class="layer layer-dharma"><i class="fas fa-dharmachakra"></i>ທຳມະ · ອຸປະມາ ເພື່ອພິຈາລະນາ</span>';
        ?>

        <!-- 4. The present we feel — flower example -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===0" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 1</p><?php echo $sciTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">“ປັດຈຸບັນ” ທີ່ເຮົາຮັບຮູ້ <span class="lux-gold">ຕ້ອງໃຊ້ເວລາ</span></h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ກວ່າເຮົາຈະຮູ້ວ່າ “ນັ້ນແມ່ນດອກໄມ້” ຕ້ອງຜ່ານຫຼາຍຂັ້ນຕອນ — ການຮັບຮູ້ <b class="text-[#f5d98b]">ບໍ່ໄດ້ເກີດຂຶ້ນລອຍໆ</b> ແຕ່ມີເຫດປັດໄຈມາກ່ອນສະເໝີ.</p>

            <!-- Illustration: object → light → eye → nerves → processing → experience -->
            <div class="sci-card rounded-[28px] p-5 sm:p-8 mt-8 rise">
              <div class="flex flex-wrap sm:flex-nowrap items-center justify-center gap-y-4">
                <?php $steps = [['fa-spa', 'ວັດຖຸ', 'ດອກໄມ້'], ['fa-sun', 'ແສງ', 'ສະທ້ອນຈາກດອກ'], ['fa-eye', 'ຕາ', 'ເຊວຮັບແສງ'], ['fa-wave-square', 'ລະບົບປະສາດ', 'ສົ່ງສັນຍານ'], ['fa-brain', 'ການປະມວນຜົນ', 'ສະໝອງແປຄວາມໝາຍ'], ['fa-comment-dots', 'ປະສົບການ', '“ນີ້ແມ່ນດອກໄມ້”']];
                foreach ($steps as $k => $s): ?>
                <div class="text-center w-1/3 sm:w-auto sm:flex-none">
                  <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-full flex items-center justify-center text-lg sm:text-2xl <?php echo $k === 0 ? 'bg-pink-400/15 text-pink-300 ring-1 ring-pink-300/40' : ($k === 5 ? 'lux-btn' : 'bg-sky-300/10 text-sky-200 ring-1 ring-sky-300/30'); ?>"><i class="fas <?php echo $s[0]; ?>"></i></div>
                  <p class="font-bold text-xs sm:text-sm mt-2"><?php echo $s[1]; ?></p>
                  <p class="text-[10px] sm:text-[11px] text-white/45"><?php echo $s[2]; ?></p>
                </div>
                <?php if ($k < 5): ?>
                <div class="hidden sm:block relative h-1 flex-1 min-w-[18px] rounded-full bg-white/10 mx-1"><span class="photon" style="animation-delay:<?php echo $k * .5; ?>s"></span></div>
                <?php endif; endforeach; ?>
              </div>
              <!-- time axis -->
              <div class="mt-8 relative">
                <div class="h-px bg-gradient-to-r from-pink-300/60 via-sky-300/60 to-[#f5d98b]"></div>
                <div class="flex justify-between text-[11px] sm:text-xs text-white/55 mt-2">
                  <span><i class="fas fa-circle text-[6px] text-pink-300 mr-1"></i>ແສງອອກຈາກດອກໄມ້</span>
                  <span class="text-center">ໃຊ້ເວລາ — ໜ້ອຍຫຼາຍ ແຕ່ບໍ່ແມ່ນສູນ</span>
                  <span class="text-[#f5d98b] font-bold">ເຮົາຮູ້ສຶກວ່າ “ຕອນນີ້” <i class="fas fa-circle text-[6px] ml-1"></i></span>
                </div>
              </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3 mt-5">
              <div class="flex gap-3 items-start sci-card rounded-2xl p-4 rise" style="animation-delay:.3s">
                <i class="fas fa-check text-sky-300 mt-1"></i>
                <p class="text-sm text-white/75 leading-relaxed">ປະສົບການທີ່ເຮົາຮັບຮູ້ວ່າເປັນ “ປັດຈຸບັນ” <b class="text-sky-200">ເກີດຈາກກະບວນການທີ່ຕ້ອງໃຊ້ເວລາ</b>.</p>
              </div>
              <div class="flex gap-3 items-start lux-card rounded-2xl p-4 rise" style="animation-delay:.4s">
                <i class="fas fa-triangle-exclamation text-[#d4af37] mt-1"></i>
                <p class="text-sm text-white/75 leading-relaxed">ແຕ່ <b class="text-[#f5d98b]">ບໍ່ໄດ້ໝາຍຄວາມວ່າ ປັດຈຸບັນບໍ່ມີຢູ່ແທ້</b> — ນັ້ນເປັນຄຳຖາມທາງປັດຊະຍາ ທີ່ຊວນໃຫ້ພິຈາລະນາ ບໍ່ແມ່ນສິ່ງທີ່ວິທະຍາສາດພິສູດ.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. Starlight -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===1" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 2</p><?php echo $sciTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ແນມທ້ອງຟ້າ = <span class="lux-gold">ຮັບຂໍ້ມູນຈາກອະດີດ</span></h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ແສງມີຄວາມໄວຈຳກັດ ຈຶ່ງຕ້ອງໃຊ້ເວລາເດີນທາງ — <b class="text-sky-200">ຍິ່ງວັດຖຸຢູ່ໄກ ເຮົາກໍຍິ່ງແນມຍ້ອນກັບໄປໄກໃນອະດີດ</b>.</p>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,2.2fr)] gap-4 mt-6">
              <!-- Big number: speed of light -->
              <div class="sci-card rounded-[28px] p-5 sm:p-6 rise">
                <p class="text-xs font-bold text-sky-300"><i class="fas fa-bolt mr-1.5"></i>ຄວາມໄວແສງ (ໃນສູນຍາກາດ)</p>
                <p class="sci-num text-3xl sm:text-4xl font-black mt-2">299,792 <span class="text-base sm:text-lg text-sky-200/80">km/s</span></p>
                <p class="text-sm text-white/60 mt-1">ປັດເປັນ <b class="text-sky-200">≈ 300,000 km ຕໍ່ວິນາທີ</b></p>
                <p class="text-xs text-white/45 mt-3">ໄວຫຼາຍ — ແຕ່ <b class="text-white/70">ບໍ່ແມ່ນທັນທີ</b>.</p>
              </div>

              <!-- Distance timeline: Earth ← Moon ← Sun ← Proxima Centauri (not to scale) -->
              <div class="sci-card rounded-[28px] p-4 sm:p-6 rise" style="animation-delay:.1s">
                <!-- Phones: the same three distances as a readable list, light arriving at Earth at the bottom -->
                <div class="sm:hidden space-y-2">
                  <?php foreach ([['Proxima Centauri', '≈ 4.24 ປີແສງ', '4.24 ປີ', 'bg-red-300 shadow-[0_0_12px_#f87171]'], ['ດວງອາທິດ', '≈ 149.6 ລ້ານ km', '8 ນາທີ 20 ວິ', 'bg-amber-200 shadow-[0_0_12px_#f5d98b]'], ['ດວງຈັນ', '≈ 384,400 km', '1.3 ວິນາທີ', 'bg-gray-300']] as $o): ?>
                  <div class="flex items-center gap-3 rounded-2xl bg-white/[.04] px-3 py-2.5">
                    <span class="w-3.5 h-3.5 rounded-full shrink-0 <?php echo $o[3]; ?>"></span>
                    <div class="min-w-0 flex-1"><p class="font-bold text-sm"><?php echo $o[0]; ?></p><p class="text-[11px] text-white/50"><?php echo $o[1]; ?></p></div>
                    <p class="sci-num font-black text-base whitespace-nowrap"><?php echo $o[2]; ?></p>
                  </div>
                  <?php endforeach; ?>
                  <p class="text-center text-[11px] text-sky-300/80"><i class="fas fa-arrow-down mr-1"></i>ເວລາທີ່ແສງໃຊ້ ເດີນທາງມາຮອດ <b>ໂລກ</b> <i class="fas fa-earth-asia ml-1"></i></p>
                </div>
                <svg viewBox="0 0 800 210" class="hidden sm:block w-full h-auto" role="img" aria-label="ເວລາທີ່ແສງໃຊ້ເດີນທາງມາຮອດໂລກ: ດວງຈັນ 1.3 ວິນາທີ, ດວງອາທິດ 8 ນາທີ 20 ວິນາທີ, Proxima Centauri 4.24 ປີ">
                  <defs>
                    <radialGradient id="sunGlow"><stop offset="0%" stop-color="#fff7d6"/><stop offset="45%" stop-color="#f5d98b"/><stop offset="100%" stop-color="#d4af37" stop-opacity="0"/></radialGradient>
                    <radialGradient id="redGlow"><stop offset="0%" stop-color="#ffd2c2"/><stop offset="45%" stop-color="#f87171"/><stop offset="100%" stop-color="#f87171" stop-opacity="0"/></radialGradient>
                  </defs>
                  <line x1="70" y1="95" x2="740" y2="95" stroke="rgba(125,211,252,.35)" stroke-width="2" stroke-dasharray="4 8"/>
                  <!-- scale breaks: not drawn to scale -->
                  <?php foreach ([340, 560] as $bx): ?>
                  <path d="M<?php echo $bx - 6; ?> 84 l8 22 M<?php echo $bx + 2; ?> 84 l8 22" stroke="#7dd3fc" stroke-width="2"/>
                  <?php endforeach; ?>
                  <!-- light arriving at Earth -->
                  <?php foreach ([0, 1.2, 2.4] as $d): ?>
                  <circle cy="95" r="4" fill="#fff3c4" opacity=".9"><animate attributeName="cx" from="740" to="90" dur="3.6s" begin="<?php echo $d; ?>s" repeatCount="indefinite"/></circle>
                  <?php endforeach; ?>
                  <!-- Earth -->
                  <circle cx="60" cy="95" r="22" fill="#1d4ed8"/><path d="M46 86 q8 -6 14 2 q6 8 14 2 M44 100 q10 4 16 -2 q8 -4 16 4" stroke="#4ade80" stroke-width="4" fill="none" stroke-linecap="round"/>
                  <text x="60" y="140" text-anchor="middle" fill="#bae6fd" font-size="15" font-weight="800">ໂລກ</text>
                  <!-- Moon -->
                  <circle cx="210" cy="95" r="11" fill="#d1d5db"/><circle cx="206" cy="91" r="2.5" fill="#9ca3af"/>
                  <text x="210" y="45" text-anchor="middle" fill="#fff" font-size="14" font-weight="700">ດວງຈັນ</text>
                  <text x="210" y="62" text-anchor="middle" fill="#ffffff88" font-size="11">≈ 384,400 km</text>
                  <text x="210" y="140" text-anchor="middle" fill="#e0f2fe" font-size="20" font-weight="900">1.3 ວິນາທີ</text>
                  <!-- Sun -->
                  <circle cx="450" cy="95" r="32" fill="url(#sunGlow)"/><circle cx="450" cy="95" r="15" fill="#fde68a"/>
                  <text x="450" y="45" text-anchor="middle" fill="#fff" font-size="14" font-weight="700">ດວງອາທິດ</text>
                  <text x="450" y="62" text-anchor="middle" fill="#ffffff88" font-size="11">≈ 149.6 ລ້ານ km</text>
                  <text x="450" y="148" text-anchor="middle" fill="#e0f2fe" font-size="20" font-weight="900">8 ນາທີ 20 ວິນາທີ</text>
                  <!-- Proxima Centauri -->
                  <circle cx="710" cy="95" r="26" fill="url(#redGlow)"/><circle cx="710" cy="95" r="8" fill="#fecaca"/>
                  <text x="710" y="45" text-anchor="middle" fill="#fff" font-size="14" font-weight="700">Proxima Centauri</text>
                  <text x="710" y="62" text-anchor="middle" fill="#ffffff88" font-size="11">≈ 4.24 ປີແສງ</text>
                  <text x="710" y="148" text-anchor="middle" fill="#e0f2fe" font-size="20" font-weight="900">4.24 ປີ</text>
                  <text x="400" y="196" text-anchor="middle" fill="#7dd3fcaa" font-size="12">← ເວລາທີ່ແສງໃຊ້ ເດີນທາງມາຮອດໂລກ (ແຜນພາບບໍ່ໄດ້ຕາມມາດຕາສ່ວນ)</text>
                </svg>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-3 mt-4">
              <!-- Light-year is a distance -->
              <div class="sci-card rounded-2xl p-4 rise" style="animation-delay:.2s">
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-ruler-horizontal text-sky-300 mr-2"></i>“ປີແສງ” ແມ່ນ <span class="text-sky-200">ໜ່ວຍໄລຍະທາງ</span> ບໍ່ແມ່ນໜ່ວຍເວລາ</p>
                <p class="text-xs sm:text-sm text-white/60 mt-1.5">= ໄລຍະທາງທີ່ແສງເດີນທາງໄດ້ໃນ 1 ປີ (≈ 9.46 ລ້ານລ້ານ km). Proxima ຢູ່ຫ່າງ 4.24 ປີແສງ → ແສງໃຊ້ເວລາ ≈ 4.24 ປີ ມາຮອດເຮົາ. ດາວທີ່ຢູ່ຫ່າງ 100 ປີແສງ → ແສງທີ່ເຮົາໄດ້ຮັບມື້ນີ້ ອອກຈາກດາວ ≈ 100 ປີກ່ອນ.</p>
              </div>
              <!-- Correct the "dead star" misconception -->
              <div class="lux-card rounded-2xl p-4 rise" style="animation-delay:.3s">
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-star-half-stroke text-[#d4af37] mr-2"></i>ເຮົາເຫັນດາວ <span class="text-[#f5d98b]">ໃນສະພາບທີ່ແສງອອກເດີນທາງມາ</span></p>
                <p class="text-xs sm:text-sm text-white/60 mt-1.5">ບໍ່ແມ່ນສະພາບແບບສົດໆ ໃນຕອນນີ້. ດາວ <b class="text-white/80">ອາດ</b> ປ່ຽນແປງໄປແລ້ວ ຫຼັງແສງອອກເດີນທາງ — ແຕ່ <b class="text-[#f5d98b]">ເຮົາສະຫຼຸບບໍ່ໄດ້ວ່າດາວດັບແລ້ວ</b> ພຽງເພາະເຫັນແສງຈາກອະດີດ.</p>
              </div>
            </div>
            <p class="src mt-4"><i class="fas fa-book-open mr-1"></i>ແຫຼ່ງອ້າງອີງ (ວິທະຍາສາດ):
              <a href="https://science.nasa.gov/exoplanets/what-is-a-light-year/" target="_blank" rel="noopener">NASA — What is a light-year?</a> ·
              <a href="https://science.nasa.gov/mission/webb/science-overview/science-explainers/how-does-webb-see-back-in-time/" target="_blank" rel="noopener">NASA — How Does Webb See Back in Time?</a> ·
              <a href="https://science.nasa.gov/moon/facts/" target="_blank" rel="noopener">NASA — Moon facts</a> ·
              <a href="https://science.nasa.gov/sun/facts/" target="_blank" rel="noopener">NASA — Sun facts</a> ·
              <a href="https://apod.nasa.gov/apod/ap160118.html" target="_blank" rel="noopener">NASA APOD — Proxima Centauri</a> ·
              <a href="https://physics.nist.gov/cgi-bin/cuu/Value?c" target="_blank" rel="noopener">NIST — speed of light</a></p>
          </div>
        </div>

        <!-- 6. Starlight ↔ mind -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===2" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 3</p><?php echo $sciTag; ?><i class="fas fa-arrow-right-long text-white/40"></i><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-2xl sm:text-4xl mt-3 leading-snug">ຖ້າແສງທີ່ເຮົາເຫັນ ມີເຫດປັດໄຈ ແລະ ການເດີນທາງມາກ່ອນ<br><span class="lux-gold">ແລ້ວປະສົບການທາງໃຈຂອງເຮົາເດ?</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base">ເລື່ອງແສງດາວ ໃຊ້ເປັນ <b class="text-[#f5d98b]">ອຸປະມາ</b> ເພື່ອຊວນພິຈາລະນາທຳມະຊາດຂອງການຮັບຮູ້ — ບໍ່ແມ່ນບອກວ່າຈິດເຮັດວຽກຄືແສງ.</p>
            <!-- Analogy table: science fact on the left, dharma reflection on the right -->
            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-x-3 sm:gap-x-5 gap-y-3 mt-6">
              <span class="layer layer-sci justify-self-start"><i class="fas fa-flask"></i>ແສງດາວ</span><span></span><span class="layer layer-dharma justify-self-start"><i class="fas fa-dharmachakra"></i>ການຮັບຮູ້ຂອງຈິດ</span>
              <?php foreach ([
                ['fa-star', 'ດາວ ຕອນແສງອອກເດີນທາງ', 'fa-diagram-project', 'ເຫດປັດໄຈ ທີ່ມີມາກ່ອນ'],
                ['fa-route', 'ແສງເດີນທາງມາຮອດຕາ', 'fa-hourglass-half', 'ສິ່ງທີ່ປາກົດໃຫ້ຮັບຮູ້ ຕອນນີ້'],
                ['fa-eye', 'ເຫັນແສງ — ບໍ່ເຫັນດາວໂດຍກົງ', 'fa-brain', 'ການຮັບຮູ້ (ວິນຍານ) ຜ່ານຂັ້ນຕອນ'],
                ['fa-eye-slash', 'ບໍ່ເຫັນທາງທີ່ແສງເດີນທາງມາ', 'fa-cloud', 'ບໍ່ເຫັນເຫດປັດໄຈ (ອະວິຊາ)'],
              ] as $k => $m): ?>
              <div class="sci-card rounded-2xl p-3 sm:p-4 flex items-center gap-3 rise" style="animation-delay:<?php echo $k * .12; ?>s"><i class="fas <?php echo $m[0]; ?> text-sky-300 w-5 text-center"></i><span class="font-bold text-xs sm:text-base"><?php echo $m[1]; ?></span></div>
              <span class="text-[10px] sm:text-xs text-white/40 text-center">ປຽບ<br>ເໝືອນ</span>
              <div class="rounded-2xl p-3 sm:p-4 flex items-center gap-3 bg-[#d4af37]/10 border border-dashed border-[#d4af37]/45 rise" style="animation-delay:<?php echo $k * .12; ?>s"><i class="fas <?php echo $m[2]; ?> text-[#f5d98b] w-5 text-center"></i><span class="font-bold text-xs sm:text-base"><?php echo $m[3]; ?></span></div>
              <?php endforeach; ?>
            </div>
            <div class="grid md:grid-cols-2 gap-3 mt-5">
              <!-- Everyday example: what is seen vs. what the mind adds -->
              <div class="lux-card rounded-3xl p-4 sm:p-5 rise" style="animation-delay:.5s">
                <p class="text-xs font-bold text-[#d4af37]"><i class="fas fa-person-walking mr-1.5"></i>ຕົວຢ່າງ: ມີຄົນຍ່າງຜ່ານ ບໍ່ທັກເຮົາ</p>
                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs sm:text-sm font-bold">
                  <span class="px-3 py-1.5 rounded-full bg-white/10">ເຫັນ: ເຂົາຍ່າງຜ່ານ</span><i class="fas fa-arrow-right text-[#d4af37]"></i>
                  <span class="px-3 py-1.5 rounded-full bg-[#d4af37]/15 text-[#f5d98b]">ຈິດເຕີມ: “ເຂົາບໍ່ມັກເຮົາ”</span>
                </div>
                <p class="text-xs text-white/55 mt-3">ສິ່ງທີ່ເຫັນ ກັບ ຄວາມໝາຍທີ່ຈິດຕີຄວາມເພີ່ມ — ບໍ່ແມ່ນສິ່ງດຽວກັນ. ເມື່ອຕີຄວາມຕໍ່ວ່າ “ນີ້ແມ່ນເຮົາ ທີ່ຖືກດູຖູກ” ຈິດກໍເລີ່ມ <b class="text-[#f5d98b]">ປຸງແຕ່ງ</b> ແລະ <b class="text-[#f5d98b]">ຍຶດຖື</b>.</p>
              </div>
              <div class="rounded-3xl p-4 sm:p-5 border border-white/15 bg-white/[.03] rise" style="animation-delay:.6s">
                <p class="text-sm sm:text-base text-white/85 leading-relaxed">“ວິທະຍາສາດ ອະທິບາຍວ່າແສງຈາກດາວ <b class="text-sky-200">ເດີນທາງມາຫາເຮົາແນວໃດ</b> — ສ່ວນທຳມະ ຊວນເຮົາພິຈາລະນາວ່າ ປະສົບການ ແລະ ຄວາມທຸກ <b class="text-[#f5d98b]">ເກີດຈາກເຫດປັດໄຈແນວໃດ</b>.”</p>
                <p class="text-xs sm:text-sm text-white/55 mt-2"><i class="fas fa-circle-info text-[#d4af37] mr-1"></i>ເຮົາບໍ່ໄດ້ບອກວ່າສອງສິ່ງນີ້ເຮັດວຽກຄືກັນ — ແສງເປັນເລື່ອງຟີຊິກ, ການຮັບຮູ້ເປັນເລື່ອງຊີວະວິທະຍາ ແລະ ຈິດ. ເຮົາໃຊ້ເລື່ອງໜຶ່ງເປັນອຸປະມາ ເພື່ອຊ່ວຍໃຫ້ເຫັນອີກເລື່ອງໜຶ່ງ.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 7. Mind arises and ceases every moment -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===3" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 4</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ຈິດ <span class="lux-gold">ເກີດ ແລະ ດັບ</span> ທຸກຂະນະ</h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ເຫັນຂໍ້ຄວາມໃນໂທລະສັບ → ດີໃຈ · ນຶກເຖິງອີກ → ດີໃຈອີກ · ນຶກເລື່ອງອື່ນ → ຮູ້ສຶກແບບໃໝ່. ຄວາມຮູ້ສຶກ ຄວາມຄິດ ແລະ ການຮັບຮູ້ <b class="text-[#f5d98b]">ປ່ຽນແປງຢູ່ສະເໝີ</b> — ແຕ່ເມື່ອບໍ່ເຫັນຕາມຈິງ (ອະວິຊາ) ມັນກໍເບິ່ງຄືມີ “ເຮົາ” ຄົນດຽວ ຕໍ່ເນື່ອງຕະຫຼອດ.</p>

            <div class="grid lg:grid-cols-2 gap-4 sm:gap-5 mt-8">
              <!-- What really happens -->
              <div class="lux-card rounded-[28px] p-5 sm:p-6 rise">
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-eye text-[#d4af37] mr-2"></i>ຄວາມເປັນຈິງ: ເກີດ-ດັບ, ເກີດ-ດັບ…</p>
                <div class="flex items-center justify-between gap-1 mt-6 h-12">
                  <?php for ($n = 0; $n < 12; $n++): ?>
                  <span class="moment w-5 h-5 sm:w-7 sm:h-7 rounded-full bg-[#f5d98b] shadow-[0_0_14px_rgba(245,217,139,.8)]" style="animation-delay:<?php echo $n * 0.2; ?>s"></span>
                  <?php endfor; ?>
                </div>
                <p class="text-xs text-white/55 mt-4">ຈິດແຕ່ລະດວງ ເກີດຂຶ້ນ ຮັບຮູ້ ແລ້ວດັບໄປ — ບໍ່ມີດວງໃດຢູ່ຖາວອນ.</p>
              </div>
              <!-- What we believe -->
              <div class="lux-card rounded-[28px] p-5 sm:p-6 rise" style="animation-delay:.15s">
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-cloud text-[#d4af37] mr-2"></i>ເມື່ອມີອະວິຊາ: ເຫັນເປັນ “ຕົວເຮົາ” ຕໍ່ເນື່ອງ</p>
                <div class="relative mt-6 h-12 flex items-center">
                  <div class="w-full h-3 rounded-full bg-gradient-to-r from-[#b8892b] via-[#f5d98b] to-[#b8892b]"></div>
                  <span class="absolute left-1/2 -translate-x-1/2 -top-1 lux-btn px-3 py-1 rounded-full text-xs font-black">“ເຮົາ”</span>
                </div>
                <p class="text-xs text-white/55 mt-4">ເໝືອນຮູບເງົາ: ພາບນິ່ງຫຼາຍພາບສາຍຕໍ່ກັນໄວໆ ຈົນເບິ່ງຄືເປັນສິ່ງດຽວທີ່ເຄື່ອນໄຫວ.</p>
              </div>
            </div>
            <p class="text-xs text-white/45 mt-4 max-w-4xl"><i class="fas fa-circle-info text-[#d4af37] mr-1.5"></i>ການເກີດ-ດັບຂອງຈິດ ເປັນຄຳອະທິບາຍທາງພຸດທະທຳ (ອະນິດຈັງ) — ການປະມວນຜົນຂອງສະໝອງ ເປັນເລື່ອງທີ່ວິທະຍາສາດສຶກສາຕ່າງຫາກ. ທັງສອງກ່ຽວກັບການປ່ຽນແປງ ແຕ່ບໍ່ຄວນຖືວ່າເປັນສິ່ງດຽວກັນ.</p>
            <button @click="go(4)" class="lux-chip mt-3 px-4 py-2 rounded-full text-xs sm:text-sm font-bold"><i class="fas fa-book-open mr-1.5"></i>ພຣະພຸດທະເຈົ້າ ປຽບຈິດທີ່ເກີດ-ດັບນີ້ ກັບ “ລີງຈັບກິ່ງໄມ້” — ເບິ່ງ 3 ອຸປະມາ ຈາກພຣະສູດ <i class="fas fa-arrow-right ml-1"></i></button>
          </div>
        </div>

        <!-- 7b. Three sutta similes: monkey (SN 12.61), fire (MN 38), seed (AN 3.76 · SN 12.38) -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===4" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 5</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-2xl sm:text-4xl mt-3 leading-snug">ຈິດ · ມະໂນ · ວິນຍານ <span class="lux-gold">ໃນ 3 ອຸປະມາ ຈາກພຣະສູດ</span></h2>
            <p class="text-white/65 mt-2 max-w-4xl text-sm sm:text-base">ຕໍ່ຈາກ <button @click="go(3)" class="font-bold text-[#f5d98b] underline underline-offset-2" x-text="titles[3]"></button> (ຕົວຢ່າງເດີມ: ຂໍ້ຄວາມໃນໂທລະສັບ) — ພຣະພຸດທະເຈົ້າ ຊົງໃຊ້ 3 ພາບ ອະທິບາຍວ່າ ຈິດ <b class="text-[#f5d98b]">ເກີດ-ດັບ</b> ແນວໃດ, <b class="text-[#f5d98b]">ອາໄສຫຍັງ</b> ຈຶ່ງເກີດ, ແລະ <b class="text-[#f5d98b]">ເງື່ອນໄຂເກົ່າ</b> ສົ່ງຜົນແນວໃດ. ຫຼັກດຽວກັນນີ້ ໃຊ້ໄດ້ທັງ <b>ຂະນະຈິດໃນຊີວິດປະຈຳວັນ</b> ແລະ <b>ການເກີດຂ້າມພົບ</b>.</p>

            <div class="grid md:grid-cols-3 gap-3 sm:gap-4 mt-5">
              <!-- 1. Monkey: grabs one branch, lets go, grabs the next -->
              <div class="lux-card rounded-[24px] p-4 sm:p-5 flex flex-col rise">
                <div class="flex items-center gap-2"><span class="w-8 h-8 rounded-full lux-btn flex items-center justify-center text-sm font-black">1</span><p class="font-black text-sm sm:text-base">ລີງ ຈັບກິ່ງໄມ້</p></div>
                <div class="relative h-24 mt-3 rounded-2xl bg-white/[.03] border border-white/10 overflow-hidden" aria-hidden="true">
                  <svg viewBox="0 0 300 96" preserveAspectRatio="none" class="absolute inset-0 w-full h-full" fill="none" stroke-linecap="round">
                    <path d="M0 18 H300" stroke="#8b6b3d" stroke-width="5"/>
                    <?php foreach ([50, 150, 250] as $x): ?>
                    <path d="M<?php echo $x; ?> 18 q-6 22 0 40" stroke="#8b6b3d" stroke-width="3"/>
                    <ellipse cx="<?php echo $x - 14; ?>" cy="14" rx="9" ry="4" fill="#4d7c4a"/><ellipse cx="<?php echo $x + 14; ?>" cy="12" rx="9" ry="4" fill="#4d7c4a"/>
                    <?php endforeach; ?>
                  </svg>
                  <?php foreach ([16.7, 50, 83.3] as $k => $x): ?>
                  <span class="grab absolute top-[42%] text-3xl sm:text-4xl" style="left:<?php echo $x; ?>%;animation-delay:<?php echo $k * 0.8; ?>s">🐒</span>
                  <?php endforeach; ?>
                </div>
                <p class="text-[11px] text-center text-white/45 mt-1">ຈັບ → ປ່ອຍ → ຈັບກິ່ງໃໝ່ → ປ່ອຍ…</p>
                <p class="text-xs sm:text-sm text-white/80 mt-2 leading-relaxed"><b class="text-[#d4af37]">ພຣະສູດ (ຖອດຄວາມ):</b> ສິ່ງທີ່ເອີ້ນວ່າ ຈິດ ມະໂນ ວິນຍານ ເກີດຂຶ້ນເປັນອັນໜຶ່ງ ດັບໄປເປັນອີກອັນໜຶ່ງ ທັງກາງເວັນ ແລະ ກາງຄືນ — ເໝືອນລີງໃນປ່າ ຈັບກິ່ງໜຶ່ງ ປ່ອຍ ແລ້ວຈັບກິ່ງໃໝ່.</p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-2"><i class="fas fa-mobile-screen text-sky-300 mr-1"></i><b class="text-sky-200">ໃນຊີວິດ</b>: ເຫັນຂໍ້ຄວາມ → ດີໃຈ → ນຶກເລື່ອງອື່ນ — ແຕ່ລະຂະນະ ຄື “ກິ່ງໃໝ່” ບໍ່ແມ່ນຈິດດວງເກົ່າ ທີ່ຍ້າຍໄປ.</p>
                <p class="mt-auto pt-3 text-xs sm:text-sm font-bold text-[#f5d98b]"><i class="fas fa-arrow-right mr-1.5"></i>ຈິດ ບໍ່ທ່ຽງ: ບໍ່ມີ “ຈິດກ້ອນເດີມ” ຢູ່ຕະຫຼອດ</p>
              </div>

              <!-- 2. Fire is named by its fuel; consciousness by its condition -->
              <div class="lux-card rounded-[24px] p-4 sm:p-5 flex flex-col rise" style="animation-delay:.12s">
                <div class="flex items-center gap-2"><span class="w-8 h-8 rounded-full lux-btn flex items-center justify-center text-sm font-black">2</span><p class="font-black text-sm sm:text-base">ໄຟ ອາໄສເຊື້ອ</p></div>
                <div class="h-24 mt-3 rounded-2xl bg-white/[.03] border border-white/10 grid grid-cols-[auto_auto_1fr] content-center items-center gap-x-2 gap-y-1 px-3 text-[11px] sm:text-xs font-bold">
                  <span>🪵 ໄມ້</span><i class="fas fa-arrow-right text-[#d4af37]"></i><span class="text-orange-300">🔥 “ໄຟໄມ້”</span>
                  <span>🌾 ຫຍ້າ</span><i class="fas fa-arrow-right text-[#d4af37]"></i><span class="text-orange-300">🔥 “ໄຟຫຍ້າ”</span>
                  <span>👁️ ຕາ+ຮູບ</span><i class="fas fa-arrow-right text-[#d4af37]"></i><span class="text-[#f5d98b]">✦ “ຈັກຂຸວິນຍານ”</span>
                </div>
                <p class="text-[11px] text-center text-white/45 mt-1">ໄຟ ເອີ້ນຕາມເຊື້ອ — ວິນຍານ ເອີ້ນຕາມປັດໄຈ</p>
                <p class="text-xs sm:text-sm text-white/80 mt-2 leading-relaxed"><b class="text-[#d4af37]">ພຣະສູດ (ຖອດຄວາມ):</b> ພິກຂຸສາຕິ ເຫັນຜິດວ່າ “ວິນຍານດວງນີ້ແຫຼະ ທ່ອງທ່ຽວໄປ ບໍ່ແມ່ນດວງອື່ນ”. ພຣະພຸດທະເຈົ້າ ແກ້ວ່າ ວິນຍານ ເກີດເພາະອາໄສປັດໄຈ — ບໍ່ມີປັດໄຈ ວິນຍານກໍບໍ່ເກີດ.</p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-2"><i class="fas fa-fire-flame-simple text-orange-300 mr-1"></i><b>ອຸປະມາເສີມ</b>: ຕະກຽງ ລຸກຢູ່ໄດ້ເພາະນ້ຳມັນ ແລະ ໄສ້ — ບໍ່ເຕີມນ້ຳມັນ ກໍດັບເອງ. ໄຟ ບໍ່ໄດ້ຍ້າຍໄປໃສ.</p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-2"><i class="fas fa-mobile-screen text-sky-300 mr-1"></i><b class="text-sky-200">ໃນຊີວິດ</b>: ເບິ່ງຈໍ → ວິນຍານທາງຕາ · ໄດ້ຍິນສຽງແຈ້ງເຕືອນ → ວິນຍານທາງຫູ — ບໍ່ແມ່ນ “ຜູ້ຮູ້” ຄົນດຽວ ຍ້າຍຈາກຕາໄປຫູ.</p>
                <p class="mt-auto pt-3 text-xs sm:text-sm font-bold text-[#f5d98b]"><i class="fas fa-arrow-right mr-1.5"></i>ວິນຍານ ບໍ່ແມ່ນ “ຂອງເດີມ” ທີ່ເດີນທາງໄປເກີດ</p>
              </div>

              <!-- 3. Seed: kamma = field, consciousness = seed, craving = moisture -->
              <div class="lux-card rounded-[24px] p-4 sm:p-5 flex flex-col rise" style="animation-delay:.24s">
                <div class="flex items-center gap-2"><span class="w-8 h-8 rounded-full lux-btn flex items-center justify-center text-sm font-black">3</span><p class="font-black text-sm sm:text-base">ເມັດພືດ ໃນນາ</p></div>
                <div class="h-24 mt-3 rounded-2xl bg-white/[.03] border border-white/10 grid grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr] items-center px-2 text-center">
                  <?php foreach ([['🟫', 'ກຳ', 'ນາ'], ['🌰', 'ວິນຍານ', 'ເມັດພືດ'], ['💧', 'ຕັນຫາ', 'ຄວາມຊຸ່ມ'], ['🌱', 'ພົບໃໝ່', 'ງອກ']] as $k => $s): ?>
                  <div><div class="text-xl sm:text-2xl"><?php echo $s[0]; ?></div><p class="text-[11px] sm:text-xs font-black text-[#f5d98b] mt-0.5"><?php echo $s[1]; ?></p><p class="text-[10px] text-white/45">= <?php echo $s[2]; ?></p></div>
                  <?php if ($k < 2): ?><span class="text-[#d4af37] font-black">+</span><?php elseif ($k === 2): ?><i class="fas fa-arrow-right text-[#d4af37] text-xs"></i><?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <p class="text-[11px] text-center text-white/45 mt-1">ເງື່ອນໄຂຄົບ ຈຶ່ງງອກ</p>
                <p class="text-xs sm:text-sm text-white/80 mt-2 leading-relaxed"><b class="text-[#d4af37]">ພຣະສູດ (ຖອດຄວາມ):</b> ກຳເປັນນາ ວິນຍານເປັນເມັດພືດ ຕັນຫາເປັນຄວາມຊຸ່ມຊື່ນ — ສຳລັບຜູ້ມີອະວິຊາ ແລະ ຕັນຫາ ວິນຍານຈຶ່ງຕັ້ງຢູ່ ພົບໃໝ່ຈຶ່ງມີ.</p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-2"><i class="fas fa-seedling text-emerald-300 mr-1"></i><b>ເສີມ</b>: ສິ່ງທີ່ຕັ້ງໃຈ ຄິດປຸງແຕ່ງ ແລະ ນອນເນື່ອງຢູ່ (ອະນຸໄສ) ເປັນບ່ອນຕັ້ງຂອງວິນຍານ — ບໍ່ມີ ກໍບໍ່ມີບ່ອນຕັ້ງ ບໍ່ງອກງາມ.</p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-2"><i class="fas fa-mobile-screen text-sky-300 mr-1"></i><b class="text-sky-200">ໃນຊີວິດ</b>: ເຄີຍຜິດຫວັງກັບໂທລະສັບຍີ່ຫໍ້ໜຶ່ງ ແລະ ຍັງຄຽດຄ້າງຢູ່ → ເຫັນອີກ ຄວາມບໍ່ມັກ ກໍ “ງອກ” ຂຶ້ນທັນທີ — ເງື່ອນໄຂເກົ່າ ສົ່ງຜົນ ໂດຍບໍ່ມີຫຍັງເດີນທາງມາ.</p>
                <p class="mt-auto pt-3 text-xs sm:text-sm font-bold text-[#f5d98b]"><i class="fas fa-arrow-right mr-1.5"></i>ການເກີດ ອາໄສເງື່ອນໄຂ — ບໍ່ແມ່ນວິນຍານຖາວອນ</p>
              </div>
            </div>

            <!-- One-line synthesis + links onward through the deck -->
            <div class="rounded-3xl p-4 mt-4 bg-[#d4af37]/10 border border-dashed border-[#d4af37]/45 rise" style="animation-delay:.4s">
              <p class="text-sm sm:text-base text-white/90 leading-relaxed"><i class="fas fa-link text-[#d4af37] mr-1.5"></i>ສະຫຼຸບ: <b class="text-[#f5d98b]">ບໍ່ມີ “ຜູ້ຮູ້” ດວງເດີມ ເດີນທາງ</b> — ທັງໃນແຕ່ລະຂະນະ ແລະ ຂ້າມພົບ ມີແຕ່ເຫດປັດໄຈ ເກີດ-ດັບ ສືບຕໍ່ກັນ. ລີງ = <b>ເກີດ-ດັບ</b> · ໄຟ = <b>ອາໄສປັດໄຈ</b> · ເມັດພືດ = <b>ເງື່ອນໄຂເກົ່າ ສົ່ງຜົນ</b>.</p>
              <button @click="go(5)" class="mt-3 w-full text-left rounded-2xl px-4 py-2.5 bg-white/5 border border-white/15 text-xs sm:text-sm text-white/80 hover:bg-white/10 transition"><i class="fas fa-circle-question text-[#d4af37] mr-1.5"></i>ຄຳຖາມຕໍ່ໄປ: ຖ້າບໍ່ມີຫຍັງເດີນທາງ ເປັນຫຍັງ <b class="text-[#f5d98b]">ອະດີດ ຍັງສົ່ງຜົນເຖິງປັດຈຸບັນ</b>? <span class="font-bold text-[#f5d98b] whitespace-nowrap">→ <span x-text="'6 · ' + titles[5]"></span></span></button>
              <div class="flex flex-wrap items-center gap-2 mt-3 text-xs font-bold">
                <span class="text-white/50">ເບິ່ງຕໍ່:</span>
                <?php foreach ([[6, 'fa-dharmachakra'], [8, 'fa-arrows-spin'], [9, 'fa-droplet']] as [$n, $ic]): ?>
                <button @click="go(<?php echo $n; ?>)" class="lux-chip px-3 py-1.5 rounded-full"><i class="fas <?php echo $ic; ?> mr-1"></i><span x-text="'<?php echo $n + 1; ?> · ' + titles[<?php echo $n; ?>]"></span></button>
                <?php endforeach; ?>
              </div>
            </div>
            <p class="text-[11px] text-white/40 mt-3"><i class="fas fa-circle-info text-[#d4af37] mr-1"></i>ຂໍ້ຄວາມ “ພຣະສູດ” ເປັນການຖອດຄວາມໂດຍສັງເຂບ ບໍ່ແມ່ນຄຳແປຄຳຕໍ່ຄຳ — ເລກສູດ ແລະ ລິ້ງຕົ້ນສະບັບ ຢູ່ໃນສະໄລ <button @click="go(13)" class="underline underline-offset-2">ແຫຼ່ງອ້າງອີງ</button>. ອຸປະມາແຕ່ລະອັນ ໃຊ້ອະທິບາຍຄົນລະແງ່ ບໍ່ຄວນຕີຄວາມເກີນບໍລິບົດຂອງພຣະສູດ.</p>
          </div>
        </div>

        <!-- 8. Five aggregates & the dead star -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===5" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 6</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-4xl mt-3">ຖ້າບໍ່ມີຫຍັງເດີນທາງ ເປັນຫຍັງອະດີດ ຍັງສົ່ງຜົນເຖິງດຽວນີ້ ? <span class="lux-gold">(ຂັນ 5)</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base">ຕໍ່ຈາກ <button @click="go(4)" class="font-bold text-[#f5d98b] underline underline-offset-2">ອຸປະມາເມັດພືດ</button>: ເງື່ອນໄຂເກົ່າ ສົ່ງຜົນແນວໃດ? ມາເບິ່ງໃກ້ໆ ດ້ວຍຕົວຢ່າງໂທລະສັບເດີມ — <b class="text-[#f5d98b]">ເຫັນໂທລະສັບລຸ້ນໃໝ່</b>: ປະສົບການດຽວ ແຍກເບິ່ງໄດ້ເປັນ 5 ກອງ:</p>
            <!-- One experience, five aggregates -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3 mt-4">
              <?php foreach ([['fa-mobile-screen', 'ຮູບ', 'ໂທລະສັບ ແລະ ຮ່າງກາຍ ທີ່ເຫັນ'], ['fa-heart', 'ເວທະນາ', 'ຮູ້ສຶກພໍໃຈ'], ['fa-tag', 'ສັນຍາ', 'ຈຳໄດ້ວ່າ “ຍີ່ຫໍ້ທີ່ມັກ”'], ['fa-gears', 'ສັງຂານ', 'ຄິດ “ຊື້ດີບໍ່?” ເຈດຕະນາເກີດ'], ['fa-eye', 'ວິນຍານ', 'ການຮັບຮູ້ທາງຕາ']] as $k => $kh): ?>
              <div class="rounded-2xl p-3 sm:p-4 border text-center rise <?php echo $kh[1] === 'ສັງຂານ' ? 'bg-amber-300/15 border-amber-300/60' : 'bg-white/5 border-white/15'; ?>" style="animation-delay:<?php echo $k * .08; ?>s">
                <i class="fas <?php echo $kh[0]; ?> text-[#f5d98b] text-lg"></i>
                <p class="font-black text-sm sm:text-base mt-1.5"><?php echo $kh[1]; ?></p>
                <p class="text-[11px] sm:text-xs text-white/55 mt-0.5"><?php echo $kh[2]; ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="rounded-3xl p-4 sm:p-5 mt-4 bg-amber-400/10 border border-dashed border-amber-300/40 flex gap-4 items-start rise" style="animation-delay:.4s">
              <i class="fas fa-clock-rotate-left text-amber-300 text-xl mt-0.5"></i>
              <div class="text-sm sm:text-base text-white/85 leading-relaxed">
                <p>ເຄີຍຊື້ໂທລະສັບຍີ່ຫໍ້ນີ້ ແລ້ວຜິດຫວັງ → ເຫັນອີກເທື່ອ ກໍ <b>ບໍ່ມັກຂຶ້ນມາທັນທີ</b>. ປະສົບການທີ່ຜ່ານໄປແລ້ວ ຍັງ <b class="text-amber-200">ເປັນເງື່ອນໄຂ</b> ໃຫ້ຄວາມຄິດ ແລະ ຄວາມຮູ້ສຶກຕອນນີ້ — ຄ້າຍແສງທີ່ອອກເດີນທາງໃນອະດີດ ແຕ່ມາຮອດເຮົາຕອນນີ້.</p>
                <p class="text-xs sm:text-sm text-white/55 mt-2"><i class="fas fa-triangle-exclamation text-amber-300 mr-1"></i>ແສງດາວ ໃຊ້ເປັນອຸປະມາໄດ້ <b>ເຄິ່ງດຽວ</b>: <span class="text-emerald-300">✔</span> ສິ່ງທີ່ປາກົດຕອນນີ້ ມີເຫດມາຈາກອະດີດ (ຄືເມັດພືດ) · <span class="text-rose-300">✘</span> ແຕ່ຈິດ ບໍ່ໄດ້ເດີນທາງມາຄືລຳແສງ — ມັນເກີດໃໝ່ທຸກຂະນະ (ຄືລີງ) ເພາະອາໄສປັດໄຈ (ຄືໄຟ). ສິ່ງທີ່ສືບຕໍ່ ຄືເຫດປັດໄຈ (ຄວາມຈຳ, ນິໄສ, ອະນຸໄສ). <button @click="go(4)" class="underline underline-offset-2 text-[#f5d98b]">(ເບິ່ງສະໄລ 5)</button></p>
              </div>
            </div>
            <p class="font-bold text-base sm:text-lg mt-8">ສິ່ງທີ່ອຸປະມານີ້ຊ່ວຍໃຫ້ເຫັນຊັດ:</p>
            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr_auto_1fr] items-stretch gap-3 mt-4">
              <?php foreach ([['fa-diagram-project', 'ມີເຫດປັດໄຈມາກ່ອນ', 'ສິ່ງທີ່ປາກົດໃນປັດຈຸບັນ ລ້ວນມີເຫດປັດໄຈ'], ['fa-eye-low-vision', 'ເຮົາບໍ່ເຫັນກະບວນການທັງໝົດ', 'ເຫັນແຕ່ຜົນ ບໍ່ເຫັນທີ່ມາ'], ['fa-user', 'ຈິດເຕີມຄວາມໝາຍ', '“ນີ້ແມ່ນສິ່ງດຽວກັນ, ນີ້ແມ່ນເຮົາ, ນີ້ແມ່ນຕົວຕົນຂອງເຮົາ”']] as $k => $s): ?>
              <div class="lux-card rounded-2xl p-4 rise" style="animation-delay:<?php echo $k * .12; ?>s">
                <div class="w-10 h-10 rounded-full lux-btn flex items-center justify-center text-sm font-black"><?php echo $k + 1; ?></div>
                <p class="font-bold text-sm sm:text-base mt-3"><i class="fas <?php echo $s[0]; ?> text-[#d4af37] mr-1.5"></i><?php echo $s[1]; ?></p>
                <p class="text-xs sm:text-sm text-white/55 mt-1"><?php echo $s[2]; ?></p>
              </div>
              <?php if ($k < 2): ?><i class="hidden sm:block fas fa-arrow-right-long text-[#d4af37] self-center"></i><?php endif; ?>
              <?php endforeach; ?>
            </div>
            <p class="text-sm text-white/65 mt-6">ຈຸດນີ້ເອງ ທີ່ສາມາດນຳໄປພິຈາລະນາຮ່ວມກັບ:</p>
            <div class="flex flex-wrap gap-2 mt-3">
              <?php foreach (['ອະວິຊາ', 'ປະຕິຈະສະມຸບປະບາດ', 'ການຍຶດຖືວ່າເປັນ “ຕົວເຮົາ”'] as $c): ?>
              <span class="lux-chip px-4 py-2 rounded-full text-sm font-bold"><?php echo $c; ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <?php $kala = [
          'past' => ['ອະດີດເຫດ', '#7dd3fc'],
          'present-result' => ['ປັດຈຸບັນຜົນ', '#f5d98b'],
          'present-cause' => ['ປັດຈຸບັນເຫດ', '#fda4af'],
          'future' => ['ອະນາຄົດຜົນ', '#c4b5fd'],
        ]; ?>
        <!-- 9. Paticcasamuppada clock -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===6" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 7</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ໂມງ <span class="lux-gold">ປະຕິຈຈະສະມຸບບາດ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed">ໃນທາງພຸດທະທຳ ປະສົບການ ແລະ ຄວາມທຸກ <b class="text-[#f5d98b]">ບໍ່ໄດ້ເກີດຂຶ້ນລອຍໆ</b> ແຕ່ເກີດໂດຍອາໄສເຫດ ແລະ ປັດໄຈ 12 ຂໍ້ — ຂໍ້ໜຶ່ງເປັນປັດໄຈໃຫ້ອີກຂໍ້ໜຶ່ງ ວົນເປັນວົງຈອນ ຕາບໃດທີ່ຍັງມີອະວິຊາ.</p>
            <div class="flex flex-wrap items-center gap-x-1.5 gap-y-1 mt-3 text-[11px] sm:text-xs font-bold">
              <?php foreach ($paticca as $k => $p): ?><span class="px-2 py-0.5 rounded-full bg-white/[.06] <?php echo in_array($k, [6, 7]) ? 'text-[#f5d98b] ring-1 ring-[#d4af37]/60' : 'text-white/75'; ?>"><?php echo $p['name']; ?></span><i class="fas <?php echo $k < 11 ? 'fa-arrow-right' : 'fa-rotate-left'; ?> text-[9px] text-[#d4af37]/70"></i><?php endforeach; ?>
            </div>
            <p class="text-xs text-white/50 mt-2"><i class="fas fa-circle-nodes text-[#d4af37] mr-1.5"></i>ປະຕິຈຈະສະມຸບບາດ ເນັ້ນ <b class="text-white/80">ຄວາມສຳພັນລະຫວ່າງເຫດ ແລະ ປັດໄຈ</b> — ບໍ່ແມ່ນພຽງລຳດັບເຫດການຕາມເວລາ. ໂມງນີ້ເປັນພຽງພາບຊ່ວຍຈື່ (ໂມງປະລະມານູແທ້ ນັບ 1 ວິນາທີ ຈາກການສັ່ນຂອງອະຕອມ ຊີຊຽມ-133 <span class="sci-num">9,192,631,770</span> ຮອບ — <a class="underline text-sky-300/80" href="https://www.bipm.org/en/si-base-units/second" target="_blank" rel="noopener">BIPM</a>).</p>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6 lg:gap-10 items-center mt-6">
              <!-- Clock face -->
              <div class="rise">
                <div class="no-print flex justify-center gap-1 mb-3">
                  <button @click="clockView='live'" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all" :class="clockView==='live' ? 'lux-btn' : 'lux-card text-white/60'">ໂມງເຄື່ອນໄຫວ</button>
                  <button @click="clockView='image'" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all" :class="clockView==='image' ? 'lux-btn' : 'lux-card text-white/60'">ຮູບຕົ້ນສະບັບ</button>
                </div>
                <div x-show="clockView==='live'" class="relative mx-auto w-full max-w-[440px] aspect-square" @touchstart.passive.stop="tStart($event)" @touchend.stop="stepSwipe($event, d => setTick(tick + d))">
                  <svg viewBox="0 0 400 400" class="absolute inset-0 w-full h-full">
                    <circle cx="200" cy="200" r="190" fill="rgba(255,255,255,.02)" stroke="rgba(212,175,55,.45)" stroke-width="2"/>
                    <circle cx="200" cy="200" r="150" fill="none" stroke="rgba(255,255,255,.08)" stroke-dasharray="3 6"/>
                    <?php for ($t = 0; $t < 60; $t++): $a = deg2rad($t * 6 - 90); $big = $t % 5 === 0; ?>
                    <line x1="<?php echo 200 + cos($a) * ($big ? 172 : 180); ?>" y1="<?php echo 200 + sin($a) * ($big ? 172 : 180); ?>" x2="<?php echo 200 + cos($a) * 186; ?>" y2="<?php echo 200 + sin($a) * 186; ?>" stroke="<?php echo $big ? '#d4af37' : 'rgba(255,255,255,.25)'; ?>" stroke-width="<?php echo $big ? 2 : 1; ?>"/>
                    <?php endfor; ?>
                    <!-- ສົນທິ 3: junctions between periods -->
                    <?php foreach ([1.5, 6.5, 9.5] as $j): $a = deg2rad($j * 30 - 90); ?>
                    <circle cx="<?php echo 200 + cos($a) * 150; ?>" cy="<?php echo 200 + sin($a) * 150; ?>" r="5" fill="#0b0d17" stroke="#f5d98b" stroke-width="2"/>
                    <?php endforeach; ?>
                    <!-- hand -->
                    <g :style="`transform: rotate(${step * 30}deg); transform-origin: 200px 200px; transition: transform .8s cubic-bezier(.34,1.56,.64,1)`">
                      <line x1="200" y1="214" x2="200" y2="78" stroke="#f5d98b" stroke-width="4" stroke-linecap="round"/>
                      <circle cx="200" cy="78" r="6" fill="#f5d98b"/>
                    </g>
                    <circle cx="200" cy="200" r="10" fill="#d4af37"/>
                  </svg>
                  <?php foreach ($paticca as $k => $p): $a = deg2rad($k * 30 - 90); $c = $kala[$p['kala']][1]; ?>
                  <button @click="setTick(<?php echo $k; ?>)" class="absolute -translate-x-1/2 -translate-y-1/2 rounded-full px-2 py-1 text-[10px] sm:text-xs font-bold whitespace-nowrap border transition-all duration-300"
                          style="left:<?php echo round(50 + cos($a) * 37.5, 2); ?>%; top:<?php echo round(50 + sin($a) * 37.5, 2); ?>%; border-color:<?php echo $c; ?>66; color:<?php echo $c; ?>"
                          :class="tick===<?php echo $k; ?> ? 'scale-125 bg-[#0b0d17] shadow-[0_0_24px_rgba(245,217,139,.6)]' : 'bg-[#0b0d17]/80 opacity-70'">
                    <span class="opacity-60 mr-0.5"><?php echo $p['hour']; ?></span><?php echo $p['name']; ?>
                  </button>
                  <?php endforeach; ?>
                </div>
                <img x-show="clockView==='image'" src="<?php echo url('assets/images/paticca-clock.png'); ?>" alt="ໂມງປະຕິຈຈະສະມຸບບາດ" loading="lazy" class="mx-auto w-full max-w-[440px] h-auto rounded-full">
              </div>

              <!-- Current link -->
              <div class="rise" style="animation-delay:.15s">
                <?php foreach ($paticca as $k => $p): $c = $kala[$p['kala']]; ?>
                <div x-show="tick===<?php echo $k; ?>" class="lux-card rounded-[28px] p-6 sm:p-7">
                  <div class="flex items-center justify-between gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border" style="color:<?php echo $c[1]; ?>; border-color:<?php echo $c[1]; ?>66; background:<?php echo $c[1]; ?>14"><?php echo $c[0]; ?></span>
                    <span class="text-xs text-white/45">ໂມງ <?php echo $p['hour']; ?> · ຂໍ້ທີ <?php echo $k + 1; ?> / 12</span>
                  </div>
                  <p class="lux-gold text-3xl sm:text-4xl font-black mt-4"><?php echo $p['name']; ?></p>
                  <p class="font-bold text-base sm:text-lg mt-1"><?php echo $p['mean']; ?></p>
                  <p class="text-sm text-white/65 mt-3 leading-relaxed"><?php echo $p['detail']; ?></p>
                  <p class="text-xs text-white/45 mt-5"><i class="fas fa-arrow-right-long text-[#d4af37] mr-1.5"></i>ເປັນປັດໄຈໃຫ້ <b class="text-white/75"><?php echo $paticca[($k + 1) % 12]['name']; ?></b></p>
                </div>
                <?php endforeach; ?>
                <div class="no-print flex items-center gap-2 mt-4">
                  <button @click="setTick(tick-1)" class="w-10 h-10 rounded-full lux-card" aria-label="ຂໍ້ກ່ອນ"><i class="fas fa-backward-step"></i></button>
                  <button @click="setTick(tick+1)" class="w-10 h-10 rounded-full lux-card" aria-label="ຂໍ້ຕໍ່ໄປ"><i class="fas fa-forward-step"></i></button>
                </div>
                <div class="flex flex-wrap gap-2 mt-4 text-[11px]">
                  <?php foreach ($kala as $c): ?>
                  <span class="flex items-center gap-1.5 text-white/60"><span class="w-2.5 h-2.5 rounded-full" style="background:<?php echo $c[1]; ?>"></span><?php echo $c[0]; ?></span>
                  <?php endforeach; ?>
                  <span class="flex items-center gap-1.5 text-white/60"><span class="w-2.5 h-2.5 rounded-full border-2 border-[#f5d98b]"></span>ສົນທິ (ຈຸດເຊື່ອມ)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 10. Kala 3: cause and result across time -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===7" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 8</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ກາລະ 3 — <span class="lux-gold">ເຫດ ແລະ ຜົນ ໃນເວລາ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed">ສັງເຂບ 4: ການຈັດກຸ່ມປະຕິຈຈະສະມຸບບາດ ຕາມເຫດ ແລະ ຜົນ ຂ້າມອະດີດ-ປັດຈຸບັນ-ອະນາຄົດ (ຕາມຄຳອະທິບາຍສາຍເຖລະວາດ).</p>
            <p class="text-xs text-white/45 mt-1.5 max-w-3xl"><i class="fas fa-circle-info text-[#d4af37] mr-1.5"></i>ນີ້ເປັນກອບອະທິບາຍແບບໜຶ່ງ — ບໍ່ໄດ້ໝາຍຄວາມວ່າທຸກຂໍ້ເກີດແຍກກັນ ເປັນຂັ້ນຕອນ ທີ່ມີຊ່ວງເວລາຕາຍຕົວສະເໝີ.</p>

            <!-- Timeline lanes -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1.6fr_auto_1.2fr_auto_1fr] items-stretch gap-2 md:gap-3 mt-7">
              <?php $groups = ['past' => 'ອະດີດ', 'present-result' => 'ປັດຈຸບັນ', 'present-cause' => 'ປັດຈຸບັນ', 'future' => 'ອະນາຄົດ'];
              $n = 0; foreach ($groups as $key => $period): $c = $kala[$key]; ?>
              <div class="rounded-2xl p-4 border rise" style="border-color:<?php echo $c[1]; ?>55; background:<?php echo $c[1]; ?>0f; animation-delay:<?php echo $n * .12; ?>s">
                <p class="text-[11px] text-white/45 font-bold"><?php echo $period; ?></p>
                <p class="font-black text-base sm:text-lg" style="color:<?php echo $c[1]; ?>"><?php echo $c[0]; ?></p>
                <div class="flex flex-wrap gap-1.5 mt-3">
                  <?php foreach ($paticca as $p): if ($p['kala'] !== $key) continue; ?>
                  <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-black/30 text-white/85"><?php echo $p['name']; ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php if ($n < 3): ?>
              <div class="flex md:flex-col items-center justify-center gap-1 text-[10px] text-[#f5d98b] font-bold">
                <span class="w-4 h-4 rounded-full border-2 border-[#f5d98b] bg-[#0b0d17]"></span><span>ສົນທິ <?php echo $n + 1; ?></span>
              </div>
              <?php endif; $n++; endforeach; ?>
            </div>
            <p class="text-xs text-white/45 mt-3"><i class="fas fa-circle-info text-[#d4af37] mr-1.5"></i>ສົນທິ 3 (ຈຸດເຊື່ອມ): ສັງຂານ + ວິນຍານ · ເວທະນາ + ຕັນຫາ · ພົບ + ຊາດ.</p>

            <div class="grid lg:grid-cols-2 gap-4 sm:gap-5 mt-6">
              <!-- Starlight link -->
              <div class="lux-card rounded-[28px] p-5 sm:p-6 rise" style="animation-delay:.3s">
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-star text-[#d4af37] mr-2"></i>ເຊື່ອມກັບແສງດາວ <span class="text-xs text-white/45 font-normal">(ອຸປະມາ)</span></p>
                <p class="text-sm text-white/70 mt-3 leading-relaxed"><b style="color:#f5d98b">ປັດຈຸບັນຜົນ</b> (ວິນຍານ → ເວທະນາ): ຄ້າຍແສງດາວ ຕົງທີ່ <b class="text-white/85">ປາກົດຕອນນີ້ ແຕ່ມີເຫດມາກ່ອນ</b>.</p>
                <p class="text-sm text-white/70 mt-2 leading-relaxed"><b style="color:#fda4af">ປັດຈຸບັນເຫດ</b> (ຕັນຫາ → ພົບ): ຈຸດທີ່ຈິດຕີຄວາມ ແລະ ຍຶດຖື — ກາຍເປັນເຫດໃໝ່ ສົ່ງຜົນຕໍ່ໄປ.</p>
                <p class="text-xs text-white/45 mt-2">ບໍ່ໄດ້ທຽບແຕ່ລະຂໍ້ກັບເວລາເດີນທາງຂອງແສງໂດຍກົງ — ເປັນກະບວນການຄົນລະປະເພດ.</p>
              </div>
              <!-- Vatta 3 wheel -->
              <div class="lux-card rounded-[28px] p-5 sm:p-6 flex items-center gap-5 rise" style="animation-delay:.4s">
                <div class="relative w-32 h-32 sm:w-36 sm:h-36 shrink-0">
                  <div class="absolute inset-0 rounded-full border-2 border-dashed border-[#d4af37]/50 animate-[spin_12s_linear_infinite]"></div>
                  <span class="absolute top-1 left-1/2 -translate-x-1/2 text-xs font-black text-rose-300">ກິເລສ</span>
                  <span class="absolute bottom-5 right-0 text-xs font-black text-sky-300">ກຳ</span>
                  <span class="absolute bottom-5 left-0 text-xs font-black text-[#f5d98b]">ວິບາກ</span>
                  <i class="fas fa-rotate absolute inset-0 m-auto w-fit h-fit text-2xl text-[#d4af37]"></i>
                </div>
                <div class="text-xs sm:text-sm text-white/70 space-y-1.5">
                  <p class="font-bold text-white text-sm sm:text-base">ວັດຕະ 3 — ການໝູນວຽນ</p>
                  <p><b class="text-rose-300">ກິເລສ:</b> ອະວິຊາ, ຕັນຫາ, ອຸປາທານ</p>
                  <p><b class="text-sky-300">ກຳ:</b> ສັງຂານ, ກຳມະພົບ</p>
                  <p><b class="text-[#f5d98b]">ວິບາກ:</b> ວິນຍານ, ນາມຮູບ, ສະລາຍະຕະນະ, ຜັດສະ, ເວທະນາ</p>
                  <p class="text-white/45">ມູລ 2: ອະວິຊາ ເລີ່ມໃນອະດີດ · ຕັນຫາ ເລີ່ມໃນປັດຈຸບັນ</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
        // Annular sector path between radii $r1..$r2 and clock angles $a1..$a2 (degrees, 0 = 12 o'clock).
        $arc = function ($r1, $r2, $a1, $a2) {
            $p = fn($r, $a) => round(200 + $r * cos(deg2rad($a - 90)), 2) . ' ' . round(200 + $r * sin(deg2rad($a - 90)), 2);
            $big = ($a2 - $a1) > 180 ? 1 : 0;
            return "M{$p($r2, $a1)} A$r2 $r2 0 $big 1 {$p($r2, $a2)} L{$p($r1, $a2)} A$r1 $r1 0 $big 0 {$p($r1, $a1)} Z";
        };
        $at = fn($r, $a) => [round(200 + $r * cos(deg2rad($a - 90)), 2), round(200 + $r * sin(deg2rad($a - 90)), 2)];
        $vattaColor = ['ກິເລສ' => '#fda4af', 'ກຳ' => '#7dd3fc', 'ວິບາກ' => '#f5d98b'];
        $causeRing = [[0, 1, 'ເຫດ', 'past'], [2, 6, 'ຜົນ', 'present-result'], [7, 9, 'ເຫດ', 'present-cause'], [10, 11, 'ຜົນ', 'future']];
        $periodRing = [[0, 1, 'ອະດີດ', '#7dd3fc'], [2, 9, 'ປັດຈຸບັນ', '#f5d98b'], [10, 11, 'ອະນາຄົດ', '#c4b5fd']];
        // ອາການ 20: each of the 4 groups (ສັງເຂບ 4) names some links outright and carries others hidden inside it.
        $akara = [
            ['key' => 'past', 'span' => [0, 1], 'ex' => [0, 1], 'im' => [7, 8, 9], 'short' => 'ອະດີດເຫດ 5', 'color' => '#7dd3fc',
             'body' => 'ເມື່ອມີອະວິຊາ ກໍຕ້ອງມີ ຕັນຫາ ແລະ ອຸປາທານ ແຝງຢູ່ນຳສະເໝີ (ກິເລສ). ສັງຂານທີ່ປຸງແຕ່ງ ກໍຄື ກຳມະພົບ (ກຳ). ເຫດໃນອະດີດ ຈຶ່ງຄົບ 5.'],
            ['key' => 'present-result', 'span' => [2, 6], 'ex' => [2, 3, 4, 5, 6], 'im' => [10, 11], 'short' => 'ປັດຈຸບັນຜົນ 5', 'color' => '#f5d98b',
             'body' => 'ວິນຍານ → ເວທະນາ ທີ່ເກີດໃນຊີວິດນີ້ ກໍຄື ການເກີດ (ຊາດ) ແລະ ຄວາມເສື່ອມ-ດັບ (ຊະລາມໍລະນະ) ຂອງຂັນທ໌ 5 ນັ້ນເອງ (ວິບາກ). ຜົນປັດຈຸບັນ ຈຶ່ງຄົບ 5.'],
            ['key' => 'present-cause', 'span' => [7, 9], 'ex' => [7, 8, 9], 'im' => [0, 1], 'short' => 'ປັດຈຸບັນເຫດ 5', 'color' => '#fda4af',
             'body' => 'ຕັນຫາ ແລະ ອຸປາທານ ເກີດໄດ້ ເພາະບໍ່ຮູ້ຕາມຈິງ — ອະວິຊາ ແຝງຢູ່ນຳ (ກິເລສ). ກຳມະພົບ ກໍຄື ສັງຂານ (ກຳ). ເຫດປັດຈຸບັນ ຈຶ່ງຄົບ 5.'],
            ['key' => 'future', 'span' => [10, 11], 'ex' => [10, 11], 'im' => [2, 3, 4, 5, 6], 'short' => 'ອະນາຄົດຜົນ 5', 'color' => '#c4b5fd',
             'body' => 'ຊາດ ແລະ ຊະລາມໍລະນະ ໃນອະນາຄົດ ກໍຄື ວິນຍານ → ເວທະນາ ຂອງຊີວິດໃໝ່ ທີ່ແຝງຢູ່ (ວິບາກ). ຜົນອະນາຄົດ ຈຶ່ງຄົບ 5 — ແລ້ວວົງລໍ້ກໍໝູນຕໍ່.'],
        ];
        $implied = '#f87171';
        ?>
        <!-- 11. The wheel: defilement · action · result across past, present, future -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===8" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 9</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ວົງລໍ້ <span class="lux-gold">ກິເລສ · ກຳ · ວິບາກ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed"><b class="text-rose-300">ກິເລສ</b> (ຄວາມໂລບ ໂກດ ຫຼົງ) → <b class="text-sky-300">ກຳ</b> (ການກະທຳທີ່ມີເຈດຕະນາ ທາງກາຍ ວາຈາ ໃຈ) → <b class="text-[#f5d98b]">ວິບາກ</b> (ຜົນຕາມເງື່ອນໄຂ) → ກິເລສໃໝ່ ↻ — ເປັນ <b class="text-white/85">ເຄືອຂ່າຍເຫດປັດໄຈ</b> ທີ່ວົນຕໍ່ກັນ ບໍ່ແມ່ນເສັ້ນຊື່. ກົດເບິ່ງແຕ່ລະຂັ້ນຕອນໄດ້.</p>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6 lg:gap-10 items-start mt-6">
              <!-- Wheel -->
              <div class="rise" @touchstart.passive.stop="tStart($event)" @touchend.stop="stepSwipe($event, d => wheelGo(wheelStep + d))">
                <svg viewBox="0 0 400 400" class="mx-auto w-full max-w-[460px] h-auto select-none">
                  <circle cx="200" cy="200" r="199" fill="none" stroke="rgba(212,175,55,.35)" stroke-width="1.5" stroke-dasharray="4 8"><animateTransform attributeName="transform" type="rotate" from="0 200 200" to="360 200 200" dur="30s" repeatCount="indefinite"/></circle>
                  <!-- Ring 1: the 12 links -->
                  <?php foreach ($paticca as $k => $p): [$x, $y] = $at(178, $k * 30); ?>
                  <g class="cursor-pointer" @click="wheelPick(<?php echo $k; ?>)" :style="`opacity:${wheelOn(<?php echo $k; ?>) || wheelHint(<?php echo $k; ?>) ? 1 : .28}; transition: opacity .4s`">
                    <path d="<?php echo $arc(160, 196, $k * 30 - 15, $k * 30 + 15); ?>" :fill="wheelHint(<?php echo $k; ?>) ? '<?php echo $implied; ?>33' : (wheelOn(<?php echo $k; ?>) ? 'rgba(212,175,55,.22)' : 'rgba(255,255,255,.04)')"
                          :stroke="wheelHint(<?php echo $k; ?>) ? '<?php echo $implied; ?>' : 'rgba(212,175,55,.45)'" :stroke-dasharray="wheelHint(<?php echo $k; ?>) ? '4 3' : ''" stroke-width="1.2"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y - 2; ?>" text-anchor="middle" font-size="<?php echo mb_strlen($p['name']) > 8 ? 8.5 : 10.5; ?>" font-weight="700" fill="#fff"><?php echo $p['name']; ?></text>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 10; ?>" text-anchor="middle" font-size="7.5" fill="#d4af37"><?php echo $k + 1; ?></text>
                  </g>
                  <?php endforeach; ?>
                  <!-- Ring 2: links hidden inside each group (ອາການທີ່ແຝງ) -->
                  <?php foreach ($akara as $g => $grp): [$a, $b] = $grp['span']; $names = array_map(fn($n) => $paticca[$n]['name'], $grp['im']);
                    $lines = count($names) > 3 ? [implode(' · ', array_slice($names, 0, 3)), implode(' · ', array_slice($names, 3))] : [implode(' · ', $names)];
                    $mid = ($a + $b) * 15; $flip = $mid > 90 && $mid < 270; $a1 = $a * 30 - 13; $a2 = $b * 30 + 13; ?>
                  <g :style="`opacity:${wheelSpanOn(<?php echo $a; ?>, <?php echo $b; ?>) ? 1 : .28}; transition: opacity .4s`">
                    <path d="<?php echo $arc(124, 160, $a * 30 - 15, $b * 30 + 15); ?>" fill="<?php echo $implied; ?>14" stroke="<?php echo $implied; ?>55" stroke-width="1"/>
                    <?php foreach ($lines as $l => $line): $r = count($lines) > 1 ? ($l === 0 ? 147 : 135) : 140; $r += $flip ? 4 : -2;
                      $p1 = $at($r, $flip ? $a2 : $a1); $p2 = $at($r, $flip ? $a1 : $a2); $id = "imp-$g-$l"; ?>
                    <path id="<?php echo $id; ?>" d="M<?php echo implode(' ', $p1); ?> A<?php echo "$r $r"; ?> 0 <?php echo ($a2 - $a1) > 180 ? 1 : 0; ?> <?php echo $flip ? 0 : 1; ?> <?php echo implode(' ', $p2); ?>" fill="none"/>
                    <text font-size="<?php echo count($lines) > 1 ? 7.5 : 9; ?>" font-weight="700" fill="#fca5a5"><textPath href="#<?php echo $id; ?>" startOffset="50%" text-anchor="middle"><?php echo $line; ?></textPath></text>
                    <?php endforeach; ?>
                  </g>
                  <?php endforeach; ?>
                  <!-- Ring 3: ວັດຕະ 3 -->
                  <?php foreach ($paticca as $k => $p): [$x, $y] = $at(111, $k * 30); $c = $vattaColor[$p['vatta']]; ?>
                  <g :style="`opacity:${wheelOn(<?php echo $k; ?>) ? 1 : .28}; transition: opacity .4s`">
                    <path d="<?php echo $arc(98, 124, $k * 30 - 15, $k * 30 + 15); ?>" fill="<?php echo $c; ?>22" stroke="<?php echo $c; ?>66" stroke-width="1"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 3.5; ?>" text-anchor="middle" font-size="9" font-weight="700" fill="<?php echo $c; ?>"><?php echo $p['vatta']; ?></text>
                  </g>
                  <?php endforeach; ?>
                  <!-- Ring 4: cause / result -->
                  <?php foreach ($causeRing as [$a, $b, $label, $key]): $c = $kala[$key][1]; [$x, $y] = $at(85, ($a + $b) * 15); ?>
                  <g :style="`opacity:${wheelSpanOn(<?php echo $a; ?>, <?php echo $b; ?>) ? 1 : .28}; transition: opacity .4s`">
                    <path d="<?php echo $arc(72, 98, $a * 30 - 15, $b * 30 + 15); ?>" fill="<?php echo $c; ?>1f" stroke="<?php echo $c; ?>77" stroke-width="1"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 4; ?>" text-anchor="middle" font-size="10" font-weight="800" fill="<?php echo $c; ?>"><?php echo $label; ?></text>
                  </g>
                  <?php endforeach; ?>
                  <!-- Ring 5: past / present / future -->
                  <?php foreach ($periodRing as [$a, $b, $label, $c]): [$x, $y] = $at(58, ($a + $b) * 15); ?>
                  <g :style="`opacity:${wheelSpanOn(<?php echo $a; ?>, <?php echo $b; ?>) ? 1 : .28}; transition: opacity .4s`">
                    <path d="<?php echo $arc(44, 72, $a * 30 - 15, $b * 30 + 15); ?>" fill="<?php echo $c; ?>29" stroke="<?php echo $c; ?>88" stroke-width="1"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 4; ?>" text-anchor="middle" font-size="9" font-weight="800" fill="#fff"><?php echo $label; ?></text>
                  </g>
                  <?php endforeach; ?>
                  <!-- ສົນທິ 3 -->
                  <?php foreach ([45, 195, 285] as $a): [$x, $y] = $at(160, $a); ?>
                  <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="5" fill="#0b0d17" stroke="#f5d98b" stroke-width="2"/>
                  <?php endforeach; ?>
                  <!-- Travelling pulse (link mode) -->
                  <g x-show="wheelMode==='link'" :style="`transform: rotate(${wheelPos * 30}deg); transform-origin: 200px 200px; transition: transform .8s cubic-bezier(.34,1.56,.64,1)`">
                    <circle cx="200" cy="4" r="6" fill="#fff3c4" style="filter: drop-shadow(0 0 6px #f5d98b)"/>
                  </g>
                  <circle cx="200" cy="200" r="40" fill="#0b0d17" stroke="rgba(212,175,55,.5)"/>
                  <text x="200" y="197" text-anchor="middle" font-size="10" font-weight="800" fill="#f5d98b">ປະຕິຈຈະ</text>
                  <text x="200" y="211" text-anchor="middle" font-size="10" font-weight="800" fill="#f5d98b">ສະມຸບບາດ</text>
                </svg>
              </div>

              <!-- Process panel -->
              <div class="rise" style="animation-delay:.15s">
                <div class="no-print flex flex-wrap gap-1.5">
                  <template x-for="m in [['link','ຂໍ້ 12'],['vatta','ວັດຕະ 3: ກິເລສ · ກຳ · ວິບາກ'],['kala','ກາລະ 3: ອະດີດ · ປັດຈຸບັນ · ອະນາຄົດ'],['akara','ອາການ 20: ເຫດ-ຜົນ ທີ່ແຝງ']]" :key="m[0]">
                    <button @click="wheelSetMode(m[0])" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all" :class="wheelMode===m[0] ? 'lux-btn' : 'lux-card text-white/60'" x-text="m[1]"></button>
                  </template>
                </div>

                <!-- Step chips -->
                <div class="flex flex-wrap gap-1.5 mt-3">
                  <template x-for="(s, n) in wheelSteps()" :key="wheelMode + n">
                    <button @click="wheelGo(n)" class="px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all"
                            :style="`border-color:${s.color}66; color:${wheelStep===n ? '#14110a' : s.color}; background:${wheelStep===n ? s.color : 'transparent'}`"
                            x-text="(n+1) + '. ' + s.short"></button>
                  </template>
                </div>

                <!-- Current step -->
                <div class="lux-card rounded-[28px] p-5 sm:p-6 mt-4" x-transition>
                  <div class="flex items-center justify-between gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border" :style="`color:${wheelInfo().color}; border-color:${wheelInfo().color}66; background:${wheelInfo().color}14`" x-text="wheelInfo().tag"></span>
                    <span class="text-xs text-white/45" x-text="'ຂັ້ນຕອນ ' + (wheelStep+1) + ' / ' + wheelSteps().length"></span>
                  </div>
                  <p class="lux-gold text-2xl sm:text-3xl font-black mt-4" x-text="wheelInfo().title"></p>
                  <p class="font-bold text-sm sm:text-base mt-1" x-text="wheelInfo().sub"></p>
                  <p class="text-sm text-white/70 mt-3 leading-relaxed" x-text="wheelInfo().body"></p>
                  <p x-show="wheelInfo().pali" class="text-xs text-[#f5d98b]/80 mt-3 italic" x-text="wheelInfo().pali"></p>
                  <div x-show="wheelMode==='akara'" class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-2 items-center mt-4 text-[11px] font-bold">
                    <span class="text-white/55">ປາກົດ</span>
                    <div class="flex flex-wrap gap-1.5">
                      <template x-for="c in (wheelInfo().shown || [])"><span class="px-2 py-0.5 rounded-full border" :style="`color:${{'ກິເລສ':'#fda4af','ກຳ':'#7dd3fc','ວິບາກ':'#f5d98b'}[c.vatta]}; border-color:currentColor`" x-text="c.name + ' · ' + c.vatta"></span></template>
                    </div>
                    <span class="text-[#fca5a5]">ແຝງຢູ່</span>
                    <div class="flex flex-wrap gap-1.5">
                      <template x-for="c in (wheelInfo().hidden || [])"><span class="px-2 py-0.5 rounded-full border border-dashed border-[#f87171] text-[#fca5a5] bg-[#f87171]/10" x-text="c.name + ' · ' + c.vatta"></span></template>
                    </div>
                  </div>
                  <div x-show="wheelMode!=='akara'" class="flex flex-wrap gap-1.5 mt-4">
                    <template x-for="name in wheelInfo().members"><span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/10" x-text="name"></span></template>
                  </div>
                </div>

                <div class="no-print flex items-center gap-2 mt-4">
                  <button @click="wheelGo(wheelStep-1)" class="w-10 h-10 rounded-full lux-card" aria-label="ຂັ້ນຕອນກ່ອນ"><i class="fas fa-backward-step"></i></button>
                  <button @click="wheelGo(wheelStep+1)" class="w-10 h-10 rounded-full lux-card" aria-label="ຂັ້ນຕອນຕໍ່ໄປ"><i class="fas fa-forward-step"></i></button>
                </div>
                <p class="text-[11px] text-white/45 mt-3"><i class="fas fa-hand-pointer text-[#d4af37] mr-1.5"></i>ກົດທີ່ຂໍ້ໃດໜຶ່ງໃນວົງລໍ້ ເພື່ອເບິ່ງລາຍລະອຽດ · <span class="inline-block w-2.5 h-2.5 rounded-full border-2 border-[#f5d98b] align-middle"></span> ສົນທິ (ຈຸດເຊື່ອມ)</p>
              </div>
            </div>

            <!-- One glance: ອາການ 20 = 4 groups × 5, joined by ສົນທິ 3 -->
            <div class="lux-card rounded-[28px] p-4 sm:p-6 mt-6 rise" style="animation-delay:.3s">
              <div class="flex flex-wrap items-baseline justify-between gap-2">
                <p class="font-black text-base sm:text-lg">ເບິ່ງເທື່ອດຽວ: <span class="lux-gold">ເຫດ 5 → ຜົນ 5 → ເຫດ 5 → ຜົນ 5</span></p>
                <p class="text-[11px] text-white/55"><span class="px-1.5 rounded border border-white/40">ປາກົດ</span> ຊື່ທີ່ກ່າວໄວ້ · <span class="px-1.5 rounded border border-dashed border-[#f87171] text-[#fca5a5]">ແຝງ</span> ອິງອາໄສກັນ ແຝງຢູ່ໃນໝວດນັ້ນ</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr] gap-2 md:gap-1 items-stretch mt-4">
                <?php foreach ($akara as $g => $grp): ?>
                <button type="button" @click="wheelSetMode('akara'); wheelGo(<?php echo $g; ?>)" class="text-left rounded-2xl p-3 border transition-all hover:bg-white/5"
                        :class="wheelMode==='akara' && wheelStep===<?php echo $g; ?> ? 'bg-white/10' : ''" style="border-color:<?php echo $grp['color']; ?>66">
                  <p class="font-black text-sm" style="color:<?php echo $grp['color']; ?>"><?php echo $grp['short']; ?></p>
                  <?php foreach (['ກິເລສ', 'ກຳ', 'ວິບາກ'] as $v):
                    $row = array_filter(array_merge(array_map(fn($k) => [$k, false], $grp['ex']), array_map(fn($k) => [$k, true], $grp['im'])), fn($r) => $paticca[$r[0]]['vatta'] === $v);
                    if (!$row) continue; ?>
                  <div class="flex flex-wrap items-center gap-1 mt-2">
                    <span class="text-[10px] font-black w-11 shrink-0" style="color:<?php echo $vattaColor[$v]; ?>"><?php echo $v; ?></span>
                    <?php foreach ($row as [$k, $hidden]): ?>
                    <span class="px-1.5 py-0.5 rounded-md text-[10.5px] font-bold border <?php echo $hidden ? 'border-dashed border-[#f87171] text-[#fca5a5] bg-[#f87171]/10' : 'border-white/30 text-white'; ?>"><?php echo $paticca[$k]['name']; ?></span>
                    <?php endforeach; ?>
                  </div>
                  <?php endforeach; ?>
                </button>
                <?php if ($g < 3): ?>
                <div class="flex md:flex-col items-center justify-center gap-1 text-[#f5d98b] px-1">
                  <i class="fas fa-arrow-down text-sm md:hidden"></i><i class="fas fa-arrow-right text-sm hidden md:block"></i>
                  <span class="text-[10px] font-bold whitespace-nowrap">ສົນທິ <?php echo $g + 1; ?></span>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
              </div>
              <p class="text-xs sm:text-sm text-white/65 mt-4 leading-relaxed"><i class="fas fa-rotate text-[#d4af37] mr-1.5"></i>ທຸກໝວດ ມີຄົບ <b class="text-white">ກິເລສ → ກຳ → ວິບາກ</b>: ເຫດ (ກິເລສ + ກຳ) ສົ່ງຜົນເປັນວິບາກ, ແລະ ວິບາກ (ເວທະນາ) ກໍເປັນບ່ອນເກີດກິເລສໃໝ່ — ສະນັ້ນ ເຫດ ອາໄສ ຜົນ, ຜົນ ກໍກາຍເປັນເຫດ ໝູນວຽນບໍ່ຮູ້ຈົບ ຈົນກວ່າຈະດັບອະວິຊາ ແລະ ຕັນຫາ.</p>
            </div>
          </div>
        </div>

        <!-- 11a. Feeling → craving: the link we can actually practise with -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===9" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 10</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3"><span class="lux-gold">ເວທະນາ → ຕັນຫາ</span> ຈຸດທີ່ຝຶກໄດ້ໃນຊີວິດຈິງ</h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base">ຕົວຢ່າງ: <b class="text-[#f5d98b]">ເຫັນໂທລະສັບລຸ້ນໃໝ່</b> — ສອງເສັ້ນທາງ ຈາກຄວາມຮູ້ສຶກດຽວກັນ.</p>
            <?php
            $lanes = [
                ['ອັດຕະໂນມັດ — ບໍ່ທັນຮູ້ຕົວ', '#fda4af', [['ຜັດສະ', 'ເຫັນໂທລະສັບ'], ['ເວທະນາ', 'ຮູ້ສຶກມັກ'], ['ຕັນຫາ', '“ຢາກໄດ້”'], ['ອຸປາທານ', '“ຕ້ອງມີເຄື່ອງນີ້”'], ['ທຸກ', 'ບໍ່ໄດ້ → ຫງຸດຫງິດ']]],
                ['ມີສະຕິ — ຮູ້ທັນຄວາມຮູ້ສຶກ', '#5eead4', [['ຜັດສະ', 'ເຫັນໂທລະສັບ'], ['ເວທະນາ', 'ຮູ້ສຶກມັກ'], ['ສະຕິ', '“ນີ້ຄືຄວາມພໍໃຈ ທີ່ກຳລັງເກີດ”'], ['ເລືອກໄດ້', 'ບໍ່ຕ້ອງຟ້າວເຮັດຕາມຄວາມຢາກ'], ['ໃຈເບົາ', 'ຕັດສິນໃຈດ້ວຍປັນຍາ']]],
            ];
            foreach ($lanes as $l => [$label, $tone, $chain]): ?>
            <div class="rounded-[24px] p-4 sm:p-5 mt-4 border bg-white/[.03] rise" style="border-color:<?php echo $tone; ?>55; animation-delay:<?php echo $l * .15; ?>s">
              <p class="text-xs sm:text-sm font-black" style="color:<?php echo $tone; ?>"><?php echo $label; ?></p>
              <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr_auto_1fr] items-center gap-2 mt-3">
                <?php foreach ($chain as $k => [$term, $what]): $gap = $k === 2; ?>
                <div class="rounded-2xl px-3 py-2.5 text-center <?php echo $gap ? 'ring-2' : 'bg-white/5'; ?>" style="<?php echo $gap ? "background:{$tone}1f; --tw-ring-color:{$tone}" : ''; ?>">
                  <p class="font-black text-sm sm:text-base" style="color:<?php echo $k >= 2 ? $tone : '#fff'; ?>"><?php echo $term; ?></p>
                  <p class="text-[11px] sm:text-xs text-white/60 mt-0.5"><?php echo $what; ?></p>
                </div>
                <?php if ($k < 4): ?><i class="fas fa-arrow-right hidden sm:block text-center" style="color:<?php echo $k === 1 ? $tone : 'rgba(255,255,255,.35)'; ?>"></i><i class="fas fa-arrow-down sm:hidden text-center text-white/35"></i><?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endforeach; ?>
            <div class="grid md:grid-cols-[1.4fr_1fr] gap-3 mt-5">
              <div class="rounded-2xl p-4 sm:p-5 bg-[#d4af37]/10 border border-[#d4af37]/40 rise" style="animation-delay:.35s">
                <p class="lux-gold text-lg sm:text-2xl font-black leading-snug">ສະຕິ ຊ່ວຍໃຫ້ເຮົາເຫັນເວທະນາ ແລະ ຄວາມຢາກ ໂດຍບໍ່ຈຳເປັນຕ້ອງຕອບສະໜອງຕາມມັນ ແບບອັດຕະໂນມັດ</p>
                <p class="text-xs sm:text-sm text-white/65 mt-2"><i class="fas fa-comment-slash text-[#d4af37] mr-1.5"></i>ຄືກັນ ເວລາມີຄົນເວົ້າບໍ່ເຂົ້າຫູ: ເຫັນ “ຄວາມບໍ່ພໍໃຈກຳລັງເກີດ” ກ່ອນທີ່ຈະເຜີຍໂກດ ຫຼື ໂຕ້ກັບ.</p>
              </div>
              <div class="lux-card rounded-2xl p-4 sm:p-5 rise" style="animation-delay:.45s">
                <p class="font-bold text-sm"><i class="fas fa-heart-pulse text-[#d4af37] mr-1.5"></i>ມີສະຕິ ≠ ບໍ່ມີຄວາມຮູ້ສຶກ</p>
                <p class="text-xs sm:text-sm text-white/60 mt-1.5">ເວທະນາ ຍັງເກີດໄດ້ຕາມປົກກະຕິ — ສິ່ງທີ່ປ່ຽນ ຄືເຮົາ <b class="text-white/85">ເຫັນມັນທັນ</b> ແລະ ບໍ່ປ່ອຍໃຫ້ມັນລາກໄປເປັນຕັນຫາ ແລະ ອຸປາທານ ໂດຍບໍ່ຮູ້ຕົວ. (ອຸປະມາ “ລູກສອນສອງດອກ” — ສັລລັດຖະສູດ)</p>
              </div>
            </div>
            <p class="src src-dharma mt-4"><i class="fas fa-book-open mr-1"></i>ແຫຼ່ງອ້າງອີງ (ທຳມະ):
              <a href="https://suttacentral.net/sn12.1" target="_blank" rel="noopener">ສັງ. ນິ. 12.1 ປະຕິຈຈະສະມຸບບາດສູດ</a> ·
              <a href="https://suttacentral.net/sn36.6" target="_blank" rel="noopener">ສັງ. ສະ. 36.6 ສັລລັດຖະສູດ</a></p>
          </div>
        </div>

        <!-- 11b. Eaten by time vs eating time: the wheel of an ordinary person and of an Arahant -->
        <?php
        $sides = [
            'p' => ['ປຸຖຸຊົນ', 'ຖືກເວລາກິນ', '#fda4af', 'ຍັງມີກິເລສ — ວົງລໍ້ໝູນຄົບ 12 ຂໍ້ ບໍ່ຮູ້ຈົບ'],
            'a' => ['ພຣະອະຣະຫັນ', 'ກິນເວລາ', '#5eead4', 'ໝົດກິເລສ — ວົງລໍ້ຂາດທີ່ ເວທະນາ ✂ ຕັນຫາ'],
        ];
        // What each link becomes for the Arahant: ວິຊາ replaces ອະວິຊາ, actions are mere ກິລິຍາ, the senses still work, the rest has ceased.
        $arahantState = fn($k) => $k === 0 ? ['ວິຊາ', '#5eead4'] : ($k === 1 ? ['ກິລິຍາ', '#94a3b8'] : ($k <= 6 ? ['ຮັບຮູ້', '#5eead4'] : ['ດັບ', '#64748b']));
        ?>
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===10" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 11</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ຖືກເວລາກິນ <span class="text-white/40">vs</span> <span class="lux-gold">ກິນເວລາ</span></h2>
            <div class="lux-card rounded-2xl px-4 py-3 mt-4 max-w-4xl">
              <p class="text-xs sm:text-sm text-[#f5d98b]/85 italic">ກາໂລ ຄະສະຕິ ພູຕານິ ສັພພາເນວະ ສະຫັດຕະນາ · ໂຍ ຈະ ກາລະຄະໂສ ພູໂຕ ສະ ພູຕະປະຈະນິງ ປະຈິ</p>
              <p class="text-xs sm:text-sm text-white/70 mt-1">“ເວລາ ກິນສັດທັງປວງ ພ້ອມທັງຕົວມັນເອງ — ຜູ້ໃດກິນເວລາໄດ້ ຜູ້ນັ້ນ ເຜົາເຄື່ອງເຜົາສັດ (ຕັນຫາ) ໄດ້ໝົດ” <span class="text-white/40">— ຊາດົກ</span></p>
            </div>

            <!-- Two wheels, one shared step -->
            <div class="grid md:grid-cols-2 gap-6 mt-6" @touchstart.passive.stop="tStart($event)" @touchend.stop="stepSwipe($event, d => cmpGo(cmpStep + d))">
              <?php foreach ($sides as $s => [$who, $time, $tone, $note]): ?>
              <div class="lux-card rounded-[28px] p-4 sm:p-5 rise" style="border-color:<?php echo $tone; ?>55; animation-delay:<?php echo $s === 'a' ? '.15' : '0'; ?>s">
                <div class="flex items-baseline justify-between gap-2">
                  <p class="font-black text-lg sm:text-xl" style="color:<?php echo $tone; ?>"><?php echo $who; ?> <span class="text-white/80">· <?php echo $time; ?></span></p>
                  <i class="fas <?php echo $s === 'p' ? 'fa-hourglass-half' : 'fa-sun'; ?>" style="color:<?php echo $tone; ?>"></i>
                </div>
                <p class="text-[11px] sm:text-xs text-white/55 mt-0.5"><?php echo $note; ?></p>
                <svg viewBox="0 0 400 400" class="mx-auto w-full max-w-[340px] h-auto mt-2 select-none">
                  <?php if ($s === 'p'): ?>
                  <circle cx="200" cy="200" r="199" fill="none" stroke="<?php echo $tone; ?>88" stroke-width="1.5" stroke-dasharray="5 7"><animateTransform attributeName="transform" type="rotate" from="0 200 200" to="360 200 200" dur="14s" repeatCount="indefinite"/></circle>
                  <?php endif; ?>
                  <?php foreach ($paticca as $k => $p):
                    $dead = $s === 'a' && $k >= 7; $state = $s === 'a' ? $arahantState($k) : [$p['vatta'], $vattaColor[$p['vatta']]];
                    $name = $s === 'a' && $k === 0 ? 'ວິຊາ' : $p['name']; [$x, $y] = $at(168, $k * 30); [$ix, $iy] = $at(122, $k * 30); ?>
                  <g :style="`opacity:${cmpOn(<?php echo $k; ?>) ? 1 : <?php echo $dead ? '.3' : '.6'; ?>}; transition: opacity .4s`">
                    <path d="<?php echo $arc(140, 196, $k * 30 - 15, $k * 30 + 15); ?>" :fill="cmpOn(<?php echo $k; ?>) ? '<?php echo $state[1]; ?>55' : '<?php echo $dead ? 'rgba(255,255,255,.02)' : $state[1] . '1a'; ?>'"
                          :stroke="cmpOn(<?php echo $k; ?>) ? '#f5d98b' : '<?php echo $state[1]; ?>55'" :stroke-width="cmpOn(<?php echo $k; ?>) ? 2.5 : 1"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 4; ?>" text-anchor="middle" font-size="<?php echo mb_strlen($name) > 8 ? 10 : 12; ?>" font-weight="700" fill="<?php echo $dead ? '#94a3b8' : '#fff'; ?>"<?php echo $dead ? ' text-decoration="line-through"' : ''; ?>><?php echo $name; ?></text>
                    <path d="<?php echo $arc(104, 140, $k * 30 - 15, $k * 30 + 15); ?>" fill="<?php echo $state[1]; ?>12" stroke="<?php echo $state[1]; ?>33" stroke-width="1"/>
                    <text x="<?php echo $ix; ?>" y="<?php echo $iy + 4; ?>" text-anchor="middle" font-size="10" font-weight="700" fill="<?php echo $state[1]; ?>"><?php echo $state[0]; ?></text>
                  </g>
                  <?php endforeach; ?>
                  <?php [$cx1, $cy1] = $at(100, 195); [$cx2, $cy2] = $at(200, 195); [$mx, $my] = $at(170, 195); ?>
                  <?php if ($s === 'a'): ?>
                  <!-- The cut: feeling no longer feeds craving -->
                  <line x1="<?php echo $cx1; ?>" y1="<?php echo $cy1; ?>" x2="<?php echo $cx2; ?>" y2="<?php echo $cy2; ?>" stroke="#f87171" stroke-width="4" stroke-linecap="round"/>
                  <circle cx="<?php echo $mx; ?>" cy="<?php echo $my; ?>" r="13" fill="#0b0d17" stroke="#f87171" stroke-width="2.5" :class="cmpStep===3 && 'animate-pulse'"/>
                  <text x="<?php echo $mx; ?>" y="<?php echo $my + 5; ?>" text-anchor="middle" font-size="14" font-weight="900" fill="#f87171">✕</text>
                  <?php else: ?>
                  <circle cx="<?php echo $mx; ?>" cy="<?php echo $my; ?>" r="7" fill="#0b0d17" stroke="#f5d98b" stroke-width="2.5"/>
                  <?php endif; ?>
                  <!-- Where the current moment is on this wheel -->
                  <g :style="`transform: rotate(${cmpAngle('<?php echo $s; ?>')}deg); transform-origin: 200px 200px; transition: transform .8s cubic-bezier(.34,1.56,.64,1)`">
                    <circle cx="200" cy="4" r="7" fill="<?php echo $tone; ?>" style="filter: drop-shadow(0 0 6px <?php echo $tone; ?>)"/>
                  </g>
                  <circle cx="200" cy="200" r="100" fill="#0b0d17" stroke="<?php echo $tone; ?>66"/>
                  <text x="200" y="186" text-anchor="middle" font-size="17" font-weight="900" fill="<?php echo $tone; ?>"><?php echo $who; ?></text>
                  <text x="200" y="212" text-anchor="middle" font-size="14" font-weight="800" fill="#fff"><?php echo $time; ?></text>
                  <text x="200" y="236" text-anchor="middle" font-size="10" fill="#ffffff99"><?php echo $s === 'p' ? 'ກິເລສ → ກຳ → ວິບາກ ↻' : 'ເວທະນາ ເຢັນ · ບໍ່ສ້າງພົບໃໝ່'; ?></text>
                </svg>
                <!-- What happens on this side at the current step -->
                <div class="rounded-2xl p-3 sm:p-4 mt-2 bg-white/5 border min-h-[92px]" style="border-color:<?php echo $tone; ?>44">
                  <p class="text-[11px] font-black" style="color:<?php echo $tone; ?>" x-text="cmpSteps[cmpStep].short"></p>
                  <p class="text-sm text-white/80 mt-1 leading-relaxed" x-text="cmpSteps[cmpStep].<?php echo $s; ?>"></p>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <!-- Step controls: manual only -->
            <div class="no-print flex flex-wrap items-center gap-2 mt-5">
              <span class="text-xs text-white/55 mr-1"><i class="fas fa-comment-dots text-[#d4af37] mr-1.5"></i>ຕົວຢ່າງ: ມີຄົນເວົ້າຕຳໜິເຮົາ</span>
              <button @click="cmpGo(cmpStep-1)" :disabled="cmpStep===0" class="w-9 h-9 rounded-full lux-card disabled:opacity-30" aria-label="ຂັ້ນຕອນກ່ອນ"><i class="fas fa-backward-step"></i></button>
              <template x-for="(st, n) in cmpSteps" :key="n">
                <button @click="cmpGo(n)" class="px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all" :class="cmpStep===n ? 'lux-btn border-transparent' : 'border-white/20 text-white/65'" x-text="(n+1) + '. ' + st.short"></button>
              </template>
              <button @click="cmpGo(cmpStep+1)" :disabled="cmpStep===cmpSteps.length-1" class="w-9 h-9 rounded-full lux-btn disabled:opacity-30" aria-label="ຂັ້ນຕອນຕໍ່ໄປ"><i class="fas fa-forward-step"></i></button>
            </div>

            <div class="grid lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] gap-5 mt-6">
              <!-- Side-by-side table -->
              <div class="lux-card rounded-[24px] p-4 sm:p-5 rise">
                <div class="grid grid-cols-[minmax(0,.9fr)_minmax(0,1fr)_minmax(0,1fr)] gap-x-3 gap-y-2 text-xs sm:text-sm items-start">
                  <span></span><b class="text-[#fda4af]">ປຸຖຸຊົນ</b><b class="text-[#5eead4]">ພຣະອະຣະຫັນ</b>
                  <?php foreach ([
                    ['fa-brain', 'ຈິດພື້ນຖານ', 'ອະວິຊາ ນອນເນື່ອງຢູ່', 'ວິຊາ ຮູ້ແຈ້ງອະລິຍະສັດ 4'],
                    ['fa-ear-listen', 'ຜັດສະ · ເວທະນາ', 'ເກີດ — ແລ້ວປ່ອຍໃຫ້ໄຫຼໄປ', 'ເກີດ — ແຕ່ຮູ້ທັນ ເປັນພຽງເວທະນາ'],
                    ['fa-fire', 'ຕັນຫາ · ອຸປາທານ', 'ເກີດທຸກຄັ້ງ ເປັນເຊື້ອໄຟ', 'ບໍ່ເກີດ — ໄຟດັບແລ້ວ'],
                    ['fa-hand', 'ກຳ', 'ສ້າງກຳໃໝ່ ສະສົມໄປ', 'ພຽງກິລິຍາ ບໍ່ເປັນກຳ'],
                    ['fa-rotate', 'ວົງລໍ້', 'ໝູນຄົບ 12 ຂໍ້ ບໍ່ຈົບ', 'ຂາດທີ່ ເວທະນາ ✂ ຕັນຫາ'],
                    ['fa-hourglass-half', 'ເວລາ', 'ຄຽດອະດີດ ກັງວົນອະນາຄົດ — ຖືກເວລາກິນ', 'ຢູ່ກັບປັດຈຸບັນ — ກິນເວລາ'],
                  ] as $r): ?>
                  <span class="font-bold text-white/85 border-t border-white/10 pt-2"><i class="fas <?php echo $r[0]; ?> text-[#d4af37] w-4 mr-1"></i><?php echo $r[1]; ?></span>
                  <span class="text-white/70 border-t border-white/10 pt-2"><?php echo $r[2]; ?></span>
                  <span class="text-white/70 border-t border-white/10 pt-2"><?php echo $r[3]; ?></span>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Science view -->
              <div class="sci-card rounded-[24px] p-4 sm:p-5 rise" style="animation-delay:.15s">
                <p class="font-black text-sm sm:text-base"><i class="fas fa-flask text-sky-300 mr-2"></i>ມຸມມອງຈິດຕະວິທະຍາ <span class="text-xs text-white/45 font-normal">(ປຽບທຽບ — ບໍ່ແມ່ນການພິສູດທຳມະ)</span></p>
                <div class="flex items-center justify-between gap-1 mt-3 text-center text-[11px] font-bold">
                  <?php foreach ([['fa-bolt', 'ສິ່ງເຮົາ', 'ຜັດສະ'], ['fa-heart-pulse', 'ຄວາມຮູ້ສຶກ', 'ເວທະນາ'], ['fa-person-running', 'ປະຕິກິລິຍາ', 'ຕັນຫາ']] as $n => $c): ?>
                  <?php if ($n): ?><i class="fas fa-arrow-right <?php echo $n === 2 ? 'text-[#f87171]' : 'text-white/40'; ?>"></i><?php endif; ?>
                  <div class="flex-1"><div class="w-10 h-10 mx-auto rounded-full bg-[#d4af37]/10 ring-1 ring-[#d4af37]/30 flex items-center justify-center text-[#f5d98b]"><i class="fas <?php echo $c[0]; ?>"></i></div><p class="mt-1"><?php echo $c[1]; ?></p><p class="text-white/45"><?php echo $c[2]; ?></p></div>
                  <?php endforeach; ?>
                </div>
                <p class="text-xs sm:text-sm text-white/70 mt-3 leading-relaxed">ໃນຊີວິດປະຈຳວັນ ເຮົາມັກຮູ້ສຶກ “ມັກ / ບໍ່ມັກ” ຕໍ່ສິ່ງທີ່ພົບຢ່າງໄວ ແລ້ວມີແຮງຢາກເຂົ້າຫາ ຫຼື ຢາກໜີ. ປະຕິກິລິຍາທີ່ເຮັດຊໍ້າໆ ມັກກາຍເປັນນິໄສ — ຄ້າຍວົງລໍ້ທີ່ໝູນໄວຂຶ້ນ.</p>
                <p class="text-xs sm:text-sm text-white/70 mt-2 leading-relaxed"><b class="text-[#5eead4]">ສະຕິ</b> ຊ່ວຍໃຫ້ເຫັນ “ຊ່ອງຫວ່າງ” ລະຫວ່າງຄວາມຮູ້ສຶກ ແລະ ປະຕິກິລິຍາ — ກົງກັບຈຸດທີ່ຕັນຫາສືບຕໍ່ຈາກເວທະນາ (ມູລທີ 2). ຄວາມຮູ້ສຶກຍັງເກີດ ແຕ່ບໍ່ຈຳເປັນຕ້ອງຕອບສະໜອງຕາມມັນໂດຍອັດຕະໂນມັດ.</p>
              </div>
            </div>

            <!-- ປະຕິຈຈະສະມຸບບາດ 24: the chain of suffering and the chain of liberation meet at ທຸກຂ໌ -->
            <?php
            $uLeft = array_column($paticca, 'name');
            $uRight = ['ສັດທາ', 'ປາໂມດ', 'ປີຕິ', 'ປັດສັດທິ', 'ສຸກ', 'ສະມາທິ', 'ຍະຖາພູຕະຍານະທັດສະນະ', 'ນິບພິທາ', 'ວິລາຄະ', 'ວິມຸດຕິ', 'ຂະຍະຍານ', 'ນິບພານ'];
            $uY = fn($i) => 50 + $i * 40;
            ?>
            <div class="lux-card rounded-[28px] p-4 sm:p-6 mt-6 rise" style="animation-delay:.25s">
              <p class="kicker text-[11px] sm:text-xs font-bold text-[#d4af37]">ອີກຊັ້ນໜຶ່ງ · ທ່ານພຸດທະທາດ</p>
              <p class="font-black text-xl sm:text-2xl mt-1">ປະຕິຈຈະສະມຸບບາດ 24 <span class="text-white/50 text-base sm:text-lg">— ປະຕິຈຈະສະມຸບບາດ ຊ້ອນ ປະຕິຈຈະສະມຸບບາດ</span></p>
              <p class="text-[11px] sm:text-xs text-white/45 mt-1">ຕັດໄວ້ໃນບາລີ ອຸປະນິສະສູດ (ສູດທີ 3) ທະສະພະລະວັກ · ສັງ. ນິ. 16/35/64</p>

              <div class="grid lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] gap-6 items-center mt-4">
                <svg viewBox="0 0 560 640" class="mx-auto w-full max-w-[520px] h-auto select-none" @touchstart.passive.stop="tStart($event)" @touchend.stop="stepSwipe($event, d => uGo(uSel + d))">
                  <defs>
                    <marker id="u-arrow-p" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="5" markerHeight="5" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#fda4af"/></marker>
                    <marker id="u-arrow-a" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="5" markerHeight="5" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#5eead4"/></marker>
                  </defs>
                  <text x="120" y="22" text-anchor="middle" font-size="12" font-weight="800" fill="#fda4af">↓ ສາຍເກີດທຸກ</text>
                  <text x="440" y="22" text-anchor="middle" font-size="12" font-weight="800" fill="#5eead4">↑ ສາຍດັບທຸກ</text>
                  <!-- Down the left, up the right -->
                  <?php foreach ([['p', $uLeft, 120, '#fda4af', 0], ['a', $uRight, 440, '#5eead4', 13]] as [$s, $list, $x, $c, $base]):
                    foreach ($list as $j => $name): $n = $base + $j; $y = $s === 'p' ? $uY($j) : $uY(11 - $j); ?>
                  <?php if ($j < 11): $y2 = $s === 'p' ? $uY($j + 1) - 16 : $uY(10 - $j) + 16; ?>
                  <line x1="<?php echo $x; ?>" y1="<?php echo $s === 'p' ? $y + 15 : $y - 15; ?>" x2="<?php echo $x; ?>" y2="<?php echo $y2; ?>" stroke="<?php echo $c; ?>99" stroke-width="1.5" marker-end="url(#u-arrow-<?php echo $s; ?>)"/>
                  <?php endif; ?>
                  <g class="cursor-pointer" @click="uGo(<?php echo $n; ?>)">
                    <rect x="<?php echo $x - 88; ?>" y="<?php echo $y - 14; ?>" width="176" height="28" rx="14" :fill="uSel===<?php echo $n; ?> ? '<?php echo $c; ?>' : '<?php echo $c; ?>1a'" stroke="<?php echo $c; ?>88"/>
                    <text x="<?php echo $x; ?>" y="<?php echo $y + 4.5; ?>" text-anchor="middle" font-size="<?php echo mb_strlen($name) > 12 ? 10.5 : 13; ?>" font-weight="700" :fill="uSel===<?php echo $n; ?> ? '#0b0d17' : '#fff'"><?php echo $name; ?></text>
                  </g>
                  <?php endforeach; endforeach; ?>
                  <!-- Into ທຸກຂ໌, and the fork out of it -->
                  <path d="M120 505 Q125 595 225 595" fill="none" stroke="#fda4af" stroke-width="2" marker-end="url(#u-arrow-p)"/>
                  <path d="M335 595 Q435 595 440 505" fill="none" stroke="#5eead4" stroke-width="2.5" marker-end="url(#u-arrow-a)"/>
                  <path d="M250 572 C215 420 215 140 212 60" fill="none" stroke="#fda4af" stroke-width="1.5" stroke-dasharray="5 5" marker-end="url(#u-arrow-p)"/>
                  <text x="246" y="250" font-size="11" font-weight="700" fill="#fda4af">↻ ໂສກ ຄຽດແຄ້ນ</text>
                  <text x="246" y="266" font-size="11" fill="#fda4afcc">ວົນກັບໄປ ອະວິຊາ</text>
                  <text x="246" y="282" font-size="11" fill="#fda4afcc">= ຖືກເວລາກິນ</text>
                  <text x="246" y="350" font-size="11" font-weight="700" fill="#5eead4">↗ ເຫັນທຸກ</text>
                  <text x="246" y="366" font-size="11" fill="#5eead4cc">ສັດທາ ເກີດ</text>
                  <text x="246" y="382" font-size="11" fill="#5eead4cc">= ທາງໄປ ກິນເວລາ</text>
                  <g class="cursor-pointer" @click="uGo(12)">
                    <circle cx="280" cy="595" r="40" :fill="uSel===12 ? '#f5d98b' : '#f5d98b22'" stroke="#f5d98b" stroke-width="2"/>
                    <text x="280" y="592" text-anchor="middle" font-size="20" font-weight="900" :fill="uSel===12 ? '#0b0d17' : '#f5d98b'">ທຸກຂ໌</text>
                    <text x="280" y="612" text-anchor="middle" font-size="9" :fill="uSel===12 ? '#0b0d17' : '#f5d98bcc'">💎 ເພັດໃນຫົວຄາງຄົກ</text>
                  </g>
                </svg>

                <div>
                  <div class="rounded-[24px] p-5 border bg-white/5 min-h-[190px]" :style="`border-color:${ {p:'#fda4af', a:'#5eead4', dukkha:'#f5d98b'}[uSide(uSel)] }66`">
                    <div class="flex items-center justify-between gap-2">
                      <span class="px-3 py-1 rounded-full text-xs font-bold" :style="`color:${ {p:'#fda4af', a:'#5eead4', dukkha:'#f5d98b'}[uSide(uSel)] }; background:${ {p:'#fda4af', a:'#5eead4', dukkha:'#f5d98b'}[uSide(uSel)] }1a`"
                            x-text="{p:'ສາຍເກີດທຸກ · ປຸຖຸຊົນ', a:'ສາຍດັບທຸກ · ທາງສູ່ພຣະອະຣະຫັນ', dukkha:'ທາງແຍກ'}[uSide(uSel)]"></span>
                      <span class="text-xs text-white/45" x-text="(uSel + 1) + ' / ' + uNodes.length"></span>
                    </div>
                    <p class="lux-gold text-2xl sm:text-3xl font-black mt-3" x-text="uNodes[uSel].name"></p>
                    <p class="text-sm text-white/75 mt-2 leading-relaxed" x-text="uNodes[uSel].mean"></p>
                  </div>
                  <div class="no-print flex items-center gap-2 mt-3">
                    <button @click="uGo(uSel-1)" :disabled="uSel===0" class="w-10 h-10 rounded-full lux-card disabled:opacity-30" aria-label="ຂັ້ນຕອນກ່ອນ"><i class="fas fa-backward-step"></i></button>
                    <button @click="uGo(12)" class="h-10 px-4 rounded-full lux-card text-xs font-bold text-[#f5d98b]">💎 ທາງແຍກ ທຸກຂ໌</button>
                    <button @click="uGo(uSel+1)" :disabled="uSel===uNodes.length-1" class="w-10 h-10 rounded-full lux-btn disabled:opacity-30" aria-label="ຂັ້ນຕອນຕໍ່ໄປ"><i class="fas fa-forward-step"></i></button>
                  </div>
                  <div class="grid grid-cols-2 gap-3 mt-4 text-xs sm:text-sm">
                    <div class="rounded-2xl p-3 border border-[#fda4af]/40 bg-[#fda4af]/5">
                      <p class="font-black text-[#fda4af]">ປຸຖຸຊົນ ເມື່ອທຸກ</p>
                      <p class="text-white/70 mt-1">ທຸກ → ໂສກ ຄຽດ → ອະວິຊາ ↻ — ທຸກ ເປັນ “ຄາງຄົກ” ທີ່ໜີ ຫຼື ຕໍ່ສູ້.</p>
                    </div>
                    <div class="rounded-2xl p-3 border border-[#5eead4]/40 bg-[#5eead4]/5">
                      <p class="font-black text-[#5eead4]">ຜູ້ເຫັນທຳ ເມື່ອທຸກ</p>
                      <p class="text-white/70 mt-1">ທຸກ → ສັດທາ → … → ນິບພານ — ພົບ “ເພັດ” ໃນທຸກ ແລ້ວອອກຈາກວົງລໍ້.</p>
                    </div>
                  </div>
                  <p class="text-[11px] text-white/45 mt-3"><i class="fas fa-hand-pointer text-[#d4af37] mr-1.5"></i>ກົດທີ່ແຕ່ລະຂໍ້ ຫຼື ປັດຊ້າຍ-ຂວາ ເທິງແຜນພາບ</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 12. Time with mind — Buddhadasa's sermon -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===11" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <div class="flex flex-wrap items-center gap-2"><p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mr-1">ເວລາກັບຈິດ · ຕອນທີ 12</p><?php echo $dharmaTag; ?></div>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ເວລາ ຄື <span class="lux-gold">ການປ່ຽນແປງຂອງສັງຂານ</span></h2>
            <div class="grid lg:grid-cols-[1.2fr_1fr] gap-5 sm:gap-6 mt-6">
              <div class="rise">
                <div class="lux-card rounded-[24px] overflow-hidden aspect-video">
                  <template x-if="i===11">
                    <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/5-N1ka6iRS8?start=1935&rel=0" title="ເວລາ — ທ່ານພຸດທະທາດ ພິກຂຸ" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                  </template>
                </div>
                <p class="text-xs text-white/55 mt-3"><i class="fab fa-youtube text-red-400 mr-1.5"></i>“ເວລາ” — ທ່ານພຸດທະທາດ ພິກຂຸ · ເທດວັນຂຶ້ນປີໃໝ່ 2510 · ເລີ່ມນາທີ 32:15
                  <a href="https://www.youtube.com/watch?v=5-N1ka6iRS8&t=1935s" target="_blank" rel="noopener" class="no-print text-[#f5d98b] underline ml-1">ເປີດໃນ YouTube</a></p>
              </div>
              <div class="space-y-3">
                <div class="lux-card rounded-2xl p-4 rise" style="animation-delay:.1s">
                  <p class="font-bold text-sm sm:text-base"><i class="fas fa-hourglass-half text-[#d4af37] mr-2"></i>ສັງຂະຕະທາດ — ທຳທີ່ຖືກປຸງແຕ່ງ</p>
                  <p class="text-xs sm:text-sm text-white/60 mt-1.5">ເກີດປາກົດ, ເສື່ອມປາກົດ, ຕັ້ງຢູ່ ແລ້ວປ່ຽນເປັນຢ່າງອື່ນ — ຂັນທ໌ 5 (ຮູບ + ນາມ). ບ່ອນໃດມີການປ່ຽນແປງ ບ່ອນນັ້ນມີ “ເວລາ”.</p>
                </div>
                <div class="lux-card rounded-2xl p-4 rise" style="animation-delay:.2s">
                  <p class="font-bold text-sm sm:text-base"><i class="fas fa-infinity text-[#d4af37] mr-2"></i>ອະສັງຂະຕະທາດ — ທຳທີ່ບໍ່ຖືກປຸງແຕ່ງ</p>
                  <p class="text-xs sm:text-sm text-white/60 mt-1.5">ເກີດບໍ່ປາກົດ, ເສື່ອມບໍ່ປາກົດ — ນິບພານ: ບໍ່ຂຶ້ນກັບການໝູນຂອງໂມງ 12 ຂໍ້.</p>
                </div>
                <div class="rounded-2xl p-4 bg-[#d4af37]/10 border border-[#d4af37]/35 rise" style="animation-delay:.3s">
                  <p class="text-sm text-white/85 leading-relaxed">“ເມື່ອມີສິ່ງນີ້ສິ່ງນີ້ເປັນປັດໄຈ ສິ່ງນີ້ສິ່ງນີ້ຈຶ່ງເກີດຂຶ້ນ”</p>
                  <p class="text-[11px] text-white/45 mt-1">ອິທັປປັດຈະຍະຕາ · ພຣະໄຕຣປິດົກ 16.61.30</p>
                </div>
              </div>
            </div>
            <div class="lux-card rounded-2xl p-4 sm:p-5 mt-5 text-center rise" style="animation-delay:.4s">
              <p class="lux-gold text-base sm:text-xl font-black leading-relaxed">“ຜູ້ໃດເຫັນປະຕິຈຈະສະມຸບບາດ ຜູ້ນັ້ນຊື່ວ່າເຫັນທຳ — ຜູ້ໃດເຫັນທຳ ຜູ້ນັ້ນຊື່ວ່າເຫັນປະຕິຈຈະສະມຸບບາດ”</p>
              <p class="text-[11px] text-white/45 mt-1.5">ພຣະໄຕຣປິດົກ 12.346.359 · ປະຕິຈຈະສະມຸບບາດ ບໍ່ແມ່ນຂອງຕື້ນ — ຢ່າຟ້າວສະຫຼຸບຕາມຄວາມເຂົ້າໃຈຂອງເຮົາເອງ</p>
            </div>
          </div>
        </div>

        <!-- 14. Summary -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===12" x-transition.opacity.duration.500ms>
          <div class="w-full max-w-4xl text-center">
            <i class="fas fa-dharmachakra text-4xl text-[#d4af37] rise"></i>
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mt-5">ສະຫຼຸບ</p>
            <!-- Closing: star → mind → seeing conditions -->
            <div class="space-y-3 mt-5 text-left">
              <div class="sci-card rounded-2xl p-4 sm:p-5 flex gap-4 items-start rise">
                <i class="fas fa-star text-sky-300 text-xl mt-1"></i>
                <p class="text-base sm:text-xl font-bold leading-snug">ເມື່ອເຮົາແນມດາວ ເຮົາຮຽນຮູ້ວ່າ <span class="text-sky-200">ແສງທີ່ມາຮອດເຮົາມື້ນີ້ ເລີ່ມຕົ້ນຈາກເຫດການໃນອະດີດ</span></p>
              </div>
              <div class="text-center"><i class="fas fa-arrow-down text-white/35"></i></div>
              <div class="rounded-2xl p-4 sm:p-5 flex gap-4 items-start bg-[#d4af37]/10 border border-dashed border-[#d4af37]/45 rise" style="animation-delay:.15s">
                <i class="fas fa-dharmachakra text-[#d4af37] text-xl mt-1"></i>
                <p class="text-base sm:text-xl font-bold leading-snug">ເມື່ອເຮົາແນມຈິດ ເຮົາອາດເຫັນຄືກັນວ່າ <span class="text-[#f5d98b]">ຄວາມຮູ້ສຶກ ຄວາມຢາກ ແລະ ຄວາມຍຶດຖື ໃນປັດຈຸບັນ ລ້ວນມີເຫດປັດໄຈທີ່ມາກ່ອນ</span></p>
              </div>
            </div>
            <h2 class="lux-title text-2xl sm:text-4xl mt-7 leading-snug">ການເຫັນເຫດປັດໄຈຢ່າງຊັດເຈນ <span class="lux-gold">ອາດເປັນຈຸດເລີ່ມຕົ້ນ</span><br>ຂອງການບໍ່ຖືກເຫດປັດໄຈນັ້ນ ລາກໄປໂດຍບໍ່ຮູ້ຕົວ</h2>
            <div class="lux-line w-48 mx-auto my-6"></div>
            <div class="grid sm:grid-cols-3 gap-3 text-left">
              <?php foreach ([['fa-flask', 'ວິທະຍາສາດ', 'ສິ່ງທີ່ເຮົາເຫັນເທິງຟ້າ ແມ່ນແສງທີ່ເດີນທາງມາ — ບອກຂໍ້ມູນຂອງວັດຖຸ ຕອນແສງອອກເດີນທາງ', 'sci-card', 'text-sky-300'], ['fa-dharmachakra', 'ທຳມະ', 'ປະສົບການມີເຫດປັດໄຈ: ເວທະນາ → ຕັນຫາ → ອຸປາທານ ນຳໄປສູ່ທຸກໄດ້', 'lux-card', 'text-[#d4af37]'], ['fa-person-praying', 'ການປະຕິບັດ', 'ຝຶກສັງເກດຄວາມຮູ້ສຶກ ແລະ ຄວາມຢາກ ເພື່ອບໍ່ຕອບສະໜອງຕາມຄວາມເຄີຍຊິນ', 'lux-card', 'text-[#5eead4]']] as $k => $s): ?>
              <div class="<?php echo $s[3]; ?> rounded-2xl p-4 rise" style="animation-delay:<?php echo .3 + $k * .12; ?>s">
                <i class="fas <?php echo $s[0]; ?> <?php echo $s[4]; ?>"></i>
                <p class="font-bold text-sm sm:text-base mt-2"><?php echo $s[1]; ?></p>
                <p class="text-xs sm:text-sm text-white/55 mt-1"><?php echo $s[2]; ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <p class="text-xs sm:text-sm text-white/50 mt-6 leading-relaxed">ເຂົ້າໃຈວິທະຍາສາດເລື່ອງແສງ → ໄດ້ມຸມມອງໃໝ່ໃນການພິຈາລະນາການຮັບຮູ້ · ສຶກສາປະຕິຈຈະສະມຸບບາດ → ໄດ້ເຄື່ອງມືອີກແບບໜຶ່ງ ໃນການເບິ່ງເຫດປັດໄຈຂອງປະສົບການ ແລະ ຄວາມທຸກ.</p>
          </div>
        </div>

        <!-- 15. Sources — science and dharma kept apart, so neither appears to vouch for the other -->
        <div class="slide flex px-4 sm:px-16 py-5 sm:py-10" x-show="i===13" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ແຫຼ່ງອ້າງອີງ</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ສອງຊັ້ນ <span class="lux-gold">ສອງແຫຼ່ງ</span></h2>
            <p class="text-white/60 mt-3 max-w-3xl text-sm">ແຫຼ່ງວິທະຍາສາດ ຮອງຮັບສະເພາະຂໍ້ເທັດຈິງເລື່ອງແສງ — ບໍ່ໄດ້ຮັບຮອງການຕີຄວາມທາງທຳມະ. ແຫຼ່ງທຳມະ ອ້າງອີງພຣະສູດ ແຍກຕ່າງຫາກ.</p>
            <div class="grid md:grid-cols-2 gap-4 mt-6">
              <div class="sci-card rounded-[24px] p-5 rise">
                <?php echo $sciTag; ?>
                <ul class="mt-4 space-y-2.5 text-sm">
                  <?php foreach ([
                    ['https://science.nasa.gov/exoplanets/what-is-a-light-year/', 'NASA — What is a light-year?', 'ປີແສງ = ໄລຍະທາງ, ≈ 9.46 ລ້ານລ້ານ km'],
                    ['https://science.nasa.gov/mission/webb/science-overview/science-explainers/how-does-webb-see-back-in-time/', 'NASA — How Does Webb See Back in Time?', 'ຍິ່ງໄກ ຍິ່ງເຫັນຍ້ອນໄປໃນອະດີດ; ດວງຈັນ 1.3 ວິ, ດວງອາທິດ ≈ 8 ນາທີ'],
                    ['https://science.nasa.gov/moon/facts/', 'NASA — Moon facts', 'ໄລຍະທາງສະເລ່ຍ 384,400 km'],
                    ['https://science.nasa.gov/sun/facts/', 'NASA — Sun facts', 'ໂລກ-ດວງອາທິດ ≈ 150 ລ້ານ km; ລະບົບດາວໃກ້ທີ່ສຸດ 4.24 ປີແສງ'],
                    ['https://apod.nasa.gov/apod/ap160118.html', 'NASA APOD — Proxima Centauri: The Closest Star', 'ແສງໃຊ້ເວລາ 4.24 ປີ ມາຮອດເຮົາ'],
                    ['https://physics.nist.gov/cgi-bin/cuu/Value?c', 'NIST — Speed of light in vacuum', '299,792,458 m/s'],
                    ['https://www.bipm.org/en/si-base-units/second', 'BIPM — The second (SI)', 'ຊີຊຽມ-133: 9,192,631,770 ຮອບ = 1 ວິນາທີ'],
                  ] as [$url, $name, $note]): ?>
                  <li><a href="<?php echo $url; ?>" target="_blank" rel="noopener" class="font-bold text-sky-200 underline underline-offset-2"><?php echo $name; ?></a><p class="text-xs text-white/50"><?php echo $note; ?></p></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="lux-card rounded-[24px] p-5 rise" style="animation-delay:.15s">
                <?php echo $dharmaTag; ?>
                <ul class="mt-4 space-y-2.5 text-sm">
                  <?php foreach ([
                    ['https://suttacentral.net/sn12.1', 'ສັງຍຸດຕະນິກາຍ 12.1 — ປະຕິຈຈະສະມຸບບາດສູດ', 'ລຳດັບ 12 ຂໍ້: ອະວິຊາ → … → ຊະລາມໍລະນະ'],
                    ['https://suttacentral.net/sn12.23', 'ສັງຍຸດຕະນິກາຍ 12.23 — ອຸປະນິສະສູດ', 'ສາຍເກີດທຸກ ແລະ ສາຍດັບທຸກ (ປະຕິຈຈະສະມຸບບາດ 24)'],
                    ['https://suttacentral.net/mn28', 'ມັດຊິມະນິກາຍ 28 — ມະຫາຫັດຖິປະໂທປະມະສູດ', '“ຜູ້ໃດເຫັນປະຕິຈຈະສະມຸບບາດ ຜູ້ນັ້ນເຫັນທຳ”'],
                    ['https://suttacentral.net/sn22.48', 'ສັງຍຸດຕະນິກາຍ 22.48 — ຂັນທະສູດ', 'ຂັນ 5: ຮູບ ເວທະນາ ສັນຍາ ສັງຂານ ວິນຍານ'],
                    ['https://suttacentral.net/sn36.6', 'ສັງຍຸດຕະນິກາຍ 36.6 — ສັລລັດຖະສູດ', 'ລູກສອນສອງດອກ: ເວທະນາເກີດ ແຕ່ບໍ່ຕ້ອງເພີ່ມທຸກ'],
                    ['https://suttacentral.net/sn12.61', 'ສັງຍຸດຕະນິກາຍ 12.61 — ອັດສຸຕະວາສູດ', 'ລີງຈັບກິ່ງໄມ້: ຈິດ ມະໂນ ວິນຍານ ເກີດອັນໜຶ່ງ ດັບອັນໜຶ່ງ'],
                    ['https://suttacentral.net/mn38', 'ມັດຊິມະນິກາຍ 38 — ມະຫາຕັນຫາສັງຂະຍະສູດ', 'ວິນຍານ ເກີດເພາະປັດໄຈ; ໄຟ ເອີ້ນຕາມເຊື້ອ'],
                    ['https://suttacentral.net/sn12.53', 'ສັງຍຸດຕະນິກາຍ 12.53 — ສັງໂຍຊະນະສູດ', 'ຕະກຽງ ບໍ່ເຕີມນ້ຳມັນ ກໍດັບ'],
                    ['https://suttacentral.net/an3.76', 'ອັງຄຸດຕະຣະນິກາຍ 3.76 — ພະວະສູດທີ 1', 'ກຳ = ນາ, ວິນຍານ = ເມັດພືດ, ຕັນຫາ = ຄວາມຊຸ່ມ'],
                    ['https://suttacentral.net/sn12.38', 'ສັງຍຸດຕະນິກາຍ 12.38 — ເຈຕະນາສູດ', 'ເຈຕະນາ ແລະ ອະນຸໄສ ເປັນບ່ອນຕັ້ງຂອງວິນຍານ'],
                  ] as [$url, $name, $note]): ?>
                  <li><a href="<?php echo $url; ?>" target="_blank" rel="noopener" class="font-bold text-[#f5d98b] underline underline-offset-2"><?php echo $name; ?></a><p class="text-xs text-white/50"><?php echo $note; ?></p></li>
                  <?php endforeach; ?>
                  <li><p class="font-bold text-white/80">ທ່ານພຸດທະທາດ ພິກຂຸ — ເທດ “ເວລາ” (2510)</p><p class="text-xs text-white/50">ສັງຂະຕະທາດ ແລະ ອະສັງຂະຕະທາດ</p></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Image zoom -->
      <div x-show="zoom" x-transition.opacity @click="zoom=null" class="no-print absolute inset-0 z-40 bg-black/90 backdrop-blur flex items-center justify-center p-4 sm:p-10 cursor-zoom-out">
        <img :src="zoom" alt="" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl">
        <button @click.stop="zoom=null" class="absolute top-4 right-4 w-10 h-10 rounded-full lux-card text-[#f5d98b]" aria-label="ປິດ"><i class="fas fa-xmark"></i></button>
      </div>

      <!-- Narration subtitles -->
      <div x-show="narr.on" x-transition.opacity class="no-print relative z-30 flex-none flex justify-center px-3 sm:px-10 pt-2" x-cloak>
        <div class="lux-card bg-[#070810]/85 rounded-2xl pl-3 pr-2 py-2 max-w-3xl w-full flex items-center gap-3 shadow-2xl" role="status" aria-live="polite">
          <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-[#14110a] text-[11px]" :class="narr.paused || narr.loading ? 'bg-white/30' : 'lux-btn animate-pulse'">
            <i class="fas" :class="narr.loading ? 'fa-spinner fa-spin' : (narr.paused ? 'fa-pause' : 'fa-volume-high')"></i>
          </span>
          <p class="flex-1 min-w-0 text-xs sm:text-sm leading-snug text-white/90 line-clamp-3" :lang="narr.lang" x-text="narr.text"></p>
          <div class="flex shrink-0 rounded-full bg-white/5 p-0.5">
            <template x-for="l in ['lo', 'th']" :key="l">
              <button data-fs @click="setNarrLang(l)" class="px-2 py-1 rounded-full text-[10px] font-black transition-all" :class="narr.lang===l ? 'lux-btn' : 'text-white/60'" x-text="NARR_LABELS[l]"></button>
            </template>
          </div>
          <button data-fs @click="stopNarration()" class="w-7 h-7 shrink-0 rounded-full text-white/60 hover:text-white hover:bg-white/10 flex items-center justify-center" :title="t('stop')" :aria-label="t('stop')"><i class="fas fa-stop text-[11px]"></i></button>
        </div>
      </div>

      <!-- Controls -->
      <div class="no-print deck-controls relative z-30 flex-none flex items-center justify-between gap-4 px-4 sm:px-10 pt-3 pb-[calc(1rem+env(safe-area-inset-bottom))] sm:pb-6">
        <button @click="go(i-1)" :disabled="i===0" class="w-12 h-12 sm:w-11 sm:h-11 shrink-0 rounded-full lux-card flex items-center justify-center disabled:opacity-30 transition-all" aria-label="ກ່ອນໜ້າ"><i class="fas fa-arrow-left"></i></button>
        <!-- Phones: a counter and progress bar instead of 20 tiny dots -->
        <div class="sm:hidden flex-1 min-w-0 text-center">
          <p class="text-[11px] font-bold text-[#c9b98a]/80 truncate"><span class="text-[#f5d98b]" x-text="(i+1) + ' / ' + total"></span> · <span x-text="titles[i]"></span></p>
          <div class="h-1 rounded-full bg-white/10 mt-1.5 overflow-hidden"><div class="h-full rounded-full bg-[#d4af37] transition-all duration-500" :style="`width:${(i+1) / total * 100}%`"></div></div>
        </div>
        <div class="hidden sm:flex items-center gap-1.5 sm:gap-2 overflow-x-auto">
          <template x-for="n in total" :key="n">
            <button @click="go(n-1)" class="h-2 rounded-full transition-all duration-500" :class="i===n-1 ? 'w-8 bg-[#d4af37]' : 'w-2 bg-white/20 hover:bg-white/40'" :aria-label="titles[n-1]"></button>
          </template>
        </div>
        <button @click="go(i+1)" :disabled="i===total-1" class="h-12 sm:h-11 px-5 sm:px-0 sm:w-11 shrink-0 rounded-full lux-btn flex items-center justify-center gap-2 font-black text-sm disabled:opacity-30 transition-all" aria-label="ຕໍ່ໄປ"><span class="sm:hidden">ຕໍ່ໄປ</span><i class="fas fa-arrow-right"></i></button>
      </div>
    </div>

</section>

<script>
  // Per-slide narration: [lang][slide] => [{ t: sentence, a: pre-generated mp3 URL | null }].
  const NARRATION = <?php echo json_encode($narration, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>;
  const NARR_LABELS = { lo: 'ລາວ', th: 'ไทย' };
  const NARR_TTS = { lo: 'lo-LA', th: 'th-TH' };
  const NARR_UI = {
    play: 'ຟັງ', pause: 'ຢຸດຊົ່ວຄາວ', stop: 'ຢຸດ', narrate: 'ຟັງຄຳບັນຍາຍແຕ່ລະສະໄລ (P)', narrLang: 'ພາສາຄຳບັນຍາຍ',
    noVoice: 'ບໍ່ສາມາດເປີດສຽງໄດ້ — ສະແດງສະເພາະຄຳບັນຍາຍ.',
  };

  function timelineDeck() {
    return {
      i: 0,
      // Narration: `run` invalidates callbacks from a sentence that was stopped or skipped.
      narr: { on: false, paused: false, loading: false, lang: 'lo', line: 0, text: '', run: 0, timer: null, warned: false },
      audio: null,
      ttsCache: {},
      t(k) { return NARR_UI[k]; },
      touchX: null,
      touchY: null,
      fullscreen: false,
      zoom: null,
      // Slide zoom (separate from the image lightbox above). Kept inside the page so fullscreen stays on.
      scale: 1,
      minScale: 0.5,
      maxScale: 3,
      pinch: null,
      pinched: false,
      // Paticcasamuppada clock: `step` only grows so the hand never spins backwards past 12.
      step: 0,
      clockView: 'live',
      // Lute-string slider for the middle-way slide (0 = slack, 100 = snapping).
      tension: 50,
      get tone() { return this.tension < 35 ? 'slack' : (this.tension > 75 ? 'tight' : 'good'); },
      get tick() { return ((this.step % 12) + 12) % 12; },
      // Dependent-origination wheel: one step per link, round (ວັດຕະ) or period (ກາລະ).
      wheelLinks: <?php echo json_encode(array_map(fn($p) => array_intersect_key($p, array_flip(['name', 'mean', 'detail', 'vatta', 'kala', 'pali'])), $paticca), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
      wheelMode: 'link',
      wheelStep: 0,
      wheelPos: 0,
      wheelVatta: [
        { key: 'ກິເລສ', short: 'ກິເລສ', color: '#fda4af', sub: 'ກິເລສວັດຕະ — ເຊື້ອໄຟ', body: 'ຄວາມບໍ່ຮູ້ (ອະວິຊາ), ຄວາມຢາກ (ຕັນຫາ) ແລະ ຄວາມຍຶດ (ອຸປາທານ) ເປັນເຊື້ອໄຟ ທີ່ຊຸກຍູ້ໃຫ້ຈິດປຸງແຕ່ງ ແລະ ກະທຳ.' },
        { key: 'ກຳ', short: 'ກຳ', color: '#7dd3fc', sub: 'ກຳມະວັດຕະ — ການກະທຳ', body: 'ເມື່ອມີກິເລສ ຈິດກໍປຸງແຕ່ງ (ສັງຂານ) ເປັນການກະທຳທາງກາຍ, ວາຈາ ແລະ ໃຈ — ສະສົມເປັນກຳມະພົບ.' },
        { key: 'ວິບາກ', short: 'ວິບາກ', color: '#f5d98b', sub: 'ວິບາກວັດຕະ — ຜົນຂອງກຳ', body: 'ຜົນຂອງກຳ ປາກົດເປັນການຮັບຮູ້ ແລະ ຄວາມຮູ້ສຶກ (ວິນຍານ → ເວທະນາ) ຈົນເຖິງການເກີດ ແລະ ແກ່-ຕາຍ. ແລ້ວເວທະນາ ກໍເປັນບ່ອນເກີດກິເລສໃໝ່ — ວົງລໍ້ຈຶ່ງໝູນຕໍ່ໄປ.' },
      ],
      wheelKala: [
        { key: 'past', short: 'ອະດີດເຫດ', color: '#7dd3fc', sub: 'ອະດີດ · ເຫດ', body: 'ອະວິຊາ ແລະ ສັງຂານ ຄືເຫດໃນອະດີດ. ມູລທີ 1: ອະວິຊາ ເປັນຈຸດເລີ່ມຕົ້ນໃນຊ່ວງອະດີດ ທີ່ສົ່ງຜົນມາຫາປັດຈຸບັນ. (ສົນທິ 1: ສັງຂານ + ວິນຍານ)' },
        { key: 'present-result', short: 'ປັດຈຸບັນຜົນ', color: '#f5d98b', sub: 'ປັດຈຸບັນ · ຜົນ', body: 'ວິນຍານ, ນາມຮູບ, ສະລາຍະຕະນະ, ຜັດສະ, ເວທະນາ ຄືຜົນທີ່ປາກົດຕອນນີ້ — ຄືແສງດາວ: ປາກົດໃນປັດຈຸບັນ ແຕ່ເຫດມາຈາກອະດີດ. (ສົນທິ 2: ເວທະນາ + ຕັນຫາ)' },
        { key: 'present-cause', short: 'ປັດຈຸບັນເຫດ', color: '#fda4af', sub: 'ປັດຈຸບັນ · ເຫດ', body: 'ຕັນຫາ, ອຸປາທານ ແລະ ພົບ ຄືເຫດໃໝ່ທີ່ເກີດໃນປັດຈຸບັນ. ມູລທີ 2: ຕັນຫາ ເລີ່ມຕໍ່ຈາກເວທະນາ — ຖ້າມີສະຕິຮູ້ທັນເວທະນາ ຕັນຫາກໍບໍ່ສືບຕໍ່. (ສົນທິ 3: ພົບ + ຊາດ)' },
        { key: 'future', short: 'ອະນາຄົດຜົນ', color: '#c4b5fd', sub: 'ອະນາຄົດ · ຜົນ', body: 'ຊາດ ແລະ ຊະລາມໍລະນະ ຄືຜົນໃນອະນາຄົດ — ພ້ອມໂສກະ, ປະລິເທວະ, ທຸກຂະ, ໂທມະນັດ, ອຸປາຍາສະ ທີ່ກາຍເປັນເຊື້ອຂອງອະວິຊາ ວົນກັບຄືນສູ່ຈຸດເລີ່ມຕົ້ນ.' },
      ],
      wheelAkara: <?php echo json_encode(array_map(fn($g) => array_intersect_key($g, array_flip(['key', 'ex', 'im', 'short', 'color', 'body'])), $akara), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
      wheelSteps() {
        if (this.wheelMode === 'akara') return this.wheelAkara;
        if (this.wheelMode === 'vatta') return this.wheelVatta;
        if (this.wheelMode === 'kala') return this.wheelKala;
        return this.wheelLinks.map(l => ({ short: l.name, color: { 'ກິເລສ': '#fda4af', 'ກຳ': '#7dd3fc', 'ວິບາກ': '#f5d98b' }[l.vatta] }));
      },
      wheelOn(k) {
        const l = this.wheelLinks[k];
        if (this.wheelMode === 'vatta') return l.vatta === this.wheelVatta[this.wheelStep].key;
        if (this.wheelMode === 'kala') return l.kala === this.wheelKala[this.wheelStep].key;
        if (this.wheelMode === 'akara') return this.wheelAkara[this.wheelStep].ex.includes(k);
        return k === this.wheelStep;
      },
      // Links that are not named in the current group but are carried inside it (ອາການ 20).
      wheelHint(k) { return this.wheelMode === 'akara' && this.wheelAkara[this.wheelStep].im.includes(k); },
      wheelSpanOn(a, b) {
        for (let k = a; k <= b; k++) if (this.wheelOn(k)) return true;
        return false;
      },
      wheelInfo() {
        const kalaName = { 'past': 'ອະດີດເຫດ', 'present-result': 'ປັດຈຸບັນຜົນ', 'present-cause': 'ປັດຈຸບັນເຫດ', 'future': 'ອະນາຄົດຜົນ' };
        if (this.wheelMode === 'link') {
          const l = this.wheelLinks[this.wheelStep], next = this.wheelLinks[(this.wheelStep + 1) % 12];
          const s = this.wheelSteps()[this.wheelStep];
          return { tag: l.vatta + ' · ' + kalaName[l.kala], color: s.color, title: (this.wheelStep + 1) + '. ' + l.name, sub: l.mean,
                   body: l.detail + ' → ເປັນປັດໄຈໃຫ້ ' + next.name + '.', pali: l.pali, members: [] };
        }
        const s = this.wheelSteps()[this.wheelStep];
        if (this.wheelMode === 'akara') {
          const chip = k => ({ name: this.wheelLinks[k].name, vatta: this.wheelLinks[k].vatta });
          return { tag: 'ອາການ 20 · ສັງເຂບ 4', color: s.color, title: s.short, sub: 'ປາກົດ ' + s.ex.length + ' ຂໍ້ + ແຝງຢູ່ ' + s.im.length + ' ຂໍ້ = 5', body: s.body, pali: '',
                   members: s.ex.map(k => this.wheelLinks[k].name), shown: s.ex.map(chip), hidden: s.im.map(chip) };
        }
        const members = this.wheelLinks.filter(l => this.wheelMode === 'vatta' ? l.vatta === s.key : l.kala === s.key).map(l => l.name);
        return { tag: this.wheelMode === 'vatta' ? 'ວັດຕະ 3' : 'ກາລະ 3', color: s.color, title: s.short, sub: s.sub, body: s.body, pali: '', members };
      },
      wheelGo(n) {
        const len = this.wheelSteps().length;
        const next = ((n % len) + len) % len;
        if (this.wheelMode === 'link') {
          let d = ((next - this.wheelStep) % 12 + 12) % 12;
          if (d > 6) d -= 12; // turn the pulse the shortest way
          this.wheelPos += d;
        }
        this.wheelStep = next;
      },
      wheelPick(k) { this.wheelSetMode('link'); this.wheelGo(k); },
      wheelSetMode(m) {
        if (this.wheelMode === m) return;
        this.wheelMode = m;
        this.wheelStep = 0;
        this.wheelPos = Math.round(this.wheelPos / 12) * 12;
      },
      // Eaten by time vs eating time: one shared step drives both wheels.
      cmpStep: 0,
      cmpSteps: [
        { k: [0], short: 'ຈຸດເລີ່ມ', p: 'ອະວິຊາ ຍັງນອນເນື່ອງຢູ່ໃນຈິດ — ບໍ່ຮູ້ຕາມເປັນຈິງວ່າ ທຸກມາຈາກໃສ.', a: 'ອະວິຊາ ດັບແລ້ວ → ວິຊາ ເກີດແທນ: ຮູ້ແຈ້ງອະລິຍະສັດ 4 ຢ່າງໝົດຈົດ.' },
        { k: [5], short: 'ຜັດສະ', p: 'ຫູ + ສຽງ + ວິນຍານ ປະຈວບກັນ — ໄດ້ຍິນຄຳຕຳໜິ.', a: 'ຫູ + ສຽງ + ວິນຍານ ປະຈວບກັນຄືກັນ — ພຣະອະຣະຫັນ ກໍຍັງໄດ້ຍິນ.' },
        { k: [6], short: 'ເວທະນາ', p: 'ຮູ້ສຶກບໍ່ພໍໃຈ (ທຸກຂະເວທະນາ) — ແລ້ວປ່ອຍໃຫ້ມັນໄຫຼຕໍ່ ໂດຍບໍ່ຮູ້ຕົວ.', a: 'ເວທະນາ ກໍເກີດ ແຕ່ຮູ້ທັນວ່າ “ເປັນພຽງເວທະນາ” — ບໍ່ມີໃຜເປັນເຈົ້າຂອງ.' },
        { k: [7], short: 'ຕັນຫາ', p: 'ຢາກໃຫ້ຄຳນັ້ນຫາຍໄປ, ຢາກຕອບໂຕ້ — ຕັນຫາ ເກີດຕໍ່ຈາກເວທະນາ (ມູລທີ 2).', a: 'ຕັນຫາ ບໍ່ເກີດ ✂ ວົງລໍ້ຂາດຢູ່ທີ່ນີ້ — ເວທະນາ → ຕັນຫາ (ສົນທິ 2) ບໍ່ຕໍ່ກັນອີກ.' },
        { k: [8], short: 'ອຸປາທານ', p: 'ຍຶດໝັ້ນວ່າ “ເຂົາດູຖູກຂ້ອຍ” — ອັດຕະວາທຸປາທານ ສ້າງ “ຕົວຂ້ອຍ” ຂຶ້ນມາ.', a: 'ບໍ່ມີຫຍັງໃຫ້ຍຶດ — ບໍ່ມີ “ຂ້ອຍ” ທີ່ຈະຖືກດູຖູກ.' },
        { k: [9], short: 'ພົບ', p: 'ກາຍເປັນ “ຜູ້ຖືກດູຖູກ” ຄິດແກ້ແຄ້ນ — ສ້າງກຳໃໝ່ (ກຳມະພົບ).', a: 'ບໍ່ສ້າງກຳໃໝ່ — ການກະທຳ ເປັນພຽງກິລິຍາ ບໍ່ມີເຊື້ອໃຫ້ເກີດຜົນ.' },
        { k: [10, 11], short: 'ຊາດ · ຊະລາມໍລະນະ', p: '“ຂ້ອຍ” ເກີດຂຶ້ນ (ຊາດ) ແລ້ວທົນທຸກ ໂສກ ຄຽດແຄ້ນ (ຊະລາມໍລະນະ) — ເປັນເຊື້ອໃຫ້ອະວິຊາ ວົນກັບໄປຈຸດເລີ່ມ: ເວລາ ກິນເຮົາອີກຮອບ.', a: 'ບໍ່ມີ “ຂ້ອຍ” ເກີດ ຈຶ່ງບໍ່ມີ “ຂ້ອຍ” ແກ່-ຕາຍ — ເລື່ອງຈົບທີ່ເວທະນາ ເຢັນສະຫງົບ: ນີ້ຄື ກິນເວລາ.' },
      ],
      cmpOn(k) { return this.cmpSteps[this.cmpStep].k.includes(k); },
      // The Arahant's marker stops at ເວທະນາ: nothing travels past the cut.
      cmpAngle(side) {
        const k = this.cmpSteps[this.cmpStep].k;
        const at = k.reduce((a, b) => a + b, 0) / k.length * 30;
        return side === 'a' && this.cmpStep >= 3 ? 180 : at;
      },
      cmpGo(n) { this.cmpStep = Math.max(0, Math.min(this.cmpSteps.length - 1, n)); },
      // ປະຕິຈຈະສະມຸບບາດ 24: 12 links down to ທຸກຂ໌ (index 12), then 12 links up to ນິບພານ.
      uNodes: [
        ...<?php echo json_encode(array_map(fn($p) => ['name' => $p['name'], 'mean' => $p['mean'] . ' — ' . $p['detail']], $paticca), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        { name: 'ທຸກຂ໌', mean: '“ເພັດໃນຫົວຄາງຄົກ” — ທຸກ ເບິ່ງໜ້າກຽດຄືຄາງຄົກ ແຕ່ມີເພັດຢູ່ຂ້າງໃນ: ໃຜຫາພົບ ກໍສະບາຍເລີຍ! ປຸຖຸຊົນ ເມື່ອທຸກ ກໍໂສກເສົ້າ ຄຽດແຄ້ນ ກາຍເປັນເຊື້ອຂອງອະວິຊາຮອບໃໝ່. ຜູ້ເຫັນທຳ ເມື່ອທຸກ ກໍເຫັນວ່າ “ນີ້ຄືທຸກ” ແລ້ວ ສັດທາ ເກີດຂຶ້ນ.' },
        { name: 'ສັດທາ', mean: 'ຄວາມເຊື່ອ ຄວາມໝັ້ນໃຈ ໃນພຣະພຸດທະເຈົ້າ ແລະ ທາງດັບທຸກ — ເພາະທຸກ ຈຶ່ງເຫັນຄຸນຄ່າຂອງທຳ.' },
        { name: 'ປາໂມດ', mean: 'ຄວາມປາບປື້ມ ເບີກບານໃຈ ທີ່ໄດ້ພົບທາງອອກ.' },
        { name: 'ປີຕິ', mean: 'ຄວາມອີ່ມເອີບໃຈ.' },
        { name: 'ປັດສັດທິ', mean: 'ກາຍ ແລະ ໃຈ ສະຫງົບລະງັບ ບໍ່ກະວົນກະວາຍ.' },
        { name: 'ສຸກ', mean: 'ຄວາມສຸກທີ່ບໍ່ອາໄສກາມ — ສຸກຈາກຄວາມສະຫງົບ.' },
        { name: 'ສະມາທິ', mean: 'ຈິດຕັ້ງໝັ້ນ ເປັນໜຶ່ງ ພ້ອມທີ່ຈະເຫັນຄວາມຈິງ.' },
        { name: 'ຍະຖາພູຕະຍານະທັດສະນະ', mean: 'ຮູ້ເຫັນຕາມທີ່ເປັນຈິງ: ທຸກສິ່ງ ບໍ່ທ່ຽງ, ເປັນທຸກ, ບໍ່ແມ່ນຕົວຕົນ.' },
        { name: 'ນິບພິທາ', mean: 'ຄວາມເບື່ອໜ່າຍ ໃນສິ່ງທີ່ຖືກປຸງແຕ່ງ — ບໍ່ຢາກໄດ້ ບໍ່ຢາກເປັນອີກ.' },
        { name: 'ວິລາຄະ', mean: 'ຄວາມຄາຍກຳໜັດ — ປ່ອຍວາງ ບໍ່ຍຶດຕິດ.' },
        { name: 'ວິມຸດຕິ', mean: 'ຄວາມຫຼຸດພົ້ນ ຈາກກິເລສທັງປວງ.' },
        { name: 'ຂະຍະຍານ', mean: 'ຮູ້ຊັດວ່າ ກິເລສ (ອາສະວະ) ສິ້ນແລ້ວ — ກິດທີ່ຄວນເຮັດ ໄດ້ເຮັດແລ້ວ.' },
        { name: 'ນິບພານ', mean: 'ດັບເຢັນສະນິດ — ເກີດບໍ່ປາກົດ ເສື່ອມບໍ່ປາກົດ: ເວລາ ບໍ່ມີຫຍັງໃຫ້ກິນ. ນີ້ຄື “ກິນເວລາ”.' },
      ],
      uSel: 12,
      uGo(n) { this.uSel = Math.max(0, Math.min(this.uNodes.length - 1, n)); },
      uSide(n) { return n < 12 ? 'p' : (n > 12 ? 'a' : 'dukkha'); },
      titles: ['ປັດຈຸບັນທີ່ເຮົາຮັບຮູ້', 'ແສງຈາກດວງດາວ', 'ແສງດາວ ກັບ ຈິດ', 'ຈິດເກີດ-ດັບ ທຸກຂະນະ', '3 ອຸປະມາ ຈາກພຣະສູດ', 'ຖ້າບໍ່ມີຫຍັງເດີນທາງ ເປັນຫຍັງອະດີດ ຍັງສົ່ງຜົນເຖິງດຽວນີ້? (ຂັນ 5 ກັບ ອຸປະມາແສງດາວ)', 'ໂມງປະຕິຈຈະສະມຸບບາດ', 'ກາລະ 3: ເຫດ ແລະ ຜົນ', 'ວົງລໍ້ ກິເລສ · ກຳ · ວິບາກ', 'ເວທະນາ → ຕັນຫາ', 'ຖືກເວລາກິນ · ກິນເວລາ', 'ເວລາ ກັບ ຈິດ', 'ສະຫຼຸບ', 'ແຫຼ່ງອ້າງອີງ'],
      get total() { return this.titles.length; },

      zoomTo(z) { this.scale = Math.round(Math.min(this.maxScale, Math.max(this.minScale, z)) * 100) / 100; },
      zoomBy(dir) { this.zoomTo(this.scale + dir * 0.1); },
      initZoom() {
        // Alpine runs init() itself and x-init runs it again; listeners must only be added once.
        if (this.zoomReady) return;
        this.zoomReady = true;
        const dist = t => Math.hypot(t[0].clientX - t[1].clientX, t[0].clientY - t[1].clientY);
        // Ctrl/Cmd + wheel (also a trackpad pinch on macOS) zooms the slide instead of the browser.
        addEventListener('wheel', e => {
          if (!e.ctrlKey && !e.metaKey) return;
          e.preventDefault();
          this.zoomTo(this.scale * Math.exp(-e.deltaY / 300));
        }, { passive: false });
        // Two-finger pinch. Capture phase, because the stepped graphics stop touch events from bubbling.
        addEventListener('touchstart', e => {
          if (e.touches.length === 1) this.pinched = false;
          if (e.touches.length === 2) { this.pinch = { d: dist(e.touches), z: this.scale }; this.pinched = true; }
        }, { capture: true, passive: true });
        addEventListener('touchmove', e => {
          if (!this.pinch || e.touches.length !== 2) return;
          e.preventDefault();
          this.zoomTo(this.pinch.z * dist(e.touches) / this.pinch.d);
        }, { capture: true, passive: false });
        addEventListener('touchend', e => { if (e.touches.length < 2) this.pinch = null; }, { capture: true, passive: true });
        // Safari's own pinch gesture would zoom the whole page.
        ['gesturestart', 'gesturechange'].forEach(t => addEventListener(t, e => e.preventDefault(), { passive: false }));
      },
      init() {
        this.initZoom();
        const q = new URLSearchParams(location.search);
        const s = parseInt(q.get('slide'), 10);
        if (s >= 1 && s <= this.total) this.i = s - 1;
        document.addEventListener('fullscreenchange', () => { this.fullscreen = !!document.fullscreenElement; });
        this.$watch('i', () => this.syncUrl());
        // Browsers only allow fullscreen from a user gesture, so enter it on the first tap/key press after opening.
        const enter = e => { if (!document.fullscreenElement && !e.target.closest?.('[data-fs]')) this.toggleFullscreen(); };
        ['pointerup', 'keydown'].forEach(t => document.addEventListener(t, enter, { once: true }));
        this.initNarration(q);
      },

      // ---- Spoken narration: pre-generated male Edge voices → /api/tts/synthesize → device voice → subtitles only ----
      initNarration(q) {
        if (this.audio) return; // init() runs twice (see initZoom)
        const nl = q.get('narr') || localStorage.getItem('slides_narr');
        if (NARRATION[nl]) this.narr.lang = nl;
        this.audio = new Audio();
        this.audio.preload = 'auto';
        window.addEventListener('pagehide', () => this.stopNarration());
        // Moving to another slide (by hand or auto-advance) narrates that slide from the top.
        this.$watch('i', () => { if (this.narr.on) this.narrateSlide(); });
      },
      setNarrLang(l) {
        if (l === this.narr.lang) return;
        this.narr.lang = l; localStorage.setItem('slides_narr', l);
        if (this.narr.on) this.narrateSlide();
      },
      toggleNarration() {
        if (!this.narr.on) { this.narr.on = true; this.narr.paused = false; return this.narrateSlide(); }
        if (this.narr.paused) { this.narr.paused = false; this.speakLine(); } // restarts the current sentence
        else { this.narr.paused = true; this.haltSpeech(); }
      },
      stopNarration() { this.narr.on = false; this.narr.paused = false; this.narr.text = ''; this.haltSpeech(); },
      haltSpeech() {
        this.narr.run++;
        this.narr.loading = false;
        clearTimeout(this.narr.timer);
        if (this.audio) { this.audio.onended = this.audio.onerror = null; this.audio.pause(); }
        if (window.speechSynthesis) speechSynthesis.cancel();
      },
      narrateSlide() { this.narr.line = 0; this.speakLine(); },
      narrLines() { return NARRATION[this.narr.lang][this.i] || []; },
      // MP3 URL for a sentence: the pre-generated file, else synthesized once by the site's TTS and kept as a blob.
      async audioUrl(line) {
        if (line.a) return line.a;
        const key = this.narr.lang + '|' + line.t;
        if (!(key in this.ttsCache)) {
          this.ttsCache[key] = fetch('<?= url('api/tts/synthesize') ?>', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: line.t, language: NARR_TTS[this.narr.lang] }),
          }).then(r => r.ok ? r.json() : null).then(d => {
            if (!d || !d.audioContent) return null;
            const bin = Uint8Array.from(atob(d.audioContent), c => c.charCodeAt(0));
            return URL.createObjectURL(new Blob([bin], { type: 'audio/mpeg' }));
          }).catch(() => null);
        }
        return this.ttsCache[key];
      },
      // Warm up the next sentence so there is no gap between them.
      prefetch(line) {
        if (!line) return;
        if (line.a) { const a = new Audio(); a.preload = 'auto'; a.src = line.a; } else this.audioUrl(line);
      },
      async speakLine() {
        this.haltSpeech();
        const run = this.narr.run, lines = this.narrLines();
        if (this.narr.line >= lines.length) {
          // A short breath, then the next slide (the `i` watcher narrates it), or finish on the last one.
          this.narr.timer = setTimeout(() => this.i < this.total - 1 ? this.go(this.i + 1) : this.stopNarration(), 900);
          return;
        }
        const line = lines[this.narr.line];
        this.narr.text = line.t;
        if (this.narr.paused) return;
        const next = () => { if (run === this.narr.run) { this.narr.line++; this.speakLine(); } };
        this.prefetch(lines[this.narr.line + 1]);

        this.narr.loading = !line.a;
        const src = await this.audioUrl(line);
        if (run !== this.narr.run) return;
        this.narr.loading = false;
        if (src) {
          this.audio.src = src;
          this.audio.onended = next;
          this.audio.onerror = () => { if (run === this.narr.run) this.speakFallback(line.t, run, next); };
          return this.audio.play().catch(() => { if (run === this.narr.run) this.speakFallback(line.t, run, next); });
        }
        this.speakFallback(line.t, run, next);
      },
      // Offline / server TTS down: the device's own voice (male if it has one), else just time the subtitle.
      speakFallback(text, run, next) {
        const readTime = Math.max(2500, text.length * 80);
        const l = this.narr.lang, voices = window.speechSynthesis ? speechSynthesis.getVoices() : [];
        const mine = voices.filter(v => v.lang.toLowerCase().startsWith(l));
        const voice = mine.find(v => /niwat|chanthavong|male|pattara/i.test(v.name) && !/female/i.test(v.name)) || mine[0];
        if (!voice) {
          if (!this.narr.warned && window.Swal) Swal.fire({ toast: true, position: 'top', icon: 'info', title: this.t('noVoice'), showConfirmButton: false, timer: 4000 });
          this.narr.warned = true;
          this.narr.timer = setTimeout(next, readTime);
          return;
        }
        const u = new SpeechSynthesisUtterance(text);
        u.voice = voice; u.lang = voice.lang; u.rate = .95;
        if (!/niwat|chanthavong|male|pattara/i.test(voice.name)) u.pitch = .8; // nudge a female-only voice lower
        u.onend = next;
        u.onerror = e => { if (!['interrupted', 'canceled'].includes(e.error)) this.narr.timer = setTimeout(next, readTime); };
        this.narr.timer = setTimeout(() => { if (run === this.narr.run) speechSynthesis.speak(u); }, 60);
      },
      setTick(k) {
        let d = ((k - this.tick) % 12 + 12) % 12;
        if (d > 6) d -= 12; // turn the hand the shortest way
        this.step += d;
      },
      syncUrl() {
        const q = new URLSearchParams(location.search);
        q.set('slide', this.i + 1);
        history.replaceState(null, '', location.pathname + '?' + q.toString() + location.hash);
      },
      toggleFullscreen() {
        const el = document.documentElement;
        if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
        else if (el.requestFullscreen) el.requestFullscreen({ navigationUI: 'hide' }).catch(() => {});
      },
      go(n) {
        this.zoom = null;
        this.i = Math.max(0, Math.min(this.total - 1, n));
        this.$nextTick(() => this.$refs.deck.querySelectorAll('.slide').forEach(el => { el.scrollTop = 0; }));
      },
      onKey(e) {
        if (['INPUT', 'TEXTAREA'].includes(e.target.tagName) || e.target.isContentEditable) return;
        if (e.key === 'Escape' && this.zoom) { this.zoom = null; return; }
        if (e.ctrlKey || e.metaKey) {
          const z = { '+': 1, '=': 1, '-': -1, '_': -1, '0': 0 }[e.key];
          if (z !== undefined) { e.preventDefault(); z ? this.zoomBy(z) : this.zoomTo(1); }
          return;
        }
        if (['ArrowRight', 'PageDown', ' '].includes(e.key)) { e.preventDefault(); this.go(this.i + 1); }
        if (['ArrowLeft', 'PageUp'].includes(e.key)) { e.preventDefault(); this.go(this.i - 1); }
        if (e.key === 'p' || e.key === 'P') { e.preventDefault(); this.toggleNarration(); }
        if (e.key === 'Home') this.go(0);
        if (e.key === 'End') this.go(this.total - 1);
      },
      tStart(e) { this.touchX = e.touches[0].clientX; this.touchY = e.touches[0].clientY; },
      // 1 = next, -1 = previous, 0 = not a swipe. Only clearly sideways moves count, so scrolling a long slide never flips it.
      swipeDir(e) {
        if (this.touchX === null) return 0;
        if (this.pinched || this.scale > 1) { this.touchX = null; return 0; }
        const t = e.changedTouches[0], dx = t.clientX - this.touchX, dy = t.clientY - this.touchY;
        this.touchX = null;
        return Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5 ? (dx < 0 ? 1 : -1) : 0;
      },
      onSwipe(e) {
        const d = this.zoom ? 0 : this.swipeDir(e);
        if (d) this.go(this.i + d);
      },
      // Swipe on a stepped graphic (clock, wheel) moves one step instead of changing slide.
      stepSwipe(e, step) {
        const d = this.swipeDir(e);
        if (d) step(d);
      },
      async share() {
        const url = location.origin + location.pathname + '?slide=' + (this.i + 1);
        if (navigator.share) {
          try { await navigator.share({ title: 'ວົງລໍ້ ກິເລສ · ກຳ · ວິບາກ — ຄຳສອນພຸດທະ', url }); } catch (e) {}
          return;
        }
        try { await navigator.clipboard.writeText(url); } catch (e) {}
        Swal.fire({ toast: true, position: 'bottom', icon: 'success', title: 'ສຳເນົາລິ້ງແລ້ວ', showConfirmButton: false, timer: 2200 });
      }
    };
  }
</script>
</body>
</html>
