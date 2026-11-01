@extends('layouts.app')

@section('title', $engineer->name . ' - Engineer Profile')
@section('page_title', 'Staff Profile')

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-3" x-data="{ 
    activeTab: 'details',
    resetModalOpen: false,
    waModalOpen: false
}">

    <!-- 1. TOP COMPACT HEADER ROW (BACK BUTTON & STATUS) -->
    <div class="flex items-center justify-between gap-2 px-1">
        <a href="{{ route('engineers.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-primary hover:text-primary-dark transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>All Engineers</span>
        </a>

        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $engineer->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                {{ $engineer->is_active ? 'Active Staff' : 'Deactivated' }}
            </span>
            <span class="text-[10px] text-textMuted font-medium hidden sm:inline">
                ID: #{{ $engineer->id }}
            </span>
        </div>
    </div>

    <!-- 2. ULTRA-PREMIUM COMPACT HERO CARD (NATIVE APP IDENTITY & FAST ACTIONS) -->
    <div class="bg-surface rounded-2xl border border-borderMain p-3 sm:p-4 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            
            <!-- Engineer Identity -->
            <div class="flex items-center space-x-3 min-w-0">
                <div class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-primary to-primary-light text-white font-black text-lg sm:text-xl flex items-center justify-center overflow-hidden border-2 border-white shadow-sm shrink-0">
                    @if($engineer->photo)
                        <img src="{{ $engineer->profile_photo_url }}" alt="{{ $engineer->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($engineer->name, 0, 1)) }}
                    @endif
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-surface {{ $engineer->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                </div>

                <div class="min-w-0">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 truncate leading-tight">
                        {{ $engineer->name }}
                    </h2>
                    <p class="text-xs text-primary font-semibold truncate mt-0.5">
                        {{ $engineer->post ?? 'Field Service Staff' }}
                        @if($engineer->department)
                            <span class="text-slate-400 font-normal">• {{ $engineer->department }}</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-textMuted truncate mt-0.5 flex items-center gap-1.5 font-medium">
                        <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                        <a href="tel:{{ $engineer->phone_no }}" class="hover:underline font-bold text-slate-700">{{ $engineer->phone_no }}</a>
                        <span class="text-slate-300">|</span>
                        <span>Joined {{ $engineer->created_at->format('M Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Communication & Management Actions (Touch friendly pills) -->
            <div class="flex items-center flex-wrap gap-1.5 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 shrink-0">
                <!-- 1. Direct Call -->
                <a href="tel:{{ $engineer->phone_no }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 font-bold text-xs transition shadow-2xs active:scale-95" 
                   title="Call Phone">
                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                    <span>Call</span>
                </a>

                <!-- 2. WhatsApp Selector -->
                <button type="button" 
                        @click="waModalOpen = true" 
                        class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-[#25D366] hover:text-white text-[#25D366] font-bold text-xs transition shadow-2xs active:scale-95" 
                        title="Chat on WhatsApp">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                    <span>WhatsApp</span>
                </button>

                @if(auth()->user()->isAdmin())
                    <!-- 3. Edit Profile -->
                    <a href="{{ route('engineers.edit', $engineer) }}" 
                       class="inline-flex items-center justify-center space-x-1 px-2.5 py-2 rounded-xl border border-borderMain hover:bg-slate-100 text-slate-700 font-bold text-xs transition shadow-2xs active:scale-95" 
                       title="Edit Staff Info">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Edit</span>
                    </a>

                    <!-- 4. Reset Password Key -->
                    <button type="button" 
                            @click="resetModalOpen = true" 
                            class="inline-flex items-center justify-center space-x-1 px-2.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-800 font-bold text-xs transition shadow-2xs active:scale-95 border border-amber-200" 
                            title="Reset Staff Password">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Password</span>
                    </button>

                    <!-- 5. Toggle Status -->
                    <form action="{{ route('engineers.toggle-status', $engineer) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Change status for {{ $engineer->name }}?')" 
                                class="inline-flex items-center justify-center p-2 rounded-xl border border-borderMain hover:bg-slate-100 {{ $engineer->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition shadow-2xs active:scale-95" 
                                title="{{ $engineer->is_active ? 'Deactivate Account' : 'Activate Account' }}">
                            <i data-lucide="{{ $engineer->is_active ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    <!-- 3. MOBILE SEGMENTED APP TABS (PHONE ZERO-SCROLL GUARANTEE) -->
    <div class="lg:hidden">
        <div class="grid grid-cols-3 p-1 bg-slate-200/80 rounded-xl gap-1 text-center text-xs">
            <button type="button" 
                    @click="activeTab = 'details'"
                    :class="activeTab === 'details' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg transition truncate">
                Profile & Bio
            </button>

            <button type="button" 
                    @click="activeTab = 'hr'"
                    :class="activeTab === 'hr' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg transition truncate">
                HR & Stats
            </button>

            <button type="button" 
                    @click="activeTab = 'tickets'"
                    :class="activeTab === 'tickets' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg transition truncate">
                Calls ({{ $complaints->count() }})
            </button>
        </div>
    </div>

    <!-- 4. CONTENT SECTIONS -->
    <!-- Mobile: Shows active tab (Zero scroll!) -->
    <!-- Desktop: Shows all 3 side-by-side in balanced 3-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        <!-- ================= CARD 1: PERSONAL & CONTACT ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-3"
             x-show="activeTab === 'details' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-primary"></i> Personal Information
                    </span>
                    <span class="text-[10px] font-bold text-textMuted bg-slate-100 px-2 py-0.5 rounded-full">
                        Bio & Contact
                    </span>
                </div>

                <div class="space-y-2.5 pt-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-textMuted flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> Date of Birth
                        </span>
                        <strong class="text-slate-800 font-bold">{{ $engineer->dob ? \Carbon\Carbon::parse($engineer->dob)->format('d M Y') : 'Not Set' }}</strong>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-textMuted flex items-center gap-1.5">
                            <i data-lucide="droplet" class="w-3.5 h-3.5 text-rose-500"></i> Blood Group
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-black text-xs border border-rose-200">
                            {{ $engineer->blood_group ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-textMuted flex items-center gap-1.5">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i> Email
                        </span>
                        <span class="text-slate-800 font-semibold truncate max-w-[160px]">{{ $engineer->email ?? 'N/A' }}</span>
                    </div>

                    <div class="py-1">
                        <span class="text-textMuted flex items-center gap-1.5 mb-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> Residential Address
                        </span>
                        <p class="text-slate-800 font-medium text-xs bg-slate-50 p-2 rounded-xl border border-slate-100 leading-relaxed">
                            {{ $engineer->address ?: 'No address specified.' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($engineer->remarks)
                <div class="pt-2 border-t border-slate-100 text-[11px] text-textMuted">
                    <span class="font-bold text-slate-700">Remarks:</span> {{ $engineer->remarks }}
                </div>
            @endif
        </div>

        <!-- ================= CARD 2: HR & PERFORMANCE KPIS ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-3"
             x-show="activeTab === 'hr' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="award" class="w-3.5 h-3.5 text-accent"></i> HR & Performance
                    </span>
                    <span class="text-[10px] font-bold text-textMuted bg-slate-100 px-2 py-0.5 rounded-full">
                        Stats
                    </span>
                </div>

                <!-- 2 Big Metric Tiles -->
                <div class="grid grid-cols-2 gap-2 pt-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div class="text-2xl font-black text-primary leading-none">{{ $engineer->assignedComplaints()->count() }}</div>
                        <div class="text-[10px] text-textMuted font-bold uppercase tracking-wider mt-1">Total Assigned</div>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                        <div class="text-2xl font-black text-emerald-700 leading-none">{{ $engineer->assignedComplaints()->where('status', 'completed')->count() }}</div>
                        <div class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider mt-1">Resolved Calls</div>
                    </div>
                </div>

                <!-- HR Details -->
                <div class="space-y-2 pt-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-textMuted flex items-center gap-1.5">
                            <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-amber-500"></i> Family Contact
                        </span>
                        @if($engineer->family_contact_no)
                            <a href="tel:{{ $engineer->family_contact_no }}" class="text-primary font-bold hover:underline">
                                {{ $engineer->family_contact_no }}
                            </a>
                        @else
                            <span class="text-textMuted">Not Provided</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-textMuted flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i> App Activity
                        </span>
                        <span class="text-slate-800 font-semibold">{{ $engineer->last_login_at ? $engineer->last_login_at->diffForHumans() : 'Never logged in' }}</span>
                    </div>

                    @if($engineer->resume)
                        <div class="pt-2">
                            <a href="{{ asset('storage/' . $engineer->resume) }}" target="_blank" class="w-full inline-flex items-center justify-center space-x-1.5 py-2 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-secondary font-bold text-xs transition border border-blue-200">
                                <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                                <span>Download Resume / CV</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 text-center">
                <a href="{{ route('attendance.leaderboard') }}" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                    <span>View on Hours Leaderboard</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

        <!-- ================= CARD 3: SERVICE CALLS HISTORY ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-3.5 sm:p-4 shadow-xs flex flex-col justify-between"
             x-show="activeTab === 'tickets' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="wrench" class="w-3.5 h-3.5 text-secondary"></i> Assigned Service Calls
                    </span>
                    <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">
                        {{ $complaints->count() }} Recent
                    </span>
                </div>

                <!-- Internal scrollable list with max height so entire page NEVER scrolls -->
                <div class="space-y-2 pt-2.5 max-h-[290px] overflow-y-auto pr-1 divide-y divide-slate-100">
                    @forelse($complaints as $cmp)
                        <div class="pt-2 first:pt-0">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('complaints.show', $cmp) }}" class="font-extrabold text-xs text-primary hover:underline">
                                    {{ $cmp->ticket_number }}
                                </a>
                                <span class="px-2 py-0.2 text-[9px] font-bold rounded-full uppercase {{ $cmp->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($cmp->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $cmp->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-600 mt-0.5">
                                <span class="font-bold text-slate-800 truncate pr-2">{{ $cmp->productType?->name ?? 'Machinery Service' }}</span>
                                <span class="text-[10px] text-textMuted shrink-0">{{ $cmp->due_date?->format('d M') ?? '—' }}</span>
                            </div>
                            <div class="text-[10px] text-textMuted truncate">
                                {{ $cmp->customer?->name }} ({{ $cmp->organization?->name ?? 'Direct' }})
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-textMuted text-xs bg-slate-50 rounded-xl border border-slate-100">
                            <i data-lucide="inbox" class="w-6 h-6 mx-auto text-slate-400 mb-1"></i>
                            No assigned service calls yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 text-center text-xs">
                <a href="{{ route('complaints.index') }}" class="font-bold text-secondary hover:underline">
                    View All Services Tickets &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- 5. WHATSAPP APP SELECTOR MODAL (Normal WhatsApp vs WhatsApp Business) -->
    <div x-show="waModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="waModalOpen = false"></div>

        <!-- Dialog -->
        <div class="relative w-full max-w-sm bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 z-50 transform transition-all text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-[#25D366] flex items-center justify-center mb-3 shadow-2xs">
                <i data-lucide="message-circle" class="w-6 h-6 stroke-[2.4]"></i>
            </div>
            
            <h3 class="font-bold text-sm text-textMain">WhatsApp Message</h3>
            <p class="text-xs text-primary font-bold mt-0.5">{{ $engineer->name }}</p>
            <p class="text-[11px] text-textMuted mt-0.5">{{ $engineer->phone_no }}</p>
            <p class="text-[11px] text-slate-500 mt-3 font-medium">Select app to message:</p>

            <div class="grid grid-cols-1 gap-2.5 mt-3 text-left">
                <!-- Option 1: Normal WhatsApp -->
                <button type="button" 
                        @click="window.openWhatsAppDirect($event, '{{ $engineer->phone_no }}', 'regular'); waModalOpen = false"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition border border-emerald-200 active:scale-98 shadow-2xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#25D366] text-white flex items-center justify-center shadow-2xs shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4 stroke-[2.4]"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-slate-900 leading-tight">WhatsApp</div>
                            <div class="text-[10px] text-slate-500 font-normal">Normal / Personal App</div>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-emerald-600"></i>
                </button>

                <!-- Option 2: WhatsApp Business -->
                <button type="button" 
                        @click="window.openWhatsAppDirect($event, '{{ $engineer->phone_no }}', 'business'); waModalOpen = false"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold text-xs transition border border-teal-200 active:scale-98 shadow-2xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#128C7E] text-white flex items-center justify-center shadow-2xs font-black text-sm shrink-0">
                            B
                        </div>
                        <div>
                            <div class="font-bold text-xs text-slate-900 leading-tight">WhatsApp Business</div>
                            <div class="text-[10px] text-slate-500 font-normal">Business App (W4B)</div>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-teal-600"></i>
                </button>
            </div>

            <button type="button" @click="waModalOpen = false" class="w-full mt-3 py-2 rounded-xl text-xs font-semibold text-textMuted hover:text-textMain hover:bg-slate-100 transition">
                Cancel
            </button>
        </div>
    </div>

    <!-- 6. RESET PASSWORD POPUP MODAL (CLEAN POPUP, DOES NOT PUSH CONTENT DOWN) -->
    <div x-show="resetModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="resetModalOpen = false"></div>

        <!-- Dialog -->
        <div class="relative w-full max-w-sm bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 z-50">
            <div class="flex items-center justify-between pb-3 border-b border-borderMain">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="key" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Set New Password</h3>
                        <p class="text-[10px] text-textMuted">{{ $engineer->name }}</p>
                    </div>
                </div>
                <button type="button" @click="resetModalOpen = false" class="text-textMuted hover:text-slate-800 p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('engineers.reset-password', $engineer) }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">New Mobile Login Password</label>
                    <input type="password" 
                           name="new_password" 
                           required 
                           minlength="6" 
                           placeholder="Enter at least 6 characters" 
                           class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-borderMain">
                    <button type="button" @click="resetModalOpen = false" class="px-3.5 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold shadow-xs transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
