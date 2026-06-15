/* === Reader Shared Features: font-size, fullscreen, share, TTS, audio === */

/* Font Size */
var currentFontSize = parseInt(localStorage.getItem('buddhaword_fontsize') || '20', 10);

function changeFontSize(delta) {
    currentFontSize = Math.min(Math.max(12, currentFontSize + delta), 40);
    var el = document.getElementById('readerContent');
    if (el) el.style.fontSize = currentFontSize + 'px';
    localStorage.setItem('buddhaword_fontsize', currentFontSize.toString());
}

(function() {
    var el = document.getElementById('readerContent');
    if (el) el.style.fontSize = currentFontSize + 'px';
})();

/* Fullscreen */
function getFS() {
    return document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
}

function requestFS(el) {
    if (el.requestFullscreen) return el.requestFullscreen();
    if (el.webkitRequestFullscreen) { el.webkitRequestFullscreen(); return Promise.resolve(); }
    if (el.mozRequestFullScreen) { el.mozRequestFullScreen(); return Promise.resolve(); }
    if (el.msRequestFullscreen) { el.msRequestFullscreen(); return Promise.resolve(); }
    return Promise.reject(new Error('Fullscreen not supported'));
}

function exitFS() {
    if (document.exitFullscreen) return document.exitFullscreen();
    if (document.webkitExitFullscreen) { document.webkitExitFullscreen(); return Promise.resolve(); }
    if (document.mozCancelFullScreen) { document.mozCancelFullScreen(); return Promise.resolve(); }
    if (document.msExitFullscreen) { document.msExitFullscreen(); return Promise.resolve(); }
    return Promise.reject(new Error('Fullscreen not supported'));
}

function toggleReaderFullscreen() {
    var el = document.getElementById('readerFullscreenTarget');
    var icon = document.getElementById('fsIcon');
    if (!getFS()) {
        if (el) {
            requestFS(el).then(function() {
                el.style.maxWidth = '100%';
                el.style.overflowY = 'auto';
                if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            }).catch(function() {});
        }
    } else {
        exitFS().then(function() {
            if (el) { el.style.maxWidth = ''; el.style.overflowY = ''; }
            if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />';
        }).catch(function() {});
    }
}

