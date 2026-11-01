@extends('layouts.app')

@section('title', 'System Audit & Security Logs')

@section('content')
<div class="space-y-6" x-data="{ filterModalOpen: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-primary-text">Security & Audit Logs</h1>
            <p class="text-xs text-secondary-text">Immutable audit trail of system events, financial handovers, approvals, and security actions</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-app-border text-xs text-secondary-text">
                <i data-lucide="shield-check" class="w-4 h-4 text-app-success"></i> Tamper-evident logging active
            </span>
        </div>
    </div>

    <!-- Compact Toolbar & Filter Modal Trigger -->
    @php
        $activeFilterCount = (request('module') ? 1 : 0) + (request('search') ? 1 : 0);
    @endphp
    <div class="bg-surface rounded-2xl border border-app-border p-3 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="{{ route('audit.index') }}" method="GET" class="flex-1 flex items-center gap-2">
            @if(request('module'))
                <input type="hidden" name="module" value="{{ request('module') }}">
            @endif
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-secondary-text"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, details, IP address..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-app-border bg-app-bg outline-none focus:ring-secondary focus:border-secondary">
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-primary-text text-xs font-semibold shrink-0">
                Search
            </button>
        </form>

        <div class="flex items-center gap-2 shrink-0">
            @if(request()->hasAny(['search', 'module']))
                <a href="{{ route('audit.index') }}" class="px-3 py-2 rounded-xl border border-app-border text-xs font-bold text-secondary-text hover:text-primary-text hover:bg-app-bg transition">
                    Reset
                </a>
            @endif

            <button type="button" @click="filterModalOpen = true" class="relative flex-1 sm:flex-initial px-4 py-2 rounded-xl border border-app-border bg-surface hover:bg-app-bg text-primary-text text-xs font-bold flex items-center justify-center gap-2 transition shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-primary"></i>
                <span>Filters</span>
                @if($activeFilterCount > 0)
                    <span class="w-5 h-5 rounded-full bg-accent text-primary text-[10px] font-black flex items-center justify-center">{{ $activeFilterCount }}</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Filter Popup Modal -->
    <div x-show="filterModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-surface rounded-3xl max-w-md w-full p-6 border border-app-border shadow-2xl relative"
             @click.away="filterModalOpen = false"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between pb-4 border-b border-app-border">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-primary-text">Filter Security Logs</h3>
                        <p class="text-[11px] text-secondary-text">Filter audit trail by action or module</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-secondary-text hover:text-primary-text p-1 rounded-lg hover:bg-app-bg transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('audit.index') }}" method="GET" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-primary-text mb-1">Search Keywords / IP</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-secondary-text"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, details, IP..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-app-border bg-app-bg outline-none focus:ring-secondary focus:border-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-primary-text mb-1">Functional Module</label>
                    <select name="module" class="w-full px-3 py-2 text-xs rounded-xl border border-app-border bg-app-bg outline-none focus:ring-secondary focus:border-secondary">
                        <option value="">All Functional Modules</option>
                        @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 border-t border-app-border flex items-center justify-between gap-3">
                    @if(request()->hasAny(['search', 'module']))
                        <a href="{{ route('audit.index') }}" class="px-4 py-2 rounded-xl border border-app-border text-xs font-bold text-secondary-text hover:text-primary-text hover:bg-app-bg transition text-center">
                            Clear Filters
                        </a>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="filterModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-secondary-text hover:text-primary-text">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary/95 text-white text-xs font-bold shadow-md transition">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($logs as $log)
            <div class="bg-surface rounded-2xl border border-app-border p-4 shadow-sm hover:border-secondary transition flex flex-col justify-between">
                <div>
                    <!-- Header: Action & Module -->
                    <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-app-border">
                        @php
                            $badgeClass = 'bg-primary/10 text-primary border-primary/20';
                            if (str_contains($log->action, 'VERIFY') || str_contains($log->action, 'APPROVE')) {
                                $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            } elseif (str_contains($log->action, 'REJECT') || str_contains($log->action, 'CANCEL') || str_contains($log->action, 'DELETE')) {
                                $badgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
                            } elseif (str_contains($log->action, 'COLLECT') || str_contains($log->action, 'BILL')) {
                                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
                            }
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                            {{ $log->action }}
                        </span>

                        <span class="px-2 py-0.5 rounded-md bg-app-bg text-secondary-text font-semibold text-[10px] uppercase">
                            {{ $log->module ?? 'General' }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="py-2.5 text-xs text-primary-text leading-relaxed font-mono text-[11px] bg-bg/50 p-2.5 rounded-xl border border-borderMain mt-2">
                        {{ $log->details }}
                    </div>
                </div>

                <!-- Footer: User, IP, Timestamp -->
                <div class="pt-3 border-t border-app-border flex items-center justify-between text-xs text-secondary-text gap-2">
                    <div class="min-w-0">
                        @if($log->user)
                            <span class="font-bold text-primary-text truncate block">{{ $log->user->name }}</span>
                            <span class="text-[10px] capitalize">{{ $log->user->role }}</span>
                        @else
                            <span class="italic text-[11px]">System / Cron</span>
                        @endif
                    </div>

                    <div class="text-right shrink-0">
                        <div class="font-semibold text-primary-text text-[11px]">{{ $log->created_at->format('d M Y, h:i A') }}</div>
                        <div class="text-[10px] text-secondary-text font-mono">IP: {{ $log->ip_address ?? '127.0.0.1' }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center text-secondary-text bg-surface rounded-2xl border border-app-border">
                <i data-lucide="shield-alert" class="w-10 h-10 mx-auto text-secondary-text/40 mb-2"></i>
                <p class="font-medium text-xs">No audit log records found.</p>
            </div>
        @endforelse
    </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-app-border font-sans">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
