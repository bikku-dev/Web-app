@extends('layouts.app')

@section('title', 'Service Hours Leaderboard | Bhumi Technocare')
@section('page_title', 'Engineer Leaderboard')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-borderMain">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-secondary mb-1">
                <a href="{{ route('attendance.index') }}" class="hover:underline flex items-center space-x-1 text-secondary">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Attendance Records</span>
                </a>
                <span class="text-textMuted">/</span>
                <span class="text-textMain">Performance Rankings</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-textMain flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-600 flex items-center justify-center shrink-0 shadow-sm">
                    <i data-lucide="trophy" class="w-5 h-5"></i>
                </span>
                <span>Field Engineer Service Hours Leaderboard</span>
            </h1>
            <p class="text-xs text-textMuted mt-1">Calculated from verified clock-in to clock-out (capped at 8 hrs/session per company policy)</p>
        </div>

        <!-- Time Filter Form -->
        <form action="{{ route('attendance.leaderboard') }}" method="GET" class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs text-textMuted font-bold whitespace-nowrap">Evaluation Period:</span>
            <select name="days" class="px-3.5 py-2 text-xs rounded-xl border border-borderMain bg-surface font-bold text-textMain outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-sm cursor-pointer" onchange="this.form.submit()">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="15" {{ $days == 15 ? 'selected' : '' }}>Last 15 Days</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 3 Months</option>
            </select>
        </form>
    </div>

    <!-- Leaderboard Table & Rankings -->
    <div class="bg-surface rounded-3xl border border-borderMain shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-borderMain bg-bg/50 flex items-center justify-between">
            <span class="text-xs font-black text-primary uppercase tracking-wider flex items-center space-x-1.5">
                <i data-lucide="award" class="w-4 h-4 text-secondary"></i>
                <span>Top Field Performers & Shift Hours</span>
            </span>
            <span class="text-xs text-textMuted font-medium">Showing {{ count($leaderboard) }} ranked engineers</span>
        </div>

        <div class="divide-y divide-borderMain">
            @forelse($leaderboard as $index => $item)
                @php
                    $rank = $index + 1;
                    $medalClass = match($rank) {
                        1 => 'bg-amber-100 text-amber-800 border-amber-300',
                        2 => 'bg-slate-200 text-slate-800 border-slate-300',
                        3 => 'bg-orange-100 text-orange-800 border-orange-300',
                        default => 'bg-bg text-textMuted border-borderMain'
                    };
                @endphp
                <div class="p-4 sm:p-5 flex items-center justify-between gap-3 sm:gap-4 hover:bg-bg/40 transition-colors">
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                        <!-- Rank Badge -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl border flex items-center justify-center font-black text-sm sm:text-base {{ $medalClass }} shrink-0 shadow-sm">
                            @if($rank === 1)
                                <i data-lucide="crown" class="w-5 h-5 text-amber-600"></i>
                            @else
                                #{{ $rank }}
                            @endif
                        </div>

                        <!-- Profile Photo -->
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl overflow-hidden shrink-0 border border-borderMain shadow-sm bg-slate-100">
                            <img src="{{ $item['engineer']->profile_photo_url }}" alt="{{ $item['engineer']->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item['engineer']->name) }}&background=1E3A5F&color=FFFFFF&bold=true&size=128';">
                        </div>

                        <!-- Engineer Info -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm sm:text-base font-black text-textMain leading-tight truncate">
                                    {{ $item['engineer']->name }}
                                </h3>
                            </div>

                            <!-- Department & Specialization Badge (Clean inline pill on its own line) -->
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center text-[10px] font-extrabold px-2 py-0.5 rounded-lg bg-blue-50 text-secondary border border-blue-200">
                                    <i data-lucide="wrench" class="w-2.5 h-2.5 mr-1 text-secondary"></i>
                                    <span>{{ $item['engineer']->department ?? 'Field Service' }}</span>
                                </span>
                            </div>

                            <!-- Post & Active Service Days -->
                            <p class="text-[11px] sm:text-xs text-textMuted mt-1 leading-snug">
                                <span class="font-medium text-textMain">{{ $item['engineer']->post ?? 'Field Service Engineer' }}</span>
                                <span class="text-textMuted mx-1">•</span>
                                <span class="font-bold text-emerald-700">{{ $item['days_worked'] }} active {{ Str::plural('day', $item['days_worked']) }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Hours Count -->
                    <div class="text-right shrink-0 pl-2">
                        <div class="text-xl sm:text-2xl font-black text-primary leading-tight">
                            {{ $item['total_hours'] }} <span class="text-xs font-bold text-textMuted">hrs</span>
                        </div>
                        <span class="text-[10px] text-textMuted font-bold block whitespace-nowrap mt-0.5">Credited On-Site</span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-textMuted text-xs space-y-2">
                    <i data-lucide="clock" class="w-8 h-8 text-textMuted opacity-50 mx-auto"></i>
                    <p class="font-bold text-textMain">No attendance logs recorded in this evaluation window.</p>
                    <p class="text-[11px]">Attendance records will appear once engineers clock-in and clock-out from field calls.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
