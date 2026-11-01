@extends('layouts.app')

@section('title', 'Field Engineers Directory')
@section('page_title', 'Field Engineering Staff & Technicians')

@section('content')
<div class="space-y-4" x-data="{ 
    filterModalOpen: false,
    waModalOpen: false,
    selectedEngName: '',
    selectedEngPhone: '',
    openWaDialog(name, phone) {
        this.selectedEngName = name;
        this.selectedEngPhone = phone;
        this.waModalOpen = true;
    }
}">

    <!-- TOOLBAR (COMPACT, APP STYLE) -->
    <div class="bg-surface rounded-2xl p-3 border border-borderMain shadow-xs flex items-center justify-between gap-2">
        <form action="{{ route('engineers.index') }}" method="GET" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-textMuted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search engineer name, phone..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
            </div>
            <button type="submit" class="hidden sm:inline-flex px-3.5 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs">
                Search
            </button>
        </form>

        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Open Filter Modal Button -->
            <button type="button" @click="filterModalOpen = true" class="px-2.5 sm:px-3 py-2 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-xs font-bold text-textMain transition flex items-center gap-1 shadow-xs">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-secondary"></i>
                <span class="hidden sm:inline">Filters</span>
                @if(request()->has('status') && request('status') !== '')
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                @endif
            </button>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('engineers.create') }}" class="px-3 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition shrink-0">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>+ Add</span>
                </a>
            @endif
        </div>
    </div>

    <!-- FILTER POPUP MODAL -->
    <div x-show="filterModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <!-- Modal Dialog -->
        <div class="relative w-full max-w-md bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 sm:p-6 z-50 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3.5 border-b border-borderMain">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-textMain">Filter Field Engineers</h3>
                        <p class="text-[11px] text-textMuted">Filter active or deactivated engineering staff</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('engineers.index') }}" method="GET" class="mt-4 space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, phone number, designation..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Employment Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Staff Only</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Deactivated Staff</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-borderMain gap-3">
                    <a href="{{ route('engineers.index') }}" class="px-3.5 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
                        Reset
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="filterModalOpen = false" class="px-3.5 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMain hover:bg-bg transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold shadow-xs transition">
                            Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- WHATSAPP APP SELECTOR MODAL (Normal WhatsApp vs WhatsApp Business) -->
    <div x-show="waModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="waModalOpen = false"></div>

        <!-- Dialog -->
        <div class="relative w-full max-w-sm bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 z-50 transform transition-all text-center">
            <!-- Icon -->
            <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-[#25D366] flex items-center justify-center mb-3 shadow-2xs">
                <i data-lucide="message-circle" class="w-6 h-6 stroke-[2.4]"></i>
            </div>
            
            <h3 class="font-bold text-sm text-textMain">WhatsApp Message</h3>
            <p class="text-xs text-primary font-bold mt-0.5" x-text="selectedEngName"></p>
            <p class="text-[11px] text-textMuted mt-0.5" x-text="selectedEngPhone"></p>
            <p class="text-[11px] text-slate-500 mt-3 font-medium">Select app to message:</p>

            <!-- 2 Options: Regular WhatsApp vs WhatsApp Business -->
            <div class="grid grid-cols-1 gap-2.5 mt-3 text-left">
                <!-- Option 1: Normal WhatsApp -->
                <button type="button" 
                        @click="window.openWhatsAppDirect($event, selectedEngPhone, 'regular'); waModalOpen = false"
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
                        @click="window.openWhatsAppDirect($event, selectedEngPhone, 'business'); waModalOpen = false"
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

    <!-- COMPACT ENGINEERS CARDS (Short, clean cards with 3 options: Identity, Call, WhatsApp) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5">
        @forelse($engineers as $eng)
            <div class="bg-surface rounded-2xl border border-borderMain hover:border-primary/50 p-2.5 sm:p-3 shadow-2xs hover:shadow-xs transition group flex items-center justify-between gap-2.5">
                
                <!-- 1. KAUNSA ENGINEER HAI (Clicking navigates to full profile/history page) -->
                <a href="{{ route('engineers.show', $eng) }}" class="flex items-center space-x-2.5 min-w-0 flex-1 py-0.5">
                    <!-- Avatar with live status indicator dot -->
                    <div class="relative w-10 h-10 rounded-xl bg-primary text-white font-extrabold text-sm flex items-center justify-center shrink-0 overflow-hidden border border-slate-200/80 shadow-2xs">
                        @if($eng->photo)
                            <img src="{{ $eng->profile_photo_url }}" alt="{{ $eng->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($eng->name, 0, 1)) }}
                        @endif
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-surface {{ $eng->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    </div>

                    <!-- Engineer Name & Post -->
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-xs sm:text-sm text-textMain group-hover:text-primary transition truncate leading-tight">
                            {{ $eng->name }}
                        </h3>
                        <p class="text-[11px] text-textMuted truncate mt-0.5 font-medium">
                            {{ $eng->post ?? 'Field Service Staff' }}
                        </p>
                    </div>
                </a>

                <!-- QUICK ACTIONS: 2. CALL & 3. WHATSAPP -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- 2. Call Icon -->
                    <a href="tel:{{ $eng->phone_no }}" 
                       class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center transition shadow-2xs active:scale-95" 
                       title="Call {{ $eng->name }}">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                    </a>

                    <!-- 3. WhatsApp Icon (Prompts Normal vs Business WhatsApp) -->
                    <button type="button" 
                            @click="openWaDialog('{{ addslashes($eng->name) }}', '{{ $eng->phone_no }}')" 
                            class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-[#25D366] hover:text-white text-[#25D366] flex items-center justify-center transition shadow-2xs active:scale-95" 
                            title="WhatsApp Chat">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-surface rounded-2xl p-8 text-center text-textMuted border border-borderMain text-xs">
                No engineers found matching your search.
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $engineers->links() }}
    </div>

</div>
@endsection
