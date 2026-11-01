@extends('layouts.app')

@section('title', 'Manage | Bhumi Technocare')
@section('page_title', 'Manage Hub')

@section('content')
<div class="max-w-3xl mx-auto px-1 sm:px-3 pt-1 pb-6">

    <!-- Top Header with Back Button (Visible on Mobile & Desktop) -->
    <div class="flex items-center justify-between pb-3.5 pt-1 px-1">
        <div class="flex items-center space-x-3">
            <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('dashboard') }}'" class="p-2 rounded-xl bg-white border border-borderMain text-textMain hover:bg-slate-50 transition active:scale-95 shadow-2xs" title="Go Back">
                <i data-lucide="arrow-left" class="w-4 h-4 text-textMuted"></i>
            </button>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Manage Hub / अधिक विकल्प
                </h1>
                <p class="text-[11px] sm:text-xs text-textMuted">
                    {{ auth()->user()->isAdmin() ? 'Administrator Control Center' : 'Field Service Operations' }}
                </p>
            </div>
        </div>

        <!-- Profile Avatar with Dropdown Options -->
        <div class="relative" x-data="{ profileOpen: false }">
            <button type="button" 
                    @click="profileOpen = !profileOpen" 
                    class="w-11 h-11 rounded-full p-0.5 ring-2 ring-[#42919e]/40 hover:ring-[#42919e] bg-white transition transform active:scale-95 flex items-center justify-center shadow-xs"
                    title="Account Profile & Settings">
                @if(auth()->user()->photo)
                    <img src="{{ auth()->user()->profile_photo_url }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-full h-full rounded-full object-cover" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=42919e&color=FFFFFF&bold=true&size=128';">
                @else
                    <div class="w-full h-full rounded-full bg-gradient-to-tr from-[#1E3A5F] to-[#42919e] text-white flex items-center justify-center font-black text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </button>

            <!-- Profile Popup Menu -->
            <div x-show="profileOpen" 
                 @click.away="profileOpen = false" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-surface rounded-2xl shadow-2xl border border-borderMain py-2 z-50">
                <div class="px-4 py-2.5 border-b border-borderMain bg-bg/50">
                    <p class="text-xs font-bold text-textMain leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-textMuted capitalize mt-0.5">{{ auth()->user()->role }} • {{ auth()->user()->phone_no }}</p>
                </div>
                <a href="{{ route('password.change') }}" class="flex items-center space-x-2.5 px-4 py-2.5 text-xs text-textMain hover:bg-bg font-medium transition">
                    <i data-lucide="key" class="w-4 h-4 text-textMuted"></i>
                    <span>Change Password</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 font-bold text-left transition">
                        <i data-lucide="power" class="w-4 h-4 text-rose-600"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN MANAGE CARD (Exact Mobile App Card Layout) -->
    <div class="bg-surface rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-sm p-4 sm:p-6 mb-5">
        <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight">Manage</h2>
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-[#42919e] bg-[#42919e]/10 px-2 py-0.5 rounded-full">
                {{ auth()->user()->isAdmin() ? 'All Modules' : 'Field Operations' }}
            </span>
        </div>

        <!-- 2-Column Grid matching mobile app -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 pt-1">

            <!-- 1. Service Tickets / Add Service -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('complaints.create') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="wrench" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Add Service
                    </span>
                </a>
            @else
                <a href="{{ route('complaints.index') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="wrench" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        My Tasks / Jobs
                    </span>
                </a>
            @endif

            <!-- 2. Add Engineer (Admin Only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('engineers.create') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="hard-hat" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Add Engineer
                    </span>
                </a>
            @endif

            <!-- 3. Expenses -->
            <a href="{{ route('expenses.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="banknote" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    Expenses
                </span>
            </a>

            <!-- 4. Bills -->
            <a href="{{ route('billing.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="receipt-text" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    {{ auth()->user()->isEngineer() ? 'My Bills' : 'Bills' }}
                </span>
            </a>

            <!-- 5. Attendance -->
            <a href="{{ route('attendance.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="user-check" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    Attendance
                </span>
            </a>

            <!-- 6. Attendance Report (Admin Only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('attendance.leaderboard') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="file-chart-column" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Attendance Report
                    </span>
                </a>
            @endif

            <!-- 7. Show Service Type -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('masters.services') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="settings-2" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Show Service Type
                    </span>
                </a>
            @endif

            <!-- 8. Show Product Type -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('masters.products') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="monitor" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Show Product Type
                    </span>
                </a>
            @endif

            <!-- 9. Show Organisation -->
            <a href="{{ route('organizations.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="building-2" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    Show Organisation
                </span>
            </a>

            <!-- 10. Show Customer -->
            <a href="{{ route('customers.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    Show Customer
                </span>
            </a>

            <!-- 11. Notice Board -->
            <a href="{{ route('notices.index') }}" 
               class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                    <i data-lucide="bell" class="w-10 h-10 stroke-[1.6]"></i>
                </div>
                <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                    Notice Board
                </span>
            </a>

            <!-- Admin Extra 1: Areas & Cities -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('masters.areas') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="map-pin" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Areas & Cities
                    </span>
                </a>

                <!-- Admin Extra 2: Security Logs -->
                <a href="{{ route('audit.index') }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white hover:bg-teal-50/30 hover:border-[#42919e]/60 active:scale-95 transition shadow-xs group">
                    <div class="w-12 h-12 flex items-center justify-center text-[#42919e] group-hover:scale-110 transition-transform">
                        <i data-lucide="shield-check" class="w-10 h-10 stroke-[1.6]"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-800 mt-2 text-center leading-snug">
                        Security Logs
                    </span>
                </a>
            @endif

        </div>
    </div>

    <!-- Quick Account & Security Actions -->
    <div class="bg-surface rounded-2xl border border-slate-200/90 shadow-xs p-3.5 sm:p-4">
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('password.change') }}" 
               class="flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs sm:text-sm transition">
                <i data-lucide="key" class="w-4 h-4 text-slate-600"></i>
                <span>Change Password</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm transition shadow-xs">
                    <i data-lucide="power" class="w-4 h-4"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
