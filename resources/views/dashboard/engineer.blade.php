@extends('layouts.app')

@section('title', 'Engineer Field Portal')
@section('page_title', 'Engineer Field Command')

@section('content')
<div class="w-full space-y-4 sm:space-y-5">

    <!-- 1. 2 ANALYTICS CHARTS (SERVICE CALLS & PAYMENT COLLECTIONS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
        
        <!-- CARD 1: SERVICES REPORT -->
        <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <!-- Top Heading & Total -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-slate-800">Services Breakdown</span>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-lg">
                        Total: {{ $myTotal }}
                    </span>
                </div>

                <!-- Bar Chart Canvas -->
                <div class="relative w-full h-44 sm:h-48 mt-2">
                    <canvas id="engineerServiceChart"></canvas>
                </div>
            </div>

            <!-- Bottom Status Metrics -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 mt-2 border-t border-slate-100 text-center">
                <div class="p-1.5 rounded-lg bg-amber-50 border border-amber-200/60">
                    <div class="text-[10px] font-semibold text-amber-800">Pending</div>
                    <div class="text-xs sm:text-sm font-black text-amber-900">{{ $myPending }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-sky-50 border border-sky-200/60">
                    <div class="text-[10px] font-semibold text-sky-800">In Progress</div>
                    <div class="text-xs sm:text-sm font-black text-sky-900">{{ $myInProgress }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60">
                    <div class="text-[10px] font-semibold text-emerald-800">Completed</div>
                    <div class="text-xs sm:text-sm font-black text-emerald-900">{{ $myCompleted }}</div>
                </div>
            </div>
        </div>

        <!-- CARD 2: PAYMENTS REPORT -->
        <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <!-- Top Heading & Total -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-slate-800">Payments & Ledger</span>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-900 bg-emerald-50 text-emerald-800 border border-emerald-200/60 px-2 py-0.5 rounded-lg">
                        Total: ₹{{ number_format($myTotalBilled, 0) }}
                    </span>
                </div>

                <!-- Bar Chart Canvas -->
                <div class="relative w-full h-44 sm:h-48 mt-2">
                    <canvas id="engineerPaymentChart"></canvas>
                </div>
            </div>

            <!-- Bottom Status Metrics -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 mt-2 border-t border-slate-100 text-center">
                <div class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60">
                    <div class="text-[10px] font-semibold text-emerald-800">Cash</div>
                    <div class="text-xs sm:text-sm font-black text-emerald-900">₹{{ number_format($myCashCollected, 0) }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-amber-50 border border-amber-200/60">
                    <div class="text-[10px] font-semibold text-amber-800">Credit</div>
                    <div class="text-xs sm:text-sm font-black text-amber-900">₹{{ number_format($myCreditPending, 0) }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-teal-50 border border-teal-200/60">
                    <div class="text-[10px] font-semibold text-teal-800">GST Bill</div>
                    <div class="text-xs sm:text-sm font-black text-teal-900">₹{{ number_format($myGstBilled, 0) }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. ESSENTIAL 4 ACTIONS (Original Bhumi App Modules: Tasks, Bills, Expenses, Attendance) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3.5">
        <!-- Tile 1: My Tasks (Assigned Complaints) -->
        <a href="{{ route('complaints.index') }}" class="bg-surface hover:bg-slate-50 p-3 sm:p-4 rounded-2xl border border-borderMain shadow-2xs transition active:scale-95 flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="wrench" class="w-5 h-5 text-secondary"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-textMain truncate">My Tasks</span>
                    @if(($myPending + $myInProgress) > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-accent text-primary text-[10px] font-black">
                            {{ $myPending + $myInProgress }}
                        </span>
                    @endif
                </div>
                <p class="text-[10px] text-textMuted mt-0.5 truncate">Assigned complaints</p>
            </div>
        </a>

        <!-- Tile 2: My Bills (Original Android feature) -->
        <a href="{{ route('billing.index') }}" class="bg-surface hover:bg-slate-50 p-3 sm:p-4 rounded-2xl border border-borderMain shadow-2xs transition active:scale-95 flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="receipt" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold text-textMain truncate block">My Bills</span>
                <p class="text-[10px] text-textMuted mt-0.5 truncate">Billing ledger</p>
            </div>
        </a>

        <!-- Tile 3: My Expenses (Original Android feature) -->
        <a href="{{ route('expenses.index') }}" class="bg-surface hover:bg-slate-50 p-3 sm:p-4 rounded-2xl border border-borderMain shadow-2xs transition active:scale-95 flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="credit-card" class="w-5 h-5 text-amber-600"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold text-textMain truncate block">Expenses</span>
                <p class="text-[10px] text-textMuted mt-0.5 truncate">Claims & vouchers</p>
            </div>
        </a>

        <!-- Tile 4: Attendance (Original Android feature) -->
        <a href="{{ route('attendance.index') }}" class="bg-surface hover:bg-slate-50 p-3 sm:p-4 rounded-2xl border border-borderMain shadow-2xs transition active:scale-95 flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="clock" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold text-textMain truncate block">Attendance</span>
                <p class="text-[10px] text-textMuted mt-0.5 truncate">Shift punch history</p>
            </div>
        </a>
    </div>

    <!-- 4. CORE PERFORMANCE STATS (Clean 3-Card Summary) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3.5">
        <!-- Stat 1: Active Tasks -->
        <div class="bg-surface p-3.5 sm:p-4 rounded-2xl border border-borderMain shadow-2xs">
            <div class="text-[10px] sm:text-[11px] font-bold text-amber-700 uppercase tracking-wider">Active & Pending</div>
            <div class="text-xl sm:text-2xl font-black text-amber-600 mt-1">{{ $myPending + $myInProgress }}</div>
            <div class="text-[10px] text-textMuted mt-0.5">{{ $myInProgress }} currently working • {{ $myPending }} pending</div>
        </div>

        <!-- Stat 2: Completed Tasks -->
        <div class="bg-surface p-3.5 sm:p-4 rounded-2xl border border-borderMain shadow-2xs">
            <div class="text-[10px] sm:text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Completed Jobs</div>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-1">{{ $myCompleted }}</div>
            <div class="text-[10px] text-textMuted mt-0.5">Resolved service calls</div>
        </div>

        <!-- Stat 3: Cash to Handover -->
        <div class="bg-surface p-3.5 sm:p-4 rounded-2xl border border-borderMain shadow-2xs">
            <div class="text-[10px] sm:text-[11px] font-bold text-textMuted uppercase tracking-wider">Cash to Handover</div>
            <div class="text-xl sm:text-2xl font-black text-textMain mt-1">₹{{ number_format($myCashToHandover, 2) }}</div>
            <div class="text-[10px] text-textMuted mt-0.5">Collected from clients</div>
        </div>
    </div>

    <!-- 5. MY ASSIGNED WORK ORDERS (Streamlined Task Cards) -->
    <div class="bg-surface rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-borderMain shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-borderMain mb-3.5">
            <div>
                <h3 class="font-extrabold text-sm sm:text-base text-textMain flex items-center space-x-2">
                    <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                    <span>My Assigned Work Orders</span>
                </h3>
                <p class="text-[11px] text-textMuted">Tap a job card to call customer, view location, or complete bill</p>
            </div>
            <a href="{{ route('complaints.index') }}" class="text-xs text-secondary font-bold hover:underline shrink-0">
                All Tickets &rarr;
            </a>
        </div>

        <div class="space-y-3">
            @forelse($assignedTickets as $ticket)
                <div class="p-3 sm:p-4 rounded-2xl bg-white border border-borderMain/80 shadow-2xs transition hover:shadow-xs space-y-2 border-l-4 {{ $ticket->status === 'in_progress' ? 'border-l-amber-500' : 'border-l-secondary' }}">
                    <!-- Header Line: Ticket ID + Status Badge -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded-lg font-black text-xs bg-primary text-white shadow-2xs">
                                {{ $ticket->ticket_number }}
                            </span>
                            @if($ticket->status === 'in_progress')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">IN PROGRESS</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">PENDING</span>
                            @endif
                        </div>
                        @if($ticket->due_date)
                            <div class="text-[11px] font-medium text-textMuted shrink-0">
                                Due: <strong>{{ $ticket->due_date->format('d M, h:i A') }}</strong>
                            </div>
                        @endif
                    </div>

                    <!-- Client & Fast Action Icons (Call & Map) -->
                    <div class="flex items-start justify-between gap-2 pt-0.5">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-extrabold text-xs sm:text-sm text-textMain truncate leading-tight">
                                {{ $ticket->customer?->name ?? 'Direct Customer' }}
                            </h4>
                            <p class="text-[11px] text-textMuted truncate mt-0.5">
                                {{ $ticket->organization?->name ?? 'Individual Customer' }}
                            </p>
                            @if($ticket->customer?->address)
                                <p class="text-[10px] text-slate-500 mt-0.5 truncate flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 shrink-0 text-slate-400"></i>
                                    <span class="truncate">{{ $ticket->customer->address }}</span>
                                </p>
                            @endif
                        </div>

                        <!-- 1-Tap Call & Map Action Buttons -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($ticket->customer?->contact_no)
                                <a href="tel:{{ $ticket->customer->contact_no }}" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" title="Call Customer">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                            @if($ticket->customer?->address || $ticket->customer?->name)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($ticket->customer?->address ? $ticket->customer->address . ', ' : '') . ($ticket->customer?->name ?? 'Surat')) }}" target="_blank" class="w-8 h-8 rounded-xl bg-blue-50 text-secondary hover:bg-secondary hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" title="Open Map">
                                    <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Machine & Service Tag -->
                    <div class="flex items-center gap-1.5 text-xs text-textMain">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-secondary shrink-0"></i>
                        <span class="font-bold truncate">{{ $ticket->productType?->name ?? 'Industrial Machinery' }}</span>
                        <span class="text-textMuted text-[11px] truncate">• {{ $ticket->serviceType?->name ?? 'Service' }}</span>
                    </div>

                    <!-- Issue Description -->
                    @if($ticket->issue_description)
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100">
                            {{ $ticket->issue_description }}
                        </p>
                    @endif

                    <!-- Footer: View Details & Primary Action -->
                    <div class="flex items-center justify-between gap-3 pt-2 border-t border-borderMain/60">
                        <a href="{{ route('complaints.show', $ticket) }}" class="text-xs font-bold text-secondary hover:text-primary transition flex items-center gap-1 active:scale-95">
                            <span>Details</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>

                        @if(!$ticket->is_started)
                            <form action="{{ route('complaints.start', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition active:scale-95 flex items-center gap-1.5">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    <span>Start Job</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('billing.create', ['complaint_id' => $ticket->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-accent hover:bg-amber-400 text-primary text-xs font-black shadow-2xs transition active:scale-95 flex items-center gap-1.5">
                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                <span>Complete & Bill</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-textMuted bg-bg rounded-2xl border border-borderMain/60">
                    <i data-lucide="check-circle" class="w-10 h-10 mx-auto text-emerald-500 mb-2"></i>
                    <p class="font-extrabold text-sm text-textMain">All caught up!</p>
                    <p class="text-xs text-textMuted">No active tickets assigned to you right now.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. SERVICES BREAKDOWN BAR CHART
    const ctxService = document.getElementById('engineerServiceChart')?.getContext('2d');
    if (ctxService) {
        new Chart(ctxService, {
            type: 'bar',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [{{ $myPending }}, {{ $myInProgress }}, {{ $myCompleted }}],
                    backgroundColor: ['#FFA500', '#00BFFF', '#32CD32'],
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.raw + ' Services'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        ticks: { font: { size: 11, weight: '600' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. PAYMENTS & LEDGER BAR CHART
    const ctxPayment = document.getElementById('engineerPaymentChart')?.getContext('2d');
    if (ctxPayment) {
        new Chart(ctxPayment, {
            type: 'bar',
            data: {
                labels: ['Cash', 'Credit', 'GST Bill'],
                datasets: [{
                    data: [{{ $myCashCollected }}, {{ $myCreditPending }}, {{ $myGstBilled }}],
                    backgroundColor: ['#32CD32', '#FFA500', '#42919E'],
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ₹' + Number(ctx.raw).toLocaleString('en-IN')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => v >= 1000 ? (v / 1000) + 'k' : v,
                            font: { size: 10 }
                        },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        ticks: { font: { size: 11, weight: '600' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

});
</script>
@endpush
