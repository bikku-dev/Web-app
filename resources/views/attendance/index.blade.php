@extends('layouts.app')

@section('title', 'Attendance & Shifts')
@section('page_title', 'Daily Photo Attendance & Shift Records')

@section('content')
<div class="space-y-6">

    <!-- CURRENT SHIFT BANNER -->
    <div class="bg-surface rounded-3xl p-6 border border-borderMain shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl {{ $isClockedIn ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                <i data-lucide="{{ $isClockedIn ? 'user-check' : 'user-x' }}" class="w-7 h-7"></i>
            </div>
            <div>
                <span class="text-xs uppercase font-bold text-textMuted tracking-wider">My Shift Status Today</span>
                <h3 class="text-xl font-extrabold text-textMain mt-0.5">
                    {{ $isClockedIn ? 'Active On-Duty Shift' : 'Not Clocked In Today' }}
                </h3>
                @if($todayLatest)
                    <p class="text-xs text-textMuted">
                        Last recorded event: <strong>{{ $todayLatest->is_clock_in ? 'Clocked In' : 'Clocked Out' }}</strong> at {{ $todayLatest->recorded_at->format('h:i A') }}
                    </p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2.5 w-full md:w-auto md:flex md:items-center md:gap-3">
            <button type="button" 
                    @click="window.openClockModal('in')" 
                    class="h-11 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed text-center shrink-0" 
                    {{ $isClockedIn ? 'disabled' : '' }}>
                <i data-lucide="camera" class="w-4 h-4 shrink-0"></i>
                <span class="truncate">Clock In</span>
            </button>

            <button type="button" 
                    @click="window.openClockModal('out')" 
                    class="h-11 px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed text-center shrink-0" 
                    {{ !$isClockedIn ? 'disabled' : '' }}>
                <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
                <span class="truncate">Clock Out</span>
            </button>

            <a href="{{ route('attendance.leaderboard') }}" 
               class="h-11 px-3 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 text-center shrink-0">
                <i data-lucide="trophy" class="w-4 h-4 shrink-0"></i>
                <span class="truncate">Leaderboard</span>
            </a>
        </div>
    </div>

    <!-- ATTENDANCE AUDIT LOG CARDS -->
    <div class="space-y-4" x-data="{ filterModalOpen: false }">
        @php
            $activeFilterCount = (request('user_id') ? 1 : 0) + (request('date') ? 1 : 0);
        @endphp

        <div class="bg-surface rounded-2xl p-4 sm:p-5 border border-borderMain shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
                    <span>Attendance Records & Photo Verification</span>
                    @if($activeFilterCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            {{ $activeFilterCount }} active filter(s)
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-textMuted">Photo submission proof with server timestamp</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(request()->hasAny(['user_id', 'date']))
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-xl border border-borderMain text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition">
                        Reset
                    </a>
                @endif

                <button type="button" @click="filterModalOpen = true" class="px-4 py-2 rounded-xl border border-borderMain bg-surface hover:bg-bg text-textMain text-xs font-bold flex items-center justify-center gap-2 transition shadow-sm">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-primary"></i>
                    <span>Filters</span>
                    @if($activeFilterCount > 0)
                        <span class="w-5 h-5 rounded-full bg-accent text-primary text-[10px] font-black flex items-center justify-center">{{ $activeFilterCount }}</span>
                    @endif
                </button>
            </div>
        </div>

        <!-- FILTER POPUP MODAL -->
        <div x-show="filterModalOpen" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-surface rounded-3xl max-w-md w-full p-6 border border-borderMain shadow-2xl relative"
                 @click.away="filterModalOpen = false"
                 x-transition:enter="transition ease-out duration-200 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex items-center justify-between pb-4 border-b border-borderMain">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-textMain">Filter Attendance Logs</h3>
                            <p class="text-[11px] text-textMuted">Filter by field engineer or duty date</p>
                        </div>
                    </div>
                    <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg hover:bg-bg transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('attendance.index') }}" method="GET" class="mt-4 space-y-4">
                    @if(auth()->user()->isAdmin())
                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1">Field Engineer</label>
                            <select name="user_id" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                                <option value="">All Engineers</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}" {{ request('user_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-textMain mb-1">Specific Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                    </div>

                    <div class="pt-4 border-t border-borderMain flex items-center justify-between gap-3">
                        @if(request()->hasAny(['user_id', 'date']))
                            <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl border border-borderMain text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
                                Clear Filters
                            </a>
                        @else
                            <div></div>
                        @endif

                        <div class="flex items-center space-x-2">
                            <button type="button" @click="filterModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-textMuted hover:text-textMain">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($attendances as $att)
                <div class="bg-surface rounded-2xl border border-borderMain p-5 shadow-sm hover:border-secondary hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Top Staff & Status -->
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-borderMain">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0 {{ $att->is_clock_in ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    <i data-lucide="{{ $att->is_clock_in ? 'user-check' : 'user-x' }}" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-textMain truncate">{{ $att->user?->name }}</h4>
                                    <span class="text-[11px] text-textMuted">{{ $att->user?->phone_no }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $att->is_clock_in ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $att->is_clock_in ? 'Clock In' : 'Clock Out' }}
                            </span>
                        </div>

                        <!-- Date, Time & Selfie -->
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center space-x-1.5 text-textMain font-bold">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-textMuted"></i>
                                    <span>{{ $att->recorded_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5 text-textMuted">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span>{{ $att->recorded_at->format('h:i A') }}</span>
                                </div>
                            </div>

                            @if($att->photo)
                                <a href="{{ asset('storage/' . $att->photo) }}" target="_blank" class="block w-14 h-14 rounded-xl overflow-hidden border border-borderMain hover:scale-105 transition shadow-sm shrink-0 bg-slate-100" title="Click to view selfie proof">
                                    <img src="{{ asset('storage/' . $att->photo) }}" alt="Selfie" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\' fill=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/></svg>';">
                                </a>
                            @else
                                <div class="w-14 h-14 rounded-xl bg-bg border border-borderMain flex items-center justify-center text-textMuted text-[10px] text-center font-medium shrink-0">
                                    No Photo
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer: Approval & Admin Action -->
                    <div class="pt-3 border-t border-borderMain flex items-center justify-between text-xs gap-2">
                        <div>
                            @if($att->is_approved)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 inline-block">
                                    Approved
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 inline-block">
                                    Pending Review
                                </span>
                            @endif
                            @if($att->admin_remarks)
                                <div class="text-[10px] text-textMuted italic mt-0.5">{{ $att->admin_remarks }}</div>
                            @endif
                        </div>

                        @if(auth()->user()->isAdmin())
                            <div>
                                @if(!$att->is_approved)
                                    <form action="{{ route('attendance.review', $att) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="is_approved" value="1">
                                        <input type="hidden" name="admin_remarks" value="Verified by admin">
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-sm transition">
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-emerald-600 font-bold flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Verified</span>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-textMuted bg-surface rounded-2xl border border-borderMain">
                    <i data-lucide="calendar-x" class="w-10 h-10 mx-auto text-textMuted/40 mb-2"></i>
                    <p class="font-medium text-xs">No attendance records found for the selected date/filter.</p>
                </div>
            @endforelse
        </div>

        @if($attendances->hasPages())
            <div class="bg-surface rounded-2xl border border-borderMain p-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
