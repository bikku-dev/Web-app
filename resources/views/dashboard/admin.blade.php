@extends('layouts.app')

@section('title', 'Reports | Bhumi Technocare')
@section('page_title', 'Bhumi Technocare')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-3" x-data="{ 
    activeTab: 'services',
    tabs: ['services', 'payments', 'products'],
    nextTab() {
        let i = this.tabs.indexOf(this.activeTab);
        this.activeTab = this.tabs[(i + 1) % this.tabs.length];
        this.triggerChartResize();
    },
    prevTab() {
        let i = this.tabs.indexOf(this.activeTab);
        this.activeTab = this.tabs[(i - 1 + this.tabs.length) % this.tabs.length];
        this.triggerChartResize();
    },
    triggerChartResize() {
        this.$nextTick(() => {
            window.dispatchEvent(new Event('resize'));
        });
    }
}">

    <!-- 1. TOP TITLE & DURATION FILTER ROW (COMPACT, MATCHES ANDROID APP) -->
    <div class="flex items-center justify-between gap-2 px-1">
        <div class="min-w-0">
            <h2 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight leading-tight truncate">
                Hi {{ auth()->user()->name }},
            </h2>
            <p class="text-[11px] sm:text-xs text-textMuted truncate">
                Reports & Analytics Overview
            </p>
        </div>

        <!-- Android-Style Duration Spinner Dropdown -->
        <div class="flex items-center space-x-1 shrink-0">
            <div class="relative">
                <select onchange="window.location.href='?days='+this.value" 
                        class="text-xs sm:text-xs font-bold text-primary bg-surface border border-slate-300 rounded-xl px-2.5 py-1.5 shadow-2xs focus:border-primary outline-none cursor-pointer">
                    <option value="30" {{ request('days', 30) == 30 ? 'selected' : '' }}>Last 30 days</option>
                    <option value="7" {{ request('days') == 7 ? 'selected' : '' }}>Last 7 days</option>
                    <option value="90" {{ request('days') == 90 ? 'selected' : '' }}>Last 90 days</option>
                    <option value="365" {{ request('days') == 365 ? 'selected' : '' }}>Last 365 days</option>
                    <option value="3650" {{ request('days') == 3650 ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 2. MOBILE REPORT SELECTOR TABS (PHONE ZERO-SCROLL GUARANTEE) -->
    <!-- Visible only on mobile (< lg), so all 3 graphs fit on phone screens without scrolling -->
    <div class="lg:hidden">
        <div class="grid grid-cols-3 p-1 bg-slate-200/80 rounded-xl gap-1 text-center">
            <button type="button" 
                    @click="activeTab = 'services'; triggerChartResize()"
                    :class="activeTab === 'services' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg text-xs transition truncate">
                Services ({{ $totalComplaints }})
            </button>

            <button type="button" 
                    @click="activeTab = 'payments'; triggerChartResize()"
                    :class="activeTab === 'payments' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg text-xs transition truncate">
                Payments (₹{{ $totalBilled >= 1000 ? round($totalBilled / 1000, 1) . 'k' : number_format($totalBilled, 0) }})
            </button>

            <button type="button" 
                    @click="activeTab = 'products'; triggerChartResize()"
                    :class="activeTab === 'products' ? 'bg-white text-primary font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-lg text-xs transition truncate">
                Products ({{ $productWise->sum('total') }})
            </button>
        </div>
    </div>

    <!-- 3. THE 3 REPORT CARDS -->
    <!-- On Desktop (lg:): All 3 shown side-by-side in a 3-column grid -->
    <!-- On Mobile (< lg): Active tab shown, fits on 1 screen with zero scroll -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3.5 sm:gap-4">

        <!-- ================= CARD 1: SERVICES REPORT ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
             x-show="activeTab === 'services' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <!-- Top Heading & Total (Matches Android Card Layout) -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-sm sm:text-base font-bold text-slate-800">Services</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-900">Total : {{ $totalComplaints }}</span>
                </div>

                <!-- Bar Chart Canvas -->
                <div class="relative w-full h-44 sm:h-48 mt-2">
                    <canvas id="serviceBarChart"></canvas>
                </div>
            </div>

            <!-- Bottom Status Metrics -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 mt-2 border-t border-slate-100 text-center">
                <div class="p-1.5 rounded-lg bg-amber-50 border border-amber-200/60">
                    <div class="text-[10px] font-semibold text-amber-800">Pending</div>
                    <div class="text-xs sm:text-sm font-black text-amber-900">{{ $pendingComplaints }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-sky-50 border border-sky-200/60">
                    <div class="text-[10px] font-semibold text-sky-800">Ongoing</div>
                    <div class="text-xs sm:text-sm font-black text-sky-900">{{ $inProgressComplaints }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60">
                    <div class="text-[10px] font-semibold text-emerald-800">Done</div>
                    <div class="text-xs sm:text-sm font-black text-emerald-900">{{ $completedComplaints }}</div>
                </div>
            </div>
        </div>

        <!-- ================= CARD 2: PAYMENTS REPORT ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
             x-show="activeTab === 'payments' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <!-- Top Heading & Total (Matches Android Card Layout) -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-sm sm:text-base font-bold text-slate-800">Payments</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-900">Total : ₹{{ number_format($totalBilled, 0) }}</span>
                </div>

                <!-- Bar Chart Canvas -->
                <div class="relative w-full h-44 sm:h-48 mt-2">
                    <canvas id="billBarChart"></canvas>
                </div>
            </div>

            <!-- Bottom Status Metrics -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 mt-2 border-t border-slate-100 text-center">
                <div class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60">
                    <div class="text-[10px] font-semibold text-emerald-800">Cash</div>
                    <div class="text-xs sm:text-sm font-black text-emerald-900">₹{{ number_format($cashCollected, 0) }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-amber-50 border border-amber-200/60">
                    <div class="text-[10px] font-semibold text-amber-800">Credit</div>
                    <div class="text-xs sm:text-sm font-black text-amber-900">₹{{ number_format($creditOutstanding, 0) }}</div>
                </div>
                <div class="p-1.5 rounded-lg bg-teal-50 border border-teal-200/60">
                    <div class="text-[10px] font-semibold text-teal-800">GST Bill</div>
                    <div class="text-xs sm:text-sm font-black text-teal-900">₹{{ number_format($gstBilled, 0) }}</div>
                </div>
            </div>
        </div>

        <!-- ================= CARD 3: ISSUES FOR EACH PRODUCT ================= -->
        <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
             x-show="activeTab === 'products' || window.innerWidth >= 1024"
             x-cloak>
            <div>
                <!-- Top Heading & Total (Matches Android Card Layout) -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-sm sm:text-base font-bold text-slate-800 truncate pr-2">Issues for each product</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-900 shrink-0">Total : {{ $productWise->sum('total') }}</span>
                </div>

                <!-- Bar Chart Canvas -->
                <div class="relative w-full h-44 sm:h-48 mt-2">
                    <canvas id="paymentBarChart"></canvas>
                </div>
            </div>

            <!-- Bottom Product Summary Tags -->
            <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between text-xs text-textMuted">
                <span class="text-[11px] font-semibold truncate">
                    @if($productWise->isNotEmpty())
                        Top: {{ $productWise->first()->name }} ({{ $productWise->first()->total }})
                    @else
                        Machinery Breakdown Lines
                    @endif
                </span>
                <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full shrink-0">
                    Equipment Issues
                </span>
            </div>
        </div>

    </div>

    <!-- Mobile Quick Prev / Next Switchers -->
    <div class="flex lg:hidden items-center justify-between pt-1 text-xs px-1 text-textMuted">
        <button type="button" @click="prevTab()" class="inline-flex items-center space-x-1 py-1 px-2 rounded-lg hover:bg-slate-200 transition font-bold text-slate-600">
            <span>← Previous</span>
        </button>
        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400">
            Report <span x-text="tabs.indexOf(activeTab) + 1"></span> of 3
        </span>
        <button type="button" @click="nextTab()" class="inline-flex items-center space-x-1 py-1 px-2 rounded-lg hover:bg-slate-200 transition font-bold text-primary">
            <span>Next →</span>
        </button>
    </div>

    <!-- ================= SENIOR LEVEL ERP SECTIONS (DESKTOP ONLY: hidden lg:block) ================= -->
    <!-- Gives PC users a full, senior-level industrial operations dashboard without cluttering mobile -->
    <div class="hidden lg:block space-y-4 pt-3">
        
        <!-- 1. LIVE OPERATIONS METRIC STRIP -->
        <div class="grid grid-cols-4 gap-3">
            <!-- Staff On Duty -->
            <div class="bg-surface rounded-2xl border border-borderMain p-3.5 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-textMuted block">Staff On Duty</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">
                        {{ $todayAttendanceCount }} <span class="text-xs font-semibold text-slate-400">/ {{ $totalEngineers }} Active</span>
                    </div>
                    <span class="text-[10.5px] text-emerald-600 font-bold mt-0.5 block flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Field Technicians
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                    <i data-lucide="hard-hat" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Cash Pending Handover -->
            <div class="bg-surface rounded-2xl border border-borderMain p-3.5 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-textMuted block">Pending Handover</span>
                    <div class="text-xl font-black text-rose-700 mt-0.5">
                        ₹{{ number_format($pendingHandover, 0) }}
                    </div>
                    <span class="text-[10.5px] text-rose-600 font-semibold mt-0.5 block">
                        Cash with Staff
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Overdue / Urgent Calls -->
            <div class="bg-surface rounded-2xl border border-borderMain p-3.5 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-textMuted block">Urgent / Overdue</span>
                    <div class="text-xl font-black text-amber-700 mt-0.5">
                        {{ $overdueComplaints }} <span class="text-xs font-semibold text-slate-400">Calls</span>
                    </div>
                    <span class="text-[10.5px] text-amber-600 font-semibold mt-0.5 block">
                        Immediate Attention Needed
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Pending Expense Reimbursements -->
            <div class="bg-surface rounded-2xl border border-borderMain p-3.5 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-textMuted block">Pending Expenses</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">
                        {{ $pendingExpenses }} <span class="text-xs font-semibold text-slate-400">Claims</span>
                    </div>
                    <span class="text-[10.5px] text-secondary font-semibold mt-0.5 block">
                        To Review & Approve
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-secondary flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- 2. TWO-COLUMN SENIOR OPERATIONS CENTER -->
        <div class="grid grid-cols-12 gap-3.5">
            
            <!-- Left (8 Cols): Recent & Active Service Complaints Queue -->
            <div class="col-span-8 bg-surface rounded-2xl border border-borderMain p-4 shadow-2xs space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <i data-lucide="wrench" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-sm text-slate-800">Live Service Tickets Queue</h3>
                    </div>
                    <a href="{{ route('complaints.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                        <span>View All Tickets</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-2">Ticket #</th>
                                <th class="pb-2">Customer & Plant</th>
                                <th class="pb-2">Equipment Type</th>
                                <th class="pb-2">Assigned Staff</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($recentComplaints->take(5) as $ticket)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-2.5">
                                        <a href="{{ route('complaints.show', $ticket) }}" class="font-extrabold text-primary hover:underline">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                        @if($ticket->priority === 'urgent')
                                            <span class="ml-1 px-1.5 py-0.2 rounded text-[9px] font-black bg-rose-100 text-rose-800 uppercase">Urgent</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 max-w-[160px] truncate">
                                        <div class="font-bold text-slate-800 truncate">{{ $ticket->customer?->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-textMuted truncate">{{ $ticket->organization?->name ?? 'Direct' }}</div>
                                    </td>
                                    <td class="py-2.5 truncate max-w-[130px] text-slate-700">
                                        {{ $ticket->productType?->name ?? 'Machinery Breakdown' }}
                                    </td>
                                    <td class="py-2.5 text-slate-700">
                                        {{ $ticket->engineer?->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="py-2.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase {{ $ticket->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($ticket->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right">
                                        <a href="{{ route('complaints.show', $ticket) }}" class="p-1.5 rounded-lg border border-borderMain hover:bg-slate-100 text-slate-600 inline-flex transition">
                                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-textMuted text-xs">
                                        No active complaints recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right (4 Cols): Operational Notices & Quick Master Hub -->
            <div class="col-span-4 space-y-3.5">
                
                <!-- Notice Board / Broadcasts -->
                <div class="bg-surface rounded-2xl border border-borderMain p-4 shadow-2xs space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-800">Notice Board</h3>
                        </div>
                        <a href="{{ route('notices.index') }}" class="text-xs font-bold text-primary hover:underline">
                            All Notices
                        </a>
                    </div>

                    <div class="space-y-2">
                        @forelse($activeNotices as $notice)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900 leading-tight">{{ $notice->title }}</span>
                                    <span class="text-[9.5px] text-slate-400">{{ $notice->created_at->format('d M') }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600 line-clamp-2">{{ $notice->content }}</p>
                            </div>
                        @empty
                            <div class="py-4 text-center text-textMuted text-xs">
                                No active company announcements.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Executive Quick Action Hub -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-4 text-white shadow-md space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs uppercase tracking-wider text-slate-300">Fast Operations</span>
                        <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded-full text-amber-300 font-bold">ERP Hub</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <a href="{{ route('customers.index') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/15 transition flex items-center space-x-2 font-bold text-white">
                            <i data-lucide="users" class="w-4 h-4 text-teal-400"></i>
                            <span class="truncate">Customers</span>
                        </a>
                        <a href="{{ route('billing.create') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/15 transition flex items-center space-x-2 font-bold text-white">
                            <i data-lucide="receipt" class="w-4 h-4 text-amber-400"></i>
                            <span class="truncate">New Bill</span>
                        </a>
                        <a href="{{ route('engineers.index') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/15 transition flex items-center space-x-2 font-bold text-white">
                            <i data-lucide="hard-hat" class="w-4 h-4 text-emerald-400"></i>
                            <span class="truncate">Staff Directory</span>
                        </a>
                        <a href="{{ route('billing.index') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/15 transition flex items-center space-x-2 font-bold text-white">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-cyan-400"></i>
                            <span class="truncate">Excel Ledger</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. SERVICES BAR CHART (Android-style serviceBarChart)
    const ctxService = document.getElementById('serviceBarChart')?.getContext('2d');
    if (ctxService) {
        new Chart(ctxService, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Ongoing', 'Done'],
                datasets: [{
                    data: [{{ $pendingComplaints }}, {{ $inProgressComplaints }}, {{ $completedComplaints }}],
                    backgroundColor: ['#FFA500', '#00BFFF', '#32CD32'],
                    borderRadius: 6,
                    maxBarThickness: 38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.raw + ' Calls'
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

    // 2. PAYMENTS BAR CHART (Android-style billBarChart)
    const ctxBill = document.getElementById('billBarChart')?.getContext('2d');
    if (ctxBill) {
        new Chart(ctxBill, {
            type: 'bar',
            data: {
                labels: ['Cash', 'Credit', 'GST Bill'],
                datasets: [{
                    data: [{{ $cashCollected }}, {{ $creditOutstanding }}, {{ $gstBilled }}],
                    backgroundColor: ['#32CD32', '#FFA500', '#42919E'],
                    borderRadius: 6,
                    maxBarThickness: 38
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

    // 3. PRODUCT-WISE ISSUES BAR CHART (Android-style paymentBarChart for products)
    const ctxProduct = document.getElementById('paymentBarChart')?.getContext('2d');
    if (ctxProduct) {
        const prodLabels = {!! json_encode($productWise->pluck('name')) !!};
        const prodData = {!! json_encode($productWise->pluck('total')) !!};

        new Chart(ctxProduct, {
            type: 'bar',
            data: {
                labels: prodLabels.length ? prodLabels : ['General Equipment'],
                datasets: [{
                    data: prodData.length ? prodData : [0],
                    backgroundColor: ['#42919E', '#31707B', '#5DBB63', '#00BFFF', '#FFA500', '#6366F1'],
                    borderRadius: 6,
                    maxBarThickness: 38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.raw + ' Issues'
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
                        ticks: { 
                            font: { size: 10, weight: '600' },
                            callback: function(val) {
                                let label = this.getLabelForValue(val) || '';
                                return label.length > 10 ? label.substr(0, 10) + '...' : label;
                            }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
