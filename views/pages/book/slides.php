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

  .deck { --gold: #d4af37; --gold-2: #f5d98b; background: radial-gradient(1200px 600px at 85% -10%, rgba(212,175,55,.14), transparent 60%), radial-gradient(900px 500px at -10% 110%, rgba(99,102,241,.16), transparent 60%), linear-gradient(160deg, #070a18 0%, #05060d 60%, #0d0b07 100%); color: #f4f1ea; }
  .deck { display: flex; flex-direction: column; height: max(620px, calc(100dvh - 190px)); font-family: 'Noto Sans Lao', 'Plus Jakarta Sans', sans-serif; }
  .deck.presenting { position: fixed; inset: 0; z-index: 100; height: 100dvh; border-radius: 0; border: 0; }
  /* Night sky behind every slide */
  .sky { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
  .sky-milkyway { position: absolute; left: -20%; right: -20%; top: 30%; height: 38%; transform: rotate(-18deg); filter: blur(28px); opacity: .5;
    background: radial-gradient(ellipse at 30% 50%, rgba(160,170,255,.16), transparent 60%), radial-gradient(ellipse at 65% 45%, rgba(245,217,139,.10), transparent 55%), linear-gradient(90deg, transparent, rgba(200,205,255,.07) 30%, rgba(255,240,210,.08) 60%, transparent); }
  .sky-layer { position: absolute; top: 0; left: 0; border-radius: 9999px; background: transparent; }
  .sky-dust { width: 1px; height: 1px; animation: sky-breathe 9s ease-in-out infinite alternate; }
  .sky-mid { width: 1.5px; height: 1.5px; animation: sky-breathe 6s ease-in-out -3s infinite alternate; }
  .sky-bright { width: 2px; height: 2px; animation: sky-breathe 4.5s ease-in-out -1.5s infinite alternate; }
  @keyframes sky-breathe { from { opacity: .35; } to { opacity: 1; } }
  .twinkle { position: absolute; border-radius: 9999px; background: var(--c); box-shadow: 0 0 6px 1px var(--c); animation: twinkle var(--d) ease-in-out var(--delay) infinite; }
  .twinkle.glint::before, .twinkle.glint::after { content: ''; position: absolute; left: 50%; top: 50%; background: linear-gradient(90deg, transparent, var(--c), transparent); transform: translate(-50%, -50%); }
  .twinkle.glint::before { width: 900%; height: 1px; }
  .twinkle.glint::after { width: 1px; height: 900%; background: linear-gradient(180deg, transparent, var(--c), transparent); }
  @keyframes twinkle { 0%, 100% { opacity: .15; transform: scale(.6); } 50% { opacity: 1; transform: scale(1); } }
  .shooting-star { position: absolute; width: 140px; height: 1.5px; border-radius: 9999px; background: linear-gradient(90deg, rgba(255,255,255,0), #fff 85%, #f5d98b); opacity: 0; transform: rotate(-32deg); animation: shoot var(--d) ease-in var(--delay) infinite; }
  @keyframes shoot { 0%, 92% { opacity: 0; translate: 0 0; } 93% { opacity: 1; } 100% { opacity: 0; translate: -340px 210px; } }
  @media (prefers-reduced-motion: reduce) { .sky * { animation: none !important; } .shooting-star { display: none; } }
  .slide-stage { flex: 1; min-height: 0; position: relative; }
  .slide { position: absolute; inset: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; scrollbar-color: rgba(212,175,55,.45) transparent; }
  .slide > * { margin: auto; }
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

  /* Light travelling along a path */
  .photon { position: absolute; top: 50%; width: 10px; height: 10px; margin-top: -5px; border-radius: 9999px; background: #f5d98b; box-shadow: 0 0 14px 4px rgba(245,217,139,.7); animation: travel 2.6s linear infinite; }
  @keyframes travel { from { left: 0; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } to { left: calc(100% - 10px); opacity: 0; } }
  /* A star that shines, then goes out */
  .dying-star { animation: die 6s ease-in-out infinite; transform-origin: center; transform-box: fill-box; }
  @keyframes die { 0%, 45% { opacity: 1; transform: scale(1); } 60% { opacity: .15; transform: scale(.6); } 90% { opacity: .15; transform: scale(.6); } 100% { opacity: 1; transform: scale(1); } }
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

  @media print {
    @page { size: A4 landscape; margin: 0; }
    header, footer, .no-print, [x-data="chatbot()"], .fixed { display: none !important; }
    body, main, #timeline { background: #05060d !important; padding: 0 !important; margin: 0 !important; }
    .deck { display: block !important; height: auto !important; position: static !important; border-radius: 0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .deck .slide { display: flex !important; position: relative !important; overflow: visible !important; min-height: 100vh; break-after: page; }
    .rise, .photon, .moment, .dying-star, .vibrate, .note, .shake { animation: none !important; }
  }
</style>
</head>
<body>
<section id="timeline" lang="lo" x-data="timelineDeck()" x-init="init()" @keydown.window="onKey($event)">
    <!-- Deck -->
    <div x-ref="deck" class="deck presenting overflow-hidden"
         @touchstart.passive="touchX = $event.touches[0].clientX" @touchend="onSwipe($event.changedTouches[0].clientX)">

      <?php
      mt_srand(2026);
      $starColors = ['#ffffff', '#ffffff', '#ffffff', '#f5d98b', '#cfe0ff'];
      $shadows = function (int $n, string $blur) use ($starColors) {
          $out = [];
          for ($s = 0; $s < $n; $s++) {
              $out[] = mt_rand(0, 2400) . 'px ' . mt_rand(0, 1500) . 'px ' . $blur . ' ' . $starColors[mt_rand(0, 4)];
          }
          return implode(',', $out);
      };
      $twinkles = [];
      for ($s = 0; $s < 42; $s++) {
          $twinkles[] = [mt_rand(10, 990) / 10, mt_rand(10, 990) / 10, mt_rand(15, 35) / 10, $starColors[mt_rand(0, 4)], mt_rand(25, 70) / 10, -mt_rand(0, 70) / 10, $s % 7 === 0];
      }
      ?>
      <div class="sky no-print" aria-hidden="true">
        <div class="sky-milkyway"></div>
        <div class="sky-layer sky-dust" style="box-shadow: <?php echo $shadows(260, '0'); ?>"></div>
        <div class="sky-layer sky-mid" style="box-shadow: <?php echo $shadows(90, '1px'); ?>"></div>
        <div class="sky-layer sky-bright" style="box-shadow: <?php echo $shadows(30, '3px'); ?>"></div>
        <?php foreach ($twinkles as $t): ?>
        <span class="twinkle<?php echo $t[6] ? ' glint' : ''; ?>" style="left:<?php echo $t[0]; ?>%;top:<?php echo $t[1]; ?>%;width:<?php echo $t[2]; ?>px;height:<?php echo $t[2]; ?>px;--c:<?php echo $t[3]; ?>;--d:<?php echo $t[4]; ?>s;--delay:<?php echo $t[5]; ?>s"></span>
        <?php endforeach; ?>
        <span class="shooting-star" style="left:78%;top:12%;--d:14s;--delay:3s"></span>
        <span class="shooting-star" style="left:42%;top:6%;--d:23s;--delay:11s"></span>
      </div>
      <?php mt_srand(); ?>

      <!-- Top bar -->
      <div class="no-print relative z-30 flex-none flex items-center justify-between gap-3 px-4 sm:px-10 pt-[calc(1rem+env(safe-area-inset-top))] sm:pt-6 pb-2 text-[11px] font-bold text-[#c9b98a]/70">
        <a href="<?= url('/book') ?>" class="flex items-center gap-2 min-w-0 hover:text-[#f5d98b] transition-colors" title="ກັບໄປໜ້າປຶ້ມ">
          <span class="w-9 h-9 shrink-0 rounded-full lux-card flex items-center justify-center text-[#f5d98b]"><i class="fas fa-arrow-left"></i></span>
          <span class="truncate">ຄຳສອນພຸດທະ<span class="text-[#d4af37]">.</span> — <span x-text="titles[i]"></span></span>
        </a>
        <div class="flex items-center gap-3">
          <span><span x-text="String(i+1).padStart(2,'0')"></span> / <span x-text="String(total).padStart(2,'0')"></span></span>
          <button @click="share()" class="w-9 h-9 rounded-full lux-card flex items-center justify-center text-[#f5d98b] hover:bg-[#d4af37]/20 transition-all" title="ແບ່ງປັນລິ້ງ"><i class="fas fa-share-nodes"></i></button>
          <button data-fs @click="toggleFullscreen()" class="w-9 h-9 rounded-full lux-card flex items-center justify-center text-[#f5d98b] hover:bg-[#d4af37]/20 transition-all" :title="fullscreen ? 'ອອກຈາກເຕັມຈໍ' : 'ເຕັມຈໍ'">
            <i class="fas" :class="fullscreen ? 'fa-compress' : 'fa-expand'"></i>
          </button>
        </div>
      </div>

      <div class="slide-stage">
        <!-- 4. The present we feel — flower example -->
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===0" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 1</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">“ປັດຈຸບັນ” ທີ່ເຮົາຮູ້ສຶກ <span class="lux-gold">ບໍ່ໄດ້ມີຢູ່ແທ້</span></h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ຕົວຢ່າງ: ເຮົາເຫັນດອກໄມ້ — ກວ່າຈະຮັບຮູ້ວ່າ “ນັ້ນແມ່ນດອກໄມ້” ສິ່ງທີ່ເຮົາຮັບຮູ້ ແລະ ຮູ້ສຶກໄດ້ນັ້ນ ກໍເປັນ <b class="text-[#f5d98b]">ອະດີດ</b> ໄປແລ້ວ.</p>

            <!-- Illustration: flower → light → eye → mind → "that's a flower" -->
            <div class="lux-card rounded-[28px] p-5 sm:p-8 mt-8 rise">
              <div class="grid grid-cols-[auto_1fr_auto_1fr_auto_1fr_auto] items-center gap-2 sm:gap-4">
                <?php $steps = [['fa-spa', 'ດອກໄມ້', 'ສິ່ງທີ່ຢູ່ຕໍ່ໜ້າ'], ['fa-eye', 'ຕາ', 'ແສງກະທົບຕາ'], ['fa-brain', 'ຈິດຮັບຮູ້', 'ແປຄວາມໝາຍ'], ['fa-comment-dots', '“ດອກໄມ້”', 'ຮູ້ສຶກວ່າ “ຕອນນີ້”']];
                foreach ($steps as $k => $s): ?>
                <div class="text-center">
                  <div class="w-14 h-14 sm:w-20 sm:h-20 mx-auto rounded-full flex items-center justify-center text-xl sm:text-3xl <?php echo $k === 0 ? 'bg-pink-400/15 text-pink-300 ring-1 ring-pink-300/40' : ($k === 3 ? 'lux-btn' : 'bg-[#d4af37]/10 text-[#f5d98b] ring-1 ring-[#d4af37]/30'); ?>"><i class="fas <?php echo $s[0]; ?>"></i></div>
                  <p class="font-bold text-xs sm:text-sm mt-2"><?php echo $s[1]; ?></p>
                  <p class="hidden sm:block text-[11px] text-white/45"><?php echo $s[2]; ?></p>
                </div>
                <?php if ($k < 3): ?>
                <div class="relative h-1 rounded-full bg-white/10"><span class="photon" style="animation-delay:<?php echo $k * .8; ?>s"></span></div>
                <?php endif; endforeach; ?>
              </div>
              <!-- time axis -->
              <div class="mt-8 relative">
                <div class="h-px bg-gradient-to-r from-pink-300/60 via-[#d4af37]/60 to-[#f5d98b]"></div>
                <div class="flex justify-between text-[11px] sm:text-xs text-white/55 mt-2">
                  <span><i class="fas fa-circle text-[6px] text-pink-300 mr-1"></i>ເຫດການເກີດຂຶ້ນ</span>
                  <span class="text-center">ໃຊ້ເວລາ… (ເຖິງຈະໜ້ອຍຫຼາຍ)</span>
                  <span class="text-[#f5d98b] font-bold">ເຮົາຮູ້ສຶກວ່າ “ຕອນນີ້” <i class="fas fa-circle text-[6px] ml-1"></i></span>
                </div>
              </div>
            </div>
            <div class="flex gap-3 items-start lux-card rounded-2xl p-4 mt-5 rise" style="animation-delay:.3s">
              <i class="fas fa-lightbulb text-[#d4af37] mt-1"></i>
              <p class="text-sm text-white/75 leading-relaxed">ເມື່ອເຮົາ “ຮູ້” ວ່າເປັນດອກໄມ້ ຂະນະທີ່ແສງກະທົບຕາ ໄດ້ຜ່ານໄປແລ້ວ — ສິ່ງທີ່ເຮົາເອີ້ນວ່າ “ປັດຈຸບັນ” ຈຶ່ງແມ່ນ <b class="text-[#f5d98b]">ອະດີດທີ່ຫາກໍຜ່ານໄປ</b>.</p>
            </div>
          </div>
        </div>

        <!-- 5. Starlight -->
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===1" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 2</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ທິດສະດີ <span class="lux-gold">ແສງຂອງດວງດາວ</span></h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ແສງດາວທີ່ເຮົາເຫັນໃນຄືນນີ້ ແມ່ນ <b class="text-[#f5d98b]">ອະດີດຂອງດວງດາວ</b> — ແສງໄດ້ເດີນທາງມາຫຼາຍປີ, ຮອດຕອນທີ່ເຮົາເຫັນ ດາວດວງນັ້ນ <b class="text-[#f5d98b]">ອາດຈະດັບໄປແລ້ວ</b>.</p>

            <!-- Illustration: star → long journey of light → eye on earth -->
            <div class="lux-card rounded-[28px] p-5 sm:p-8 mt-8 rise">
              <svg viewBox="0 0 800 220" class="w-full h-auto" role="img" aria-label="ແສງຈາກດາວເດີນທາງມາຮອດຕາເຮົາ">
                <defs>
                  <radialGradient id="starGlow"><stop offset="0%" stop-color="#fff7d6"/><stop offset="40%" stop-color="#f5d98b"/><stop offset="100%" stop-color="#d4af37" stop-opacity="0"/></radialGradient>
                  <linearGradient id="beam" x1="0" x2="1"><stop offset="0%" stop-color="#f5d98b" stop-opacity=".9"/><stop offset="100%" stop-color="#f5d98b" stop-opacity=".15"/></linearGradient>
                </defs>
                <!-- star -->
                <g class="dying-star">
                  <circle cx="90" cy="100" r="60" fill="url(#starGlow)"/>
                  <path d="M90 62 L99 91 L129 91 L105 108 L114 137 L90 120 L66 137 L75 108 L51 91 L81 91 Z" fill="#fff3c4"/>
                </g>
                <!-- light path -->
                <line x1="160" y1="100" x2="640" y2="100" stroke="url(#beam)" stroke-width="2" stroke-dasharray="6 10"/>
                <circle cy="100" r="6" fill="#fff3c4"><animate attributeName="cx" from="160" to="640" dur="3s" repeatCount="indefinite"/></circle>
                <circle cy="100" r="6" fill="#fff3c4"><animate attributeName="cx" from="160" to="640" dur="3s" begin="1s" repeatCount="indefinite"/></circle>
                <circle cy="100" r="6" fill="#fff3c4"><animate attributeName="cx" from="160" to="640" dur="3s" begin="2s" repeatCount="indefinite"/></circle>
                <!-- eye -->
                <g transform="translate(700 100)">
                  <path d="M-50 0 Q0 -40 50 0 Q0 40 -50 0 Z" fill="none" stroke="#f5d98b" stroke-width="3"/>
                  <circle r="15" fill="#d4af37"/><circle r="6" fill="#0b0d17"/>
                </g>
                <text x="90" y="200" text-anchor="middle" fill="#f5d98b" font-size="16" font-weight="700">ດາວ (ອາດດັບໄປແລ້ວ)</text>
                <text x="400" y="80" text-anchor="middle" fill="#ffffff" fill-opacity=".6" font-size="15">ແສງເດີນທາງ ຫຼາຍພັນປີ…</text>
                <text x="700" y="200" text-anchor="middle" fill="#f5d98b" font-size="16" font-weight="700">ເຮົາເຫັນ “ຕອນນີ້”</text>
              </svg>
            </div>
            <div class="grid sm:grid-cols-3 gap-3 mt-5">
              <?php foreach ([['fa-star', 'ດາວສ່ອງແສງ', 'ເຫດການເກີດຂຶ້ນໃນອະດີດ'], ['fa-route', 'ແສງເດີນທາງມາ', 'ໃຊ້ເວລາກວ່າຈະມາຮອດ'], ['fa-eye', 'ເຮົາເຫັນແສງ', 'ແຕ່ຄິດວ່າເປັນ “ປັດຈຸບັນ”']] as $k => $c): ?>
              <div class="flex gap-3 items-start lux-card rounded-2xl p-4 rise" style="animation-delay:<?php echo .2 + $k * .1; ?>s">
                <i class="fas <?php echo $c[0]; ?> text-[#d4af37] mt-1"></i>
                <div><p class="font-bold text-sm"><?php echo $c[1]; ?></p><p class="text-xs text-white/55"><?php echo $c[2]; ?></p></div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- 6. Starlight ↔ mind -->
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===2" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 3</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ປຽບ <span class="lux-gold">ແສງດາວ</span> ກັບ <span class="lux-gold">ການຮັບຮູ້ຂອງຈິດ</span></h2>
            <div class="space-y-3 mt-8">
              <?php foreach ([
                ['fa-star-half-stroke', 'ດາວທີ່ດັບໄປແລ້ວ', 'fa-clock-rotate-left', 'ສິ່ງທີ່ເກີດຂຶ້ນໃນອະດີດ'],
                ['fa-wand-sparkles', 'ແສງທີ່ເດີນທາງມາຮອດຕາເຮົາ', 'fa-hourglass-half', 'ສິ່ງທີ່ປາກົດໃຫ້ເຮົາຮັບຮູ້ໃນປັດຈຸບັນ'],
                ['fa-eye', 'ການເຫັນ', 'fa-brain', 'ການຮັບຮູ້ ຫຼື ວິນຍານ'],
                ['fa-eye-slash', 'ບໍ່ເຫັນກະບວນການຕາມຄວາມເປັນຈິງ', 'fa-cloud', '“ອະວິຊາ” (ຄວາມບໍ່ຮູ້ແຈ້ງ)'],
              ] as $k => $m): ?>
              <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 sm:gap-5 rise" style="animation-delay:<?php echo $k * .12; ?>s">
                <div class="lux-card rounded-2xl p-3 sm:p-4 flex items-center gap-3"><i class="fas <?php echo $m[0]; ?> text-[#f5d98b] w-5 text-center"></i><span class="font-bold text-xs sm:text-base"><?php echo $m[1]; ?></span></div>
                <i class="fas fa-arrow-right-long text-[#d4af37]"></i>
                <div class="rounded-2xl p-3 sm:p-4 flex items-center gap-3 bg-[#d4af37]/10 border border-[#d4af37]/35"><i class="fas <?php echo $m[2]; ?> text-[#f5d98b] w-5 text-center"></i><span class="font-bold text-xs sm:text-base"><?php echo $m[3]; ?></span></div>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="lux-card rounded-3xl p-5 sm:p-6 mt-6 rise" style="animation-delay:.5s">
              <p class="text-sm text-white/65">ແລະເມື່ອຈິດໄປຕີຄວາມຕໍ່ວ່າ</p>
              <p class="lux-gold text-xl sm:text-3xl font-black mt-2 leading-snug">“ນີ້ແມ່ນເຮົາ, ນີ້ແມ່ນຕົວເຮົາ, ນີ້ແມ່ນຕົວຕົນຂອງເຮົາ”</p>
              <p class="text-sm sm:text-base text-white/75 mt-3">ກໍເປັນຈຸດທີ່ຈິດເລີ່ມ <b class="text-[#f5d98b]">ປຸງແຕ່ງ</b> ແລະ <b class="text-[#f5d98b]">ຍຶດຖື</b>.</p>
            </div>
          </div>
        </div>

        <!-- 7. Mind arises and ceases every moment -->
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===3" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 4</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ຈິດ <span class="lux-gold">ເກີດ ແລະ ດັບ</span> ທຸກຂະນະ</h2>
            <p class="text-white/65 mt-4 max-w-3xl text-sm sm:text-base leading-relaxed">ລອງປ່ຽນ “ແສງຈາກດາວທີ່ດັບໄປ” ມາເປັນຈິດໃຈເຮົາ: ຕອນທີ່ເຮົາບໍ່ເຫັນຕາມຄວາມເປັນຈິງ (ມີອະວິຊາ) ວ່າຈິດທີ່ຮັບຮູ້ “ປັດຈຸບັນ” ໄດ້ດັບໄປແລ້ວ — ມັນກໍຈະເບິ່ງຄືມີຕົວຕົນ ມີ “ເຮົາ” ເປັນຜູ້ຄິດ ທັງໆທີ່ມັນເກີດ ແລະ ດັບໄປທຸກສ່ຽວວິນາທີ.</p>

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
          </div>
        </div>

        <!-- 8. Five aggregates & the dead star -->
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===4" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 5</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ຂັນ 5 <span class="lux-gold">ກັບ ແສງດາວທີ່ດັບແລ້ວ</span></h2>
            <div class="rounded-3xl p-5 sm:p-6 mt-6 bg-amber-400/10 border border-amber-300/40 flex gap-4 items-start rise">
              <i class="fas fa-star text-amber-300 text-xl mt-0.5"></i>
              <div class="text-sm sm:text-base text-white/85 leading-relaxed space-y-3">
                <p>ດາວບາງດວງ <b>ດັບໄປແລ້ວ</b> ກາຍເປັນອະດີດ ແຕ່ເຮົາຍັງເຫັນ <b>ລຳແສງ</b> ຂອງມັນຢູ່ໃນປັດຈຸບັນ — <b>“ຈິດດວງກ່ອນ”</b> ກໍເຊັ່ນກັນ: ເກີດຂຶ້ນ ແລ້ວດັບໄປ ສະຫຼາຍເປັນອະດີດແລ້ວ.</p>
                <p>ແຕ່ສິ່ງທີ່ເຮົາຍັງເຫັນ ແລະ ຮູ້ສຶກຢູ່ ແມ່ນອາໄສ <b>ຂັນທັງ 5</b> ສະແດງຜົນໃຫ້ປາກົດ. ໂດຍສະເພາະ <b class="text-amber-200">ສັງຂານຂັນ</b> — ຄວາມປຸງແຕ່ງ ແລະ ຄວາມຈື່ຈຳ — ເຖິງຈິດດວງນັ້ນຈະດັບໄປແລ້ວ ກໍຍັງເຮັດໜ້າທີ່ປຸງແຕ່ງຜົນຂອງອະດີດ ໃຫ້ເຮົາເຫັນ ແລະ ຮູ້ສຶກຢູ່ໃນປັດຈຸບັນ <b>ເໝືອນລຳແສງທີ່ເດີນທາງມາໃຫ້ເຫັນ</b>.</p>
                <div class="flex flex-wrap gap-2 pt-1">
                  <?php foreach (['ຮູບ', 'ເວທະນາ', 'ສັນຍາ', 'ສັງຂານ', 'ວິນຍານ'] as $kh): ?>
                  <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-bold border <?php echo $kh === 'ສັງຂານ' ? 'bg-amber-300/25 border-amber-300 text-amber-100' : 'bg-white/5 border-white/20 text-white/80'; ?>"><?php echo $kh; ?></span>
                  <?php endforeach; ?>
                </div>
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
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===5" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 6</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ໂມງ <span class="lux-gold">ປະຕິຈຈະສະມຸບບາດ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed">ໂມງປະລະມານູ ນັບ 1 ວິນາທີ ຈາກການສັ່ນຂອງອະຕອມຊີຊຽມ <b class="text-[#f5d98b]">9,192,631,770</b> ຄັ້ງ. ຈິດກໍມີ “ຈັງຫວະ” ຂອງມັນ — ໝູນໄປຕາມເຫດປັດໄຈ 12 ຂໍ້ ເໝືອນເຂັມໂມງທີ່ບໍ່ເຄີຍຢຸດ ຖ້າຍັງມີອະວິຊາ.</p>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6 lg:gap-10 items-center mt-6">
              <!-- Clock face -->
              <div class="rise">
                <div class="no-print flex justify-center gap-1 mb-3">
                  <button @click="clockView='live'" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all" :class="clockView==='live' ? 'lux-btn' : 'lux-card text-white/60'">ໂມງເຄື່ອນໄຫວ</button>
                  <button @click="clockView='image'" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all" :class="clockView==='image' ? 'lux-btn' : 'lux-card text-white/60'">ຮູບຕົ້ນສະບັບ</button>
                </div>
                <div x-show="clockView==='live'" class="relative mx-auto w-full max-w-[440px] aspect-square" @touchstart.passive.stop="touchX = $event.touches[0].clientX" @touchend.stop="stepSwipe($event.changedTouches[0].clientX, d => setTick(tick + d))">
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
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===6" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 7</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ກາລະ 3 — <span class="lux-gold">ເຫດ ແລະ ຜົນ ໃນເວລາ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed">ສັງເຂບ 4: ການຫຍໍ້ປະຕິຈຈະສະມຸບບາດ ຕາມເຫດ ແລະ ຜົນ ທີ່ສຳພັນກັບເວລາ.</p>

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
                <p class="font-bold text-sm sm:text-base"><i class="fas fa-star text-[#d4af37] mr-2"></i>ເຊື່ອມກັບແສງດາວ</p>
                <p class="text-sm text-white/70 mt-3 leading-relaxed"><b style="color:#f5d98b">ປັດຈຸບັນຜົນ</b> (ວິນຍານ → ເວທະນາ) ຄື “ແສງດາວ” — ມັນປາກົດຕອນນີ້ ແຕ່ເຫດຂອງມັນເກີດໃນອະດີດ.</p>
                <p class="text-sm text-white/70 mt-2 leading-relaxed"><b style="color:#fda4af">ປັດຈຸບັນເຫດ</b> (ຕັນຫາ → ພົບ) ຄືຈຸດທີ່ຈິດ “ຕີຄວາມ” ແລະ ຍຶດຖື — ແລະ ເປັນເຫດໃໝ່ ທີ່ສົ່ງຜົນໄປຫາອະນາຄົດ.</p>
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
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===7" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 8</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ວົງລໍ້ <span class="lux-gold">ກິເລສ · ກຳ · ວິບາກ</span></h2>
            <p class="text-white/65 mt-3 max-w-3xl text-sm sm:text-base leading-relaxed">ການເຮັດວຽກຂອງປະຕິຈຈະສະມຸບບາດ ໃນນັຍຂອງ ກິເລສ-ກຳ-ວິບາກ ແລະ ອະດີດ-ປັດຈຸບັນ-ອະນາຄົດ — ກົດເບິ່ງແຕ່ລະຂັ້ນຕອນໄດ້.</p>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] gap-6 lg:gap-10 items-start mt-6">
              <!-- Wheel -->
              <div class="rise" @touchstart.passive.stop="touchX = $event.touches[0].clientX" @touchend.stop="stepSwipe($event.changedTouches[0].clientX, d => wheelGo(wheelStep + d))">
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

        <!-- 11b. Eaten by time vs eating time: the wheel of an ordinary person and of an Arahant -->
        <?php
        $sides = [
            'p' => ['ປຸຖຸຊົນ', 'ຖືກເວລາກິນ', '#fda4af', 'ຍັງມີກິເລສ — ວົງລໍ້ໝູນຄົບ 12 ຂໍ້ ບໍ່ຮູ້ຈົບ'],
            'a' => ['ພຣະອະຣະຫັນ', 'ກິນເວລາ', '#5eead4', 'ໝົດກິເລສ — ວົງລໍ້ຂາດທີ່ ເວທະນາ ✂ ຕັນຫາ'],
        ];
        // What each link becomes for the Arahant: ວິຊາ replaces ອະວິຊາ, actions are mere ກິລິຍາ, the senses still work, the rest has ceased.
        $arahantState = fn($k) => $k === 0 ? ['ວິຊາ', '#5eead4'] : ($k === 1 ? ['ກິລິຍາ', '#94a3b8'] : ($k <= 6 ? ['ຮັບຮູ້', '#5eead4'] : ['ດັບ', '#64748b']));
        ?>
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===8" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 9</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ຖືກເວລາກິນ <span class="text-white/40">vs</span> <span class="lux-gold">ກິນເວລາ</span></h2>
            <div class="lux-card rounded-2xl px-4 py-3 mt-4 max-w-4xl">
              <p class="text-xs sm:text-sm text-[#f5d98b]/85 italic">ກາໂລ ຄະສະຕິ ພູຕານິ ສັພພາເນວະ ສະຫັດຕະນາ · ໂຍ ຈະ ກາລະຄະໂສ ພູໂຕ ສະ ພູຕະປະຈະນິງ ປະຈິ</p>
              <p class="text-xs sm:text-sm text-white/70 mt-1">“ເວລາ ກິນສັດທັງປວງ ພ້ອມທັງຕົວມັນເອງ — ຜູ້ໃດກິນເວລາໄດ້ ຜູ້ນັ້ນ ເຜົາເຄື່ອງເຜົາສັດ (ຕັນຫາ) ໄດ້ໝົດ” <span class="text-white/40">— ຊາດົກ</span></p>
            </div>

            <!-- Two wheels, one shared step -->
            <div class="grid md:grid-cols-2 gap-6 mt-6" @touchstart.passive.stop="touchX = $event.touches[0].clientX" @touchend.stop="stepSwipe($event.changedTouches[0].clientX, d => cmpGo(cmpStep + d))">
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
              <div class="lux-card rounded-[24px] p-4 sm:p-5 rise" style="animation-delay:.15s">
                <p class="font-black text-sm sm:text-base"><i class="fas fa-flask text-[#d4af37] mr-2"></i>ທຽບກັບວິທະຍາສາດ</p>
                <div class="flex items-center justify-between gap-1 mt-3 text-center text-[11px] font-bold">
                  <?php foreach ([['fa-bolt', 'ສິ່ງເຮົາ', 'ຜັດສະ'], ['fa-heart-pulse', 'ຄວາມຮູ້ສຶກ', 'ເວທະນາ'], ['fa-person-running', 'ປະຕິກິລິຍາ', 'ຕັນຫາ']] as $n => $c): ?>
                  <?php if ($n): ?><i class="fas fa-arrow-right <?php echo $n === 2 ? 'text-[#f87171]' : 'text-white/40'; ?>"></i><?php endif; ?>
                  <div class="flex-1"><div class="w-10 h-10 mx-auto rounded-full bg-[#d4af37]/10 ring-1 ring-[#d4af37]/30 flex items-center justify-center text-[#f5d98b]"><i class="fas <?php echo $c[0]; ?>"></i></div><p class="mt-1"><?php echo $c[1]; ?></p><p class="text-white/45"><?php echo $c[2]; ?></p></div>
                  <?php endforeach; ?>
                </div>
                <p class="text-xs sm:text-sm text-white/70 mt-3 leading-relaxed">ສະໝອງ ປະເມີນທຸກສິ່ງເຮົາ ເປັນ “ດີ / ບໍ່ດີ” ໂດຍອັດຕະໂນມັດ (ເວທະນາ) ແລ້ວລະບົບ “ຢາກໄດ້ - ຢາກໜີ” ກໍຕອບສະໜອງ (ຕັນຫາ). ຍິ່ງເຮັດຊໍ້າ ເສັ້ນທາງປະສາດນັ້ນ ຍິ່ງແຂງແຮງ ກາຍເປັນນິໄສ — ຄືວົງລໍ້ທີ່ໝູນໄວຂຶ້ນ.</p>
                <p class="text-xs sm:text-sm text-white/70 mt-2 leading-relaxed"><b class="text-[#5eead4]">ສະຕິ</b> ຄືການເຫັນ “ຊ່ອງຫວ່າງ” ລະຫວ່າງ ຄວາມຮູ້ສຶກ ແລະ ປະຕິກິລິຍາ — ຈຸດດຽວກັນ ທີ່ຕັນຫາເລີ່ມຕົ້ນຕໍ່ຈາກເວທະນາ (ມູລທີ 2). ສິ່ງທີ່ບໍ່ຖືກປຸງແຕ່ງ (ອະສັງຂະຕະທາດ) “ເກີດບໍ່ປາກົດ ເສື່ອມບໍ່ປາກົດ” — ເວລາ ຈຶ່ງບໍ່ມີຫຍັງໃຫ້ກິນ.</p>
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
                <svg viewBox="0 0 560 640" class="mx-auto w-full max-w-[520px] h-auto select-none" @touchstart.passive.stop="touchX = $event.touches[0].clientX" @touchend.stop="stepSwipe($event.changedTouches[0].clientX, d => uGo(uSel + d))">
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
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===9" x-transition.opacity.duration.500ms>
          <div class="w-full">
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37]">ເວລາກັບຈິດ · ຕອນທີ 9</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3">ເວລາ ຄື <span class="lux-gold">ການປ່ຽນແປງຂອງສັງຂານ</span></h2>
            <div class="grid lg:grid-cols-[1.2fr_1fr] gap-5 sm:gap-6 mt-6">
              <div class="rise">
                <div class="lux-card rounded-[24px] overflow-hidden aspect-video">
                  <template x-if="i===9">
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
        <div class="slide flex px-6 sm:px-16 py-8 sm:py-10" x-show="i===10" x-transition.opacity.duration.500ms>
          <div class="w-full max-w-4xl text-center">
            <i class="fas fa-dharmachakra text-4xl text-[#d4af37] rise"></i>
            <p class="kicker text-xs sm:text-sm font-bold text-[#d4af37] mt-5">ສະຫຼຸບ</p>
            <h2 class="lux-title text-3xl sm:text-5xl mt-3 leading-snug">ສິ່ງທີ່ເຮົາເຫັນ ແມ່ນ <span class="lux-gold">ແສງ</span><br>ບໍ່ແມ່ນ <span class="lux-gold">ດວງດາວ</span></h2>
            <div class="lux-line w-48 mx-auto my-7"></div>
            <div class="grid sm:grid-cols-3 gap-3 text-left">
              <?php foreach ([['fa-clock', 'ປັດຈຸບັນທີ່ຮູ້ສຶກ', 'ແທ້ຈິງແລ້ວ ແມ່ນອະດີດທີ່ຫາກໍຜ່ານໄປ'], ['fa-rotate', 'ຈິດເກີດ-ດັບ', 'ບໍ່ມີດວງໃດຢູ່ຄົງທີ່ ຫຼື ເດີນທາງຕໍ່ໄປ'], ['fa-hand', 'ເຫັນຕາມຈິງ', 'ຫຼຸດການຍຶດຖືວ່າ “ນີ້ແມ່ນເຮົາ”']] as $k => $s): ?>
              <div class="lux-card rounded-2xl p-4 rise" style="animation-delay:<?php echo $k * .12; ?>s">
                <i class="fas <?php echo $s[0]; ?> text-[#d4af37]"></i>
                <p class="font-bold text-sm sm:text-base mt-2"><?php echo $s[1]; ?></p>
                <p class="text-xs sm:text-sm text-white/55 mt-1"><?php echo $s[2]; ?></p>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      <!-- Image zoom -->
      <div x-show="zoom" x-transition.opacity @click="zoom=null" class="no-print absolute inset-0 z-40 bg-black/90 backdrop-blur flex items-center justify-center p-4 sm:p-10 cursor-zoom-out">
        <img :src="zoom" alt="" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl">
        <button @click.stop="zoom=null" class="absolute top-4 right-4 w-10 h-10 rounded-full lux-card text-[#f5d98b]" aria-label="ປິດ"><i class="fas fa-xmark"></i></button>
      </div>

      <!-- Controls -->
      <div class="no-print deck-controls relative z-30 flex-none flex items-center justify-between gap-4 px-4 sm:px-10 pt-3 pb-[calc(1rem+env(safe-area-inset-bottom))] sm:pb-6">
        <button @click="go(i-1)" :disabled="i===0" class="w-11 h-11 rounded-full lux-card flex items-center justify-center disabled:opacity-30 transition-all" aria-label="ກ່ອນໜ້າ"><i class="fas fa-arrow-left"></i></button>
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto">
          <template x-for="n in total" :key="n">
            <button @click="go(n-1)" class="h-2 rounded-full transition-all duration-500" :class="i===n-1 ? 'w-8 bg-[#d4af37]' : 'w-2 bg-white/20 hover:bg-white/40'" :aria-label="titles[n-1]"></button>
          </template>
        </div>
        <button @click="go(i+1)" :disabled="i===total-1" class="w-11 h-11 rounded-full lux-btn flex items-center justify-center disabled:opacity-30 transition-all" aria-label="ຕໍ່ໄປ"><i class="fas fa-arrow-right"></i></button>
      </div>
    </div>
</section>

<script>
  function timelineDeck() {
    return {
      i: 0,
      touchX: null,
      fullscreen: false,
      zoom: null,
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
      titles: ['ປັດຈຸບັນທີ່ເຮົາຮູ້ສຶກ', 'ແສງຈາກດວງດາວ', 'ແສງດາວ ກັບ ຈິດ', 'ຈິດເກີດ-ດັບ ທຸກຂະນະ', 'ຂັນ 5 ກັບ ແສງດາວ', 'ໂມງປະຕິຈຈະສະມຸບບາດ', 'ກາລະ 3: ເຫດ ແລະ ຜົນ', 'ວົງລໍ້ ກິເລສ · ກຳ · ວິບາກ', 'ຖືກເວລາກິນ · ກິນເວລາ', 'ເວລາ ກັບ ຈິດ', 'ສະຫຼຸບ'],
      get total() { return this.titles.length; },

      init() {
        const q = new URLSearchParams(location.search);
        const s = parseInt(q.get('slide'), 10);
        if (s >= 1 && s <= this.total) this.i = s - 1;
        document.addEventListener('fullscreenchange', () => { this.fullscreen = !!document.fullscreenElement; });
        this.$watch('i', () => this.syncUrl());
        // Browsers only allow fullscreen from a user gesture, so enter it on the first tap/key press after opening.
        const enter = e => { if (!document.fullscreenElement && !e.target.closest?.('[data-fs]')) this.toggleFullscreen(); };
        ['pointerup', 'keydown'].forEach(t => document.addEventListener(t, enter, { once: true }));
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
        if (['ArrowRight', 'PageDown', ' '].includes(e.key)) { e.preventDefault(); this.go(this.i + 1); }
        if (['ArrowLeft', 'PageUp'].includes(e.key)) { e.preventDefault(); this.go(this.i - 1); }
        if (e.key === 'Home') this.go(0);
        if (e.key === 'End') this.go(this.total - 1);
      },
      onSwipe(x) {
        if (this.touchX === null) return;
        const dx = x - this.touchX;
        if (Math.abs(dx) > 50) this.go(this.i + (dx < 0 ? 1 : -1));
        this.touchX = null;
      },
      // Swipe on a stepped graphic (clock, wheel) moves one step instead of changing slide.
      stepSwipe(x, step) {
        if (this.touchX === null) return;
        const dx = x - this.touchX;
        if (Math.abs(dx) > 40) step(dx < 0 ? 1 : -1);
        this.touchX = null;
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
