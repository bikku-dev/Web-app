@php
    $serverToasts = [];
    if (session('error') || (isset($errors) && $errors->any())) {
        $serverToasts[] = [
            'id' => 'toast_' . uniqid(),
            'type' => 'error',
            'title' => session('error') ? 'Notice' : 'Validation Error',
            'message' => session('error') ?? ($errors->first() ?? 'Please check the entered values.'),
            'details' => (isset($errors) && $errors->any()) ? array_values(array_unique($errors->all())) : [],
        ];
    } elseif (session('warning')) {
        $serverToasts[] = [
            'id' => 'toast_' . uniqid(),
            'type' => 'warning',
            'title' => 'Warning',
            'message' => (string) session('warning'),
            'details' => [],
        ];
    } elseif (session('success')) {
        $serverToasts[] = [
            'id' => 'toast_' . uniqid(),
            'type' => 'success',
            'title' => 'Success',
            'message' => (string) session('success'),
            'details' => [],
        ];
    } elseif (session('status')) {
        $serverToasts[] = [
            'id' => 'toast_' . uniqid(),
            'type' => 'info',
            'title' => 'Status',
            'message' => (string) session('status'),
            'details' => [],
        ];
    } elseif (session('info')) {
        $serverToasts[] = [
            'id' => 'toast_' . uniqid(),
            'type' => 'info',
            'title' => 'Info',
            'message' => (string) session('info'),
            'details' => [],
        ];
    }
    // Strictly cap at max 1 toast
    $serverToasts = array_slice($serverToasts, 0, 1);
@endphp

<!-- BOTTOM CENTER WHITE TOAST NOTIFICATION CONTAINER (STRICTLY MAX 1 AT ANY TIME) -->
<div id="toast-notification-system"
     x-data="toastSystem(@js($serverToasts))"
     class="fixed bottom-20 sm:bottom-24 lg:bottom-8 inset-x-0 z-[9999] flex flex-col items-center pointer-events-none px-4 space-y-2.5">
    
    <template x-for="item in items" :key="item.id">
        <div x-show="item.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-6 scale-95"
             class="pointer-events-auto max-w-lg w-full sm:w-auto min-w-[280px] sm:min-w-[340px] bg-white text-slate-800 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.22)] border border-slate-200/90 py-3 px-4 flex items-start gap-3 backdrop-blur-md">

            <!-- ICON (Pure inline SVG for zero-latency instant rendering) -->
            <div class="flex-shrink-0 mt-0.5">
                <!-- SUCCESS -->
                <div x-show="item.type === 'success'" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <!-- ERROR -->
                <div x-show="item.type === 'error'" class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>

                <!-- WARNING -->
                <div x-show="item.type === 'warning'" class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>

                <!-- INFO -->
                <div x-show="item.type === 'info'" class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
            </div>

            <!-- TEXT CONTENT -->
            <div class="flex-1 min-w-0 pr-1">
                <div class="text-[11px] font-black uppercase tracking-wider mb-0.5"
                     :class="{
                         'text-emerald-600': item.type === 'success',
                         'text-rose-600': item.type === 'error',
                         'text-amber-600': item.type === 'warning',
                         'text-sky-600': item.type === 'info'
                     }"
                     x-text="item.title || (item.type === 'success' ? 'Success' : (item.type === 'error' ? 'Notice' : 'Notification'))">
                </div>
                <div class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug break-words" x-text="item.message"></div>

                <!-- Optional Details list (e.g. multiple validation errors) -->
                <template x-if="item.details && item.details.length > 1">
                    <ul class="mt-1.5 space-y-0.5 text-xs text-rose-600 list-disc list-inside bg-rose-50/60 p-2 rounded-lg border border-rose-100">
                        <template x-for="(detail, i) in item.details" :key="i">
                            <li x-text="detail"></li>
                        </template>
                    </ul>
                </template>
            </div>

            <!-- CLOSE BUTTON -->
            <button type="button" 
                    @click="dismiss(item.id)" 
                    class="flex-shrink-0 text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1 rounded-lg transition"
                    title="Dismiss">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
    function toastSystem(initialItems = []) {
        return {
            items: [],
            timer: null,
            _initialized: false,

            init() {
                if (this._initialized) return;
                this._initialized = true;

                // Initialize only the first item if available (never more than 1)
                if (Array.isArray(initialItems) && initialItems.length > 0) {
                    const first = initialItems[0];
                    if (first && first.message) {
                        this.add(first.message, first.type, first.title, first.details);
                    }
                }

                // Expose global showToast helper
                window.showToast = (message, type = 'success', title = null, details = []) => {
                    this.add(message, type, title, details);
                };
            },

            add(message, type = 'success', title = null, details = []) {
                if (!message) return;

                // Clear previous auto-dismiss timer if any
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                const id = 'toast_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);
                const toast = {
                    id: id,
                    type: type || 'success',
                    title: title,
                    message: message,
                    details: Array.isArray(details) ? details : [],
                    visible: true
                };

                // Strictly ensure NEVER more than 1 toast: replace items with the single new toast
                this.items = [toast];

                // Auto dismiss after 4.5 seconds
                this.timer = setTimeout(() => {
                    this.dismiss(id);
                }, 4500);
            },

            dismiss(id) {
                const target = this.items.find(t => t.id === id);
                if (target) {
                    target.visible = false;
                    setTimeout(() => {
                        this.items = this.items.filter(t => t.id !== id);
                    }, 300);
                }
            }
        };
    }
</script>
