<div x-data="pushApp()" class="max-w-6xl mx-auto p-4 page-enter">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-6">
        <div class="p-4 sm:p-6 bg-[#795548] text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold Lao-font">ຈັດການແຈ້ງເຕືອນ (Push)</h1>
                    <p class="text-white/70 text-sm mt-1">ສ້າງ ແກ້ໄຂ ລຶບ ແລະ ສົ່ງຂໍ້ຄວາມແຈ້ງເຕືອນໄປຫາໂທລະສັບທີ່ຕິດຕັ້ງແອັບ</p>
                </div>
                <a href="<?= url('/') ?>" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white/70 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Status cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white/95 rounded-2xl shadow-md p-4">
            <div class="text-2xl font-bold text-[#795548]" x-text="notifications.length"></div>
            <div class="text-xs text-gray-500 Lao-font mt-1">ຈຳນວນຂໍ້ຄວາມ</div>
        </div>
        <div class="bg-white/95 rounded-2xl shadow-md p-4">
            <div class="text-2xl font-bold text-[#795548]" x-text="subscriberCount"></div>
            <div class="text-xs text-gray-500 Lao-font mt-1">ອຸປະກອນທີ່ສະໝັກ</div>
        </div>
        <div class="bg-white/95 rounded-2xl shadow-md p-4">
            <div class="text-2xl font-bold" :class="config.configured ? 'text-green-600' : 'text-red-500'" x-text="config.configured ? 'ພ້ອມ' : 'ບໍ່ພ້ອມ'"></div>
            <div class="text-xs text-gray-500 Lao-font mt-1">VAPID ກຽມພ້ອມ</div>
        </div>
        <div class="bg-white/95 rounded-2xl shadow-md p-4">
            <div class="text-2xl font-bold" :class="config.bucketConfigured ? 'text-green-600' : 'text-gray-400'" x-text="config.bucketConfigured ? 'ຕັ້ງແລ້ວ' : '-'"></div>
            <div class="text-xs text-gray-500 Lao-font mt-1">HF Bucket</div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-800 Lao-font">
                ລາຍການແຈ້ງເຕືອນ (<span x-text="notifications.length"></span>)
            </h2>
            <div class="flex items-center gap-2 flex-wrap">
                <button @click="toggleNotifyUI()" class="px-4 py-2 rounded-xl bg-green-600 text-white font-bold text-sm hover:bg-green-700 transition-colors Lao-font">
                    <span x-text="notifyEnabled ? 'ປິດການທົດສອບ' : 'ທົດສອບສຽງເຕືອນ'"></span>
                </button>
                <button @click="openModal()" class="px-4 py-2 rounded-xl bg-[#795548] text-white font-bold text-sm hover:bg-[#5E412D] transition-colors Lao-font whitespace-nowrap">
                    + ສ້າງຂໍ້ຄວາມໃໝ່
                </button>
            </div>
        </div>

        <div x-show="!notifications.length" class="p-8 text-center text-gray-400 Lao-font">
            ຍັງບໍ່ມີຂໍ້ຄວາມແຈ້ງເຕືອນ. ກົດ "+ ສ້າງຂໍ້ຄວາມໃໝ່" ເພື່ອເລີ່ມຕົ້ນ.
        </div>

        <div class="max-h-[560px] overflow-y-auto">
        <template x-for="(n, i) in notifications" :key="n.id">
            <div class="p-4 sm:p-6 border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                         :class="n.enabled === false ? 'bg-gray-100 text-gray-400' : 'bg-[#795548]/10 text-[#795548]'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-800 Lao-font text-sm sm:text-base truncate" x-text="n.title || '(no title)'"></h3>
                                <p class="text-xs text-gray-500 line-clamp-2 mt-0.5 Lao-font" x-text="n.body || ''"></p>
                                <p class="text-xs text-gray-300 mt-1 truncate">
                                    <span x-show="n.enabled === false" class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-500 mr-1">ປິດ</span>
                                    <a x-show="n.url" :href="n.url" target="_blank" class="text-blue-500 hover:underline" x-text="n.url"></a>
                                </p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button @click="sendNow(i)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-green-600 transition-colors" title="ສົ່ງດຽວນີ້">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </button>
                                <button @click="edit(i)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-blue-600 transition-colors" title="ແກ້ໄຂ">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="remove(i)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-red-600 transition-colors" title="ລຶບ">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        </div>
    </div>

    <!-- Create/Edit modal -->
    <div x-show="modal.show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
         style="display: none;"
         @keydown.escape.window="modal.show = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 my-auto"
             @click.away="modal.show = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 Lao-font" x-text="modal.editing ? 'ແກ້ໄຂຂໍ້ຄວາມ' : 'ສ້າງຂໍ້ຄວາມໃໝ່'"></h3>
                <button @click="modal.show = false" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="save()" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 Lao-font mb-1">ຫົວຂໍ້ (Title) <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#795548] focus:ring-2 focus:ring-[#795548]/20 outline-none text-sm Lao-font">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 Lao-font mb-1">ລາຍລະອຽດ (Body)</label>
                    <textarea x-model="form.body" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#795548] focus:ring-2 focus:ring-[#795548]/20 outline-none text-sm Lao-font resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 Lao-font mb-1">ລິ້ງເມື່ອກົດ (URL ທີ່ຈະເປີດ)</label>
                    <input type="text" x-model="form.url" placeholder="<?= url('/') ?>" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#795548] focus:ring-2 focus:ring-[#795548]/20 outline-none text-sm font-mono">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="enabled" x-model="form.enabled" class="h-4 w-4 rounded border-gray-300 text-[#795548] focus:ring-[#795548]">
                    <label for="enabled" class="text-sm font-bold text-gray-700 Lao-font">ເປີດໃຊ້ງານ (Enabled)</label>
                </div>

                <div x-show="form.error" class="text-red-500 text-sm Lao-font" x-text="form.error"></div>
                <div x-show="form.success" class="text-green-600 text-sm Lao-font" x-text="form.success"></div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="modal.show = false"
                            class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors Lao-font">ຍົກເລີກ</button>
                    <button type="submit" :disabled="form.saving"
                            class="flex-1 px-4 py-2 rounded-xl bg-[#795548] text-white font-bold hover:bg-[#5E412D] transition-colors disabled:opacity-50 Lao-font"
                            x-text="form.saving ? 'ກຳລັງບັນທຶກ...' : 'ບັນທຶກ'">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bucket sync bar -->
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden mt-6">
        <div class="p-4 sm:p-6 flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 Lao-font">ຈັດເກັບຂໍ້ມູນໃນ Hugging Face Bucket</h3>
                <p class="text-xs text-gray-400 mt-0.5 Lao-font" x-text="'Bucket: ' + (bucketName || 'ບໍ່ໄດ້ຕັ້ງຄ່າ')"></p>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="bucketSyncing" class="text-sm text-gray-500 Lao-font">ກຳລັງອັບ...</span>
                <span x-show="bucketResult" class="text-sm" :class="bucketOk ? 'text-green-600' : 'text-red-500'" x-text="bucketResult"></span>
                <button @click="syncBucket()" :disabled="!config.bucketConfigured || bucketSyncing"
                        class="px-4 py-2 rounded-xl bg-[#795548] text-white font-bold text-sm hover:bg-[#5E412D] transition-colors disabled:opacity-40 Lao-font">
                    ອັບໄປ Bucket
                </button>
                <button @click="pullBucket()" :disabled="!config.bucketConfigured || bucketSyncing"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-colors disabled:opacity-40 Lao-font">
                    ດຶງຈາກ Bucket
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function pushApp() {
    return {
        notifications: JSON.parse(document.getElementById('push-data')?.textContent || '[]'),
        subscriberCount: 0,
        config: JSON.parse(document.getElementById('push-config')?.textContent || '{}'),
        bucketName: '<?= addslashes(getenv('HF_BUCKET') ?: ($_ENV['HF_BUCKET'] ?? '')) ?>',
        bucketSyncing: false,
        bucketResult: '',
        bucketOk: false,
        notifyEnabled: false,
        modal: {
            show: false,
            editing: false,
        },
        form: {
            id: '',
            title: '',
            body: '',
            url: '',
            enabled: true,
            saving: false,
            error: '',
            success: '',
        },

        init() {
            this.$watch('modal.show', val => {
                document.body.style.overflow = val ? 'hidden' : '';
            });
            this.refreshStatus();
        },

        async refreshStatus() {
            try {
                const res = await fetch('<?= url('/api/notify/list') ?>');
                const data = await res.json();
                if (data.success) {
                    this.notifications = data.notifications || [];
                    this.subscriberCount = data.subscriberCount || 0;
                    if (data.config) this.config = data.config;
                }
            } catch (e) {}
        },

        openModal() {
            this.form = { id: '', title: '', body: '', url: '', enabled: true, saving: false, error: '', success: '' };
            this.modal.editing = false;
            this.modal.show = true;
        },

        edit(i) {
            const n = this.notifications[i];
            this.form = {
                id: n.id, title: n.title || '', body: n.body || '', url: n.url || '',
                enabled: n.enabled !== false, saving: false, error: '', success: ''
            };
            this.modal.editing = true;
            this.modal.show = true;
        },

        async save() {
            if (!this.form.title.trim()) {
                this.form.error = 'ກະລຸນາປ້ອນຫົວຂໍ້';
                return;
            }
            this.form.error = '';
            this.form.success = '';
            this.form.saving = true;
            try {
                const res = await fetch('<?= url('/api/notify/store') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: this.form.id, title: this.form.title,
                        body: this.form.body, url: this.form.url, enabled: this.form.enabled
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.form.success = 'ບັນທຶກສຳເລັດ';
                    await this.refreshStatus();
                    setTimeout(() => { this.modal.show = false; }, 600);
                } else {
                    this.form.error = data.error || 'ບັນທຶກລົ້ມເຫຼວ';
                }
            } catch (e) {
                this.form.error = 'ເກີດຂໍ້ຜິດພາດ: ' + e.message;
            }
            this.form.saving = false;
        },

        remove(i) {
            const n = this.notifications[i];
            if (!confirm('ຕ້ອງການລຶບ "' + (n.title || '') + '" ບໍ່?')) return;
            fetch('<?= url('/api/notify/destroy') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: n.id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.notifications = this.notifications.filter(x => x.id !== n.id);
                } else {
                    alert(data.error || 'ລຶບລົ້ມເຫຼວ');
                }
            })
            .catch(e => alert('ເກີດຂໍ້ຜິດພາດ: ' + e.message));
        },

        sendNow(i) {
            const n = this.notifications[i];
            if (!confirm('ສົ່ງແຈ້ງເຕືອນ "' + (n.title || '') + '" ໄປຫາທຸກອຸປະກອນທີ່ສະໝັກ?')) return;
            fetch('<?= url('/api/notify/send') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: n.id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('ສຳເລັດ', 'ສົ່ງສຳເລັດ ' + (data.sent || 0) + ' / ' + (data.total || 0) + ' ອຸປະກອນ', 'success');
                    this.refreshStatus();
                } else {
                    alert(data.error || 'ສົ່ງລົ້ມເຫຼວ');
                }
            })
            .catch(e => alert('ເກີດຂໍ້ຜິດພາດ: ' + e.message));
        },

        async getVapidKey() {
            if (this.config.vapidPublicKey) return this.config.vapidPublicKey;
            try {
                const res = await fetch('<?= url('/api/notify/pubkey') ?>', { cache: 'no-store' });
                const data = await res.json();
                if (data.publicKey) {
                    this.config.vapidPublicKey = data.publicKey;
                    return data.publicKey;
                }
            } catch (e) {}
            return '';
        },
        async toggleNotifyUI() {
            if (!('Notification' in window)) {
                alert('ບຣາວເຊີນີ້ບໍ່ຮອງຮັບ Notification');
                return;
            }
            if (this.notifyEnabled) {
                await this.unsubscribeLocal();
                this.notifyEnabled = false;
                return;
            }
            if (Notification.permission === 'denied') {
                Swal.fire('ກະລຸນາອະນຸຍາດແຈ້ງເຕືອນ',
                    'ກະລຸນາເປີດການຕັ້ງຄ່າບຣາວເຊີ → Site settings → Notifications → Allow ສຳລັບເວັບນີ້, ແລ້ວ Reload ໜ້າເວັບ.',
                    'warning');
                return;
            }
            try {
                const key = await this.getVapidKey();
                if (!key) {
                    Swal.fire('ຜິດພາດ', 'ບໍ່ສາມາດດຶງຄີ VAPID ຈາກເຊີເວີໄດ້', 'error');
                    return;
                }
                if (Notification.permission === 'granted') {
                    const reg = await navigator.serviceWorker.ready;
                    const sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(key)
                    });
                    await this.saveSubscription(sub);
                    this.notifyEnabled = true;
                    new Notification('ທົດສອບສຳເລັດ', { body: 'ການແຈ້ງເຕືອນພ້ອມໃຊ້ງານ' });
                    return;
                }
                const perm = await Notification.requestPermission();
                if (perm !== 'granted') {
                    Swal.fire('ບໍ່ໄດ້ອະນຸຍາດ', 'ກະລຸນາອະນຸຍາດການແຈ້ງເຕືອນ', 'warning');
                    return;
                }
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(key)
                });
                await this.saveSubscription(sub);
                this.notifyEnabled = true;
                new Notification('ທົດສອບສຳເລັດ', { body: 'ການແຈ້ງເຕືອນພ້ອມໃຊ້ງານ' });
            } catch (e) {
                Swal.fire('ຜິດພາດ', e.message, 'error');
            }
        },

        urlBase64ToUint8Array(base64String) {
            if (!base64String || typeof base64String !== 'string') {
                throw new Error('ບໍ່ພົບຄີ VAPID ສາທາລະນະ (VAPID_PUBLIC_KEY) ກະລຸນາຕັ້ງຄ່າໃຫ້ຖືກຕ້ອງ');
            }
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
            return outputArray;
        },

        async saveSubscription(sub) {
            await fetch('<?= url('/api/notify/subscribe') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(sub)
            });
        },

        async unsubscribeLocal() {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                await fetch('<?= url('/api/notify/unsubscribe') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ endpoint: sub.endpoint })
                });
                await sub.unsubscribe();
            }
        },

        syncBucket() {
            this.bucketSyncing = true;
            this.bucketResult = '';
            fetch('<?= url('/api/notify/sync-bucket') ?>', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    this.bucketOk = data.success;
                    this.bucketResult = data.success ? 'ອັບສຳເລັດ' : (data.reason === 'bucket_not_configured' ? 'ບໍ່ມີ HF_TOKEN' : 'ອັບລົ້ມເຫຼວ');
                })
                .catch(() => { this.bucketOk = false; this.bucketResult = 'ຜິດພາດ'; })
                .finally(() => { this.bucketSyncing = false; });
        },

        pullBucket() {
            this.bucketSyncing = true;
            this.bucketResult = '';
            fetch('<?= url('/api/notify/pull-bucket') ?>', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    this.bucketOk = data.success;
                    this.bucketResult = data.success ? 'ດຶງສຳເລັດ' : 'ດຶງລົ້ມເຫຼວ';
                    if (data.success) this.refreshStatus();
                })
                .catch(() => { this.bucketOk = false; this.bucketResult = 'ຜິດພາດ'; })
                .finally(() => { this.bucketSyncing = false; });
        }
    };
}
</script>

<script id="push-config" type="application/json"><?= json_encode($config, JSON_UNESCAPED_UNICODE) ?></script>
<script id="push-data" type="application/json"><?= json_encode($notifications, JSON_UNESCAPED_UNICODE) ?></script>

<style>
.page-enter { animation: fadeUp .35s ease-out both; }
@keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>