document.addEventListener('fullscreenchange', function() {
    var el = document.getElementById('readerFullscreenTarget');
    var icon = document.getElementById('fsIcon');
    if (!getFS() && el) { el.style.maxWidth = ''; el.style.overflowY = ''; }
    if (!getFS() && icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />';
});
document.addEventListener('webkitfullscreenchange', function() { document.dispatchEvent(new Event('fullscreenchange')); });
document.addEventListener('mozfullscreenchange', function() { document.dispatchEvent(new Event('fullscreenchange')); });
document.addEventListener('MSFullscreenChange', function() { document.dispatchEvent(new Event('fullscreenchange')); });

/* Share */
function shareReader(btn) {
    var h1 = document.querySelector('h1');
    var title = h1 ? (h1.textContent || h1.innerText || '').trim() : 'ອ່ານ';
    var url = window.location.href;
    var text = 'ອ່ານ ' + title + ' ທີ່ ຄຳສອນພຸດທະ';
    if (navigator.share) {
        navigator.share({ title: title, text: text, url: url }).catch(function() {});
    } else {
        navigator.clipboard.writeText(url).then(function() {
            if (btn) {
                var orig = btn.innerHTML;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
                setTimeout(function() { btn.innerHTML = orig; }, 2000);
            }
        }).catch(function() {});
    }
}

/* TTS */
var ttsPlaying = false;
var ttsOrigHTML = null;
var ttsAudioCtx = null;
var ttsSource = null;
var ttsInterval = null;
var ttsTimeout = null;
var ttsPaused = false;
var ttsProgressInterval = null;
var ttsLang = '';
var ttsPlaybackRate = 1.0;

function detectLanguage(text) {
    var laoCount = (text.match(/[\u{0E80}-\u{0EFF}]/gu) || []).length;
    var thaiCount = (text.match(/[\u{0E00}-\u{0E7F}]/gu) || []).length;
    var engCount = (text.match(/[a-zA-Z]/g) || []).length;
    if (laoCount > thaiCount && laoCount > engCount) return 'lo-LA';
    if (thaiCount > laoCount && thaiCount > engCount) return 'th-TH';
    return 'en-US';
}

function getReaderText() {
    var el = document.getElementById('readerContent');
    if (!el) return '';
    return (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim();
}

function stopTTS() {
    if (ttsTimeout) { clearTimeout(ttsTimeout); ttsTimeout = null; }
    window.__ttsStarted = false;
    ttsPaused = false;
    if (ttsSource) { try { ttsSource.stop(); } catch(e) {} ttsSource = null; }
    if (ttsInterval) { clearInterval(ttsInterval); ttsInterval = null; }
    if (ttsProgressInterval) { clearInterval(ttsProgressInterval); ttsProgressInterval = null; }
    ttsPlaying = false;
    updateTTSIcon();
    var btn = document.getElementById('ttsBtn');
    if (btn) { btn.classList.remove('text-green-300', 'bg-green-500/20'); btn.classList.add('text-white/70'); }
    var ctrl = document.getElementById('ttsControls');
    if (ctrl) ctrl.style.display = 'none';
    if (ttsOrigHTML) {
        var el = document.getElementById('readerContent');
        if (el) el.innerHTML = ttsOrigHTML;
        ttsOrigHTML = null;
    }
}

function formatTTSTime(sec) {
    if (isNaN(sec) || sec < 0) return '0:00';
    var m = Math.floor(sec / 60);
    var s = Math.floor(sec % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}

function splitTextChunks(text, maxLen) {
    if (text.length <= maxLen) return [text];
    var chunks = [];
    var remaining = text;
    while (remaining.length > 0) {
        if (remaining.length <= maxLen) { chunks.push(remaining); break; }
        var idx = remaining.lastIndexOf('.', maxLen);
        if (idx < maxLen * 0.3) idx = remaining.lastIndexOf(' ', maxLen);
        if (idx < maxLen * 0.3) idx = maxLen;
        else idx++;
        chunks.push(remaining.substring(0, idx).trim());
        remaining = remaining.substring(idx).trim();
    }
    return chunks;
}

function toggleTTS() {
    if (ttsPlaying) {
        if (ttsPaused) {
            ttsAudioCtx.resume();
            ttsPaused = false;
            updateTTSIcon();
            updateTTSPlayPauseIcon(false);
        } else {
            ttsAudioCtx.suspend();
            ttsPaused = true;
            updateTTSIcon();
            updateTTSPlayPauseIcon(true);
        }
        return;
    }
    var text = getReaderText();
    if (!text) return;

    stopTTS();

    var lang = detectLanguage(text);
    var textEl = document.getElementById('readerContent');
    if (!textEl) return;

    ttsOrigHTML = textEl.innerHTML;
    textEl.innerHTML = textEl.innerHTML.replace(/(<[^>]+>)|(\S+)|(\s+)/gi, function(m, tag) {
        if (tag) return tag;
        return '<span class="tts-w">' + m + '</span>';
    });

    ttsLang = lang;
    ttsPlaybackRate = (lang === 'th-TH' || lang === 'lo-LA') ? 0.8 : 1.0;
    updateTTSIcon();
    var btn = document.getElementById('ttsBtn');
    if (btn) { btn.classList.remove('text-white/70'); btn.classList.add('text-green-300', 'bg-green-500/20'); }
    var ctrl = document.getElementById('ttsControls');
    if (ctrl) ctrl.style.display = 'flex';
    updateTTSPlayPauseIcon(false);

    if (!ttsAudioCtx) ttsAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (ttsAudioCtx.state === 'suspended') ttsAudioCtx.resume();

    var words = textEl.querySelectorAll('.tts-w');
    var chunks = splitTextChunks(text, 800);
    var chunkQueue = {};
    var allDecodedChunks = [];
    var nextToPlay = 0;
    var seqWordOffset = 0;
    var decodedDuration = 0;
    var isSeqPlaying = false;
    var firstChunkStartTime = 0;
    var totalEstDuration = text.length / 4.5;

    function playNextInSequence() {
        if (isSeqPlaying || !ttsPlaying) return;
        while (chunkQueue[nextToPlay]) {
            var item = chunkQueue[nextToPlay];
            delete chunkQueue[nextToPlay];
            nextToPlay++;
            playSingleChunk(item);
            return;
        }
        if (nextToPlay >= chunks.length) stopTTS();
    }

    function playSingleChunk(item) {
        isSeqPlaying = true;
        if (firstChunkStartTime === 0) firstChunkStartTime = ttsAudioCtx.currentTime;

        ttsSource = ttsAudioCtx.createBufferSource();
        ttsSource.buffer = item.buffer;
        ttsSource.playbackRate.value = ttsPlaybackRate;
        ttsSource.connect(ttsAudioCtx.destination);

        var tpIdx = 0;
        var chunkStartTime = ttsAudioCtx.currentTime;

        if (ttsProgressInterval) clearInterval(ttsProgressInterval);
        ttsProgressInterval = setInterval(function() {
            if (!ttsPlaying) { clearInterval(ttsProgressInterval); ttsProgressInterval = null; return; }
            var elapsed = (ttsAudioCtx.currentTime - firstChunkStartTime) * ttsPlaybackRate;
            var pct = totalEstDuration > 0 ? Math.min(100, (elapsed / totalEstDuration) * 100) : 0;
            var pEl = document.getElementById('ttsProgress');
            var tEl = document.getElementById('ttsTime');
            if (pEl) pEl.style.width = pct + '%';
            if (tEl) tEl.textContent = formatTTSTime(elapsed) + ' / ' + formatTTSTime(totalEstDuration);
        }, 200);

        ttsInterval = setInterval(function() {
            if (!ttsPlaying) { clearInterval(ttsInterval); return; }
            var elapsed = (ttsAudioCtx.currentTime - chunkStartTime) * ttsPlaybackRate;
            while (tpIdx < item.timepoints.length && elapsed >= item.timepoints[tpIdx]) {
                words.forEach(function(w) { w.classList.remove('tts-active'); });
                var gIdx = item.wordStart + tpIdx;
                if (gIdx < words.length) words[gIdx].classList.add('tts-active');
                tpIdx++;
            }
        }, 50);

        ttsSource.onended = function() {
            clearInterval(ttsInterval);
            isSeqPlaying = false;
            playNextInSequence();
        };
        ttsSource.start(0);
    }

    function processChunk(i) {
        if (i >= chunks.length || !ttsPlaying) {
            if (i >= chunks.length && !isSeqPlaying && Object.keys(chunkQueue).length === 0) stopTTS();
            return;
        }
        var apiUrlEl = document.getElementById('ttsApiUrl');
        if (!apiUrlEl) { stopTTS(); return; }
        var apiUrl = apiUrlEl.value;
        fetch(apiUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ text: chunks[i], language: lang })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!ttsPlaying) return;
            if (data.fallback) { return processChunk(i + 1); }
            if (data.error) { if (i === 0) { stopTTS(); return; } return processChunk(i + 1); }
            var binary = atob(data.audioContent);
            var len = binary.length;
            var bytes = new Uint8Array(len);
            for (var j = 0; j < len; j++) bytes[j] = binary.charCodeAt(j);
            ttsAudioCtx.decodeAudioData(bytes.buffer, function(buf) {
                if (!ttsPlaying) return;
                var tps = data.timepoints || [];
                var relTimepoints = [];
                for (var ti = 0; ti < tps.length; ti++) relTimepoints.push(tps[ti].timeSeconds);
                chunkQueue[i] = { buffer: buf, timepoints: relTimepoints, wordStart: seqWordOffset, chunkOffset: decodedDuration };
                allDecodedChunks[i] = chunkQueue[i];
                seqWordOffset += tps.length;
                decodedDuration += buf.duration;
                playNextInSequence();
                processChunk(i + 1);
            }, function() { processChunk(i + 1); });
        })
        .catch(function(e) {
            console.warn('TTS chunk ' + i + ' failed:', e);
            if (i === 0) { stopTTS(); return; }
            processChunk(i + 1);
        });
    }

    window.seekTTS = function(e) {
        if (!ttsPlaying || !window.__ttsStarted || allDecodedChunks.length === 0) return;
        var bar = e.currentTarget;
        var rect = bar.getBoundingClientRect();
        var pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
        var targetTime = pct * totalEstDuration;
        var cumTime = 0;
        for (var ci = 0; ci < allDecodedChunks.length; ci++) {
            var ch = allDecodedChunks[ci];
            var chunkEnd = cumTime + ch.duration;
            if (targetTime <= chunkEnd || ci === allDecodedChunks.length - 1) {
                var timeInChunk = Math.max(0, targetTime - cumTime);
                if (ttsSource) { ttsSource.onended = null; try { ttsSource.stop(); } catch(e) {} ttsSource = null; }
                if (ttsInterval) { clearInterval(ttsInterval); ttsInterval = null; }
                isSeqPlaying = false;
                nextToPlay = ci + 1;
                for (var j = ci + 1; j < allDecodedChunks.length; j++) { chunkQueue[j] = allDecodedChunks[j]; }
                firstChunkStartTime = ttsAudioCtx.currentTime - targetTime / ttsPlaybackRate;
                var cst = ttsAudioCtx.currentTime - timeInChunk / ttsPlaybackRate;
                ttsSource = ttsAudioCtx.createBufferSource();
                ttsSource.buffer = ch.buffer;
                ttsSource.playbackRate.value = ttsPlaybackRate;
                ttsSource.connect(ttsAudioCtx.destination);
                var tpi = 0;
                while (tpi < ch.timepoints.length && timeInChunk >= ch.timepoints[tpi]) tpi++;
                ttsInterval = setInterval(function() {
                    if (!ttsPlaying) { clearInterval(ttsInterval); return; }
                    var elapsed = (ttsAudioCtx.currentTime - cst) * ttsPlaybackRate;
                    while (tpi < ch.timepoints.length && elapsed >= ch.timepoints[tpi]) {
                        words.forEach(function(w) { w.classList.remove('tts-active'); });
                        var gi = ch.wordStart + tpi;
                        if (gi < words.length) words[gi].classList.add('tts-active');
                        tpi++;
                    }
                }, 50);
                ttsSource.onended = function() { clearInterval(ttsInterval); isSeqPlaying = false; playNextInSequence(); };
                if (ttsAudioCtx.state === 'suspended') ttsAudioCtx.resume();
                ttsSource.start(0, timeInChunk);
                return;
            }
            cumTime += ch.duration;
        }
    };

    processChunk(0);
}

function updateTTSPlayPauseIcon(paused) {
    var icon = document.getElementById('ttsPlayPauseIcon');
    if (!icon) return;
    if (paused) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v14l11-7z" />';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h4v16H6V4zm8 0h4v16h-4V4z" />';
    }
}

function updateTTSIcon() {
    var icon = document.getElementById('ttsIcon');
    if (!icon) return;
    if (ttsPlaying && !ttsPaused) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
    } else if (ttsPlaying && ttsPaused) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M11 5L6 9H2v6h4l5 4V5z" />';
    }
}

/* Audio player utilities (for etipitaka which may have audio) */
function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '0:00';
    var m = Math.floor(seconds / 60);
    var s = Math.floor(seconds % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}
