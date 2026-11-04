@extends('layouts.app')

@section('title', 'Billing & Ledger | Bhumi Technocare')
@section('page_title', 'Billing & Ledger')

@section('content')
<div class="space-y-3" x-data="{ 
    filterModalOpen: false,
    exportModalOpen: false,
    previewPhotoOpen: false,
    previewPhotoUrl: '',
    previewPhotoTitle: '',
    exportFromDate: '{{ request('from_date', '') }}',
    exportToDate: '{{ request('to_date', '') }}',
    setExportPreset(type) {
        const today = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        if (type === 'all') {
            this.exportFromDate = '';
            this.exportToDate = '';
        } else if (type === 'today') {
            const d = formatDate(today);
            this.exportFromDate = d;
            this.exportToDate = d;
        } else if (type === 'this_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            this.exportFromDate = formatDate(firstDay);
            this.exportToDate = formatDate(today);
        } else if (type === 'last_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
            this.exportFromDate = formatDate(firstDay);
            this.exportToDate = formatDate(lastDay);
        }
    },
    openPhotoPreview(url, title) {
        this.previewPhotoUrl = url;
        this.previewPhotoTitle = title;
        this.previewPhotoOpen = true;
    }
}">

    <!-- 1. COMPACT FINANCIAL SUMMARY (PHONE RESPONSIVE: HORIZONTAL SCROLL ON MOBILE, 5-COLS ON DESKTOP) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none lg:grid lg:grid-cols-5">
        
        <!-- Total -->
        <div class="bg-surface rounded-xl border border-borderMain p-2.5 shadow-2xs shrink-0 w-36 sm:w-auto">
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-textMuted block">Total Billed</span>
            <span class="text-sm sm:text-base font-black text-slate-900 block mt-0.5">₹{{ number_format($totalBilled, 0) }}</span>
            <span class="text-[9.5px] text-primary font-semibold mt-0.5 block truncate">{{ $bills->total() }} Bills</span>
        </div>

        <!-- Cash -->
        <div class="bg-emerald-50/80 rounded-xl border border-emerald-200/80 p-2.5 shadow-2xs shrink-0 w-36 sm:w-auto">
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-emerald-800 block">Cash</span>
            <span class="text-sm sm:text-base font-black text-emerald-900 block mt-0.5">₹{{ number_format($cashCollected, 0) }}</span>
            <span class="text-[9.5px] text-emerald-700 font-semibold mt-0.5 block truncate">Collected</span>
        </div>

        <!-- Credit -->
        <div class="bg-amber-50/80 rounded-xl border border-amber-200/80 p-2.5 shadow-2xs shrink-0 w-36 sm:w-auto">
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-amber-800 block">Credit</span>
            <span class="text-sm sm:text-base font-black text-amber-900 block mt-0.5">₹{{ number_format($creditPending, 0) }}</span>
            <span class="text-[9.5px] text-amber-700 font-semibold mt-0.5 block truncate">Receivables</span>
        </div>

        <!-- GST Bill -->
        <div class="bg-teal-50/80 rounded-xl border border-teal-200/80 p-2.5 shadow-2xs shrink-0 w-36 sm:w-auto">
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-teal-800 block">GST Bill</span>
            <span class="text-sm sm:text-base font-black text-teal-900 block mt-0.5">₹{{ number_format($gstTotal, 0) }}</span>
            <span class="text-[9.5px] text-teal-700 font-semibold mt-0.5 block truncate">Tax Invoices</span>
        </div>

        <!-- Pending Handover -->
        <div class="bg-rose-50/80 rounded-xl border border-rose-200/80 p-2.5 shadow-2xs shrink-0 w-40 sm:w-auto">
            <span class="text-[9.5px] font-bold uppercase tracking-wider text-rose-800 block">Pending Handover</span>
            <span class="text-sm sm:text-base font-black text-rose-900 block mt-0.5">₹{{ number_format($cashPendingHandover, 0) }}</span>
            <span class="text-[9.5px] text-rose-700 font-semibold mt-0.5 block truncate">With Engineers</span>
        </div>

    </div>

    <!-- 2. TOOLBAR (MOBILE OPTIMIZED: SEARCH FULL WIDTH + COMPACT ACTIONS ROW) -->
    <div class="bg-surface rounded-2xl p-2.5 sm:p-3 border border-borderMain shadow-2xs space-y-2 sm:space-y-0 sm:flex sm:items-center sm:justify-between sm:gap-2.5">
        
        <!-- Search input -->
        <form action="{{ route('billing.index') }}" method="GET" class="flex-1 flex items-center gap-1.5">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-2.5 text-textMuted"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search Bill #, Customer, GST #..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
            </div>
            @if(request('bill_type'))
                <input type="hidden" name="bill_type" value="{{ request('bill_type') }}">
            @endif
            @if(request('is_handed') !== null)
                <input type="hidden" name="is_handed" value="{{ request('is_handed') }}">
            @endif
            <button type="submit" class="hidden sm:inline-flex px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-2xs">
                Search
            </button>
        </form>

        <!-- Action Buttons (Responsive flex row on phone) -->
        <div class="flex items-center justify-between sm:justify-end gap-1.5 shrink-0">
            
            <!-- 1. Export (Opens Filter & Date Modal, Admin Only) -->
            @if(auth()->user()->isAdmin())
                <button type="button" 
                        @click="exportModalOpen = true" 
                        class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition active:scale-95" 
                        title="Export Excel / CSV with Date Range Filter">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Export</span>
                </button>
            @endif

            <!-- 2. Filter Button -->
            <button type="button" 
                    @click="filterModalOpen = true" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-xs font-bold text-textMain transition shadow-2xs">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-secondary"></i>
                <span>Filters</span>
                @if(request()->hasAny(['bill_type', 'is_handed']))
                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                @endif
            </button>

            <!-- 3. + New Bill Button -->
            <a href="{{ route('billing.create') }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1 px-3 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-xs shadow-2xs transition shrink-0">
                <i data-lucide="plus" class="w-3.5 h-3.5 text-amber-300"></i>
                <span>+ Bill</span>
            </a>

        </div>

    </div>

    <!-- 3. QUICK FILTER PILLS (SWIPEABLE ROW) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none text-xs">
        <a href="{{ route('billing.index') }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ !request('bill_type') && request('is_handed') === null ? 'bg-primary text-white shadow-2xs' : 'bg-surface border border-borderMain text-textMuted hover:bg-slate-100' }}">
            All Bills ({{ $bills->total() }})
        </a>
        <a href="{{ route('billing.index', array_merge(request()->query(), ['bill_type' => 'cash'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('bill_type') === 'cash' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-emerald-800 hover:bg-emerald-50' }}">
            💵 Cash (₹{{ number_format($cashCollected, 0) }})
        </a>
        <a href="{{ route('billing.index', array_merge(request()->query(), ['bill_type' => 'credit'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('bill_type') === 'credit' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-amber-800 hover:bg-amber-50' }}">
            💳 Credit (₹{{ number_format($creditPending, 0) }})
        </a>
        <a href="{{ route('billing.index', array_merge(request()->query(), ['bill_type' => 'bill'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('bill_type') === 'bill' ? 'bg-teal-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-teal-800 hover:bg-teal-50' }}">
            📄 GST Bill (₹{{ number_format($gstTotal, 0) }})
        </a>
        <a href="{{ route('billing.index', array_merge(request()->query(), ['is_handed' => '0'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('is_handed') === '0' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-rose-700 hover:bg-rose-50' }}">
            ⏳ Pending Handover (₹{{ number_format($cashPendingHandover, 0) }})
        </a>
    </div>

    <!-- ================= 4. MOBILE NATIVE BILL CARDS (MOBILE FIRST, MATCHES PHONE APP) ================= -->
    <div class="md:hidden space-y-3">
        @forelse($bills as $bill)
            <div class="bg-surface rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-2.5">
                <!-- Top Row: Bill #, Pill, Job -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('billing.show', $bill) }}" class="font-black text-xs text-primary font-mono hover:underline">
                            {{ $bill->bill_number }}
                        </a>
                        @if($bill->bill_type == 'cash')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800">CASH</span>
                        @elseif($bill->bill_type == 'credit')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-100 text-amber-900">CREDIT</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-teal-100 text-teal-900">GST INVOICE</span>
                        @endif
                    </div>
                    @if($bill->complaint)
                        <span class="text-[10px] text-textMuted font-mono font-semibold">
                            Job: {{ $bill->complaint->ticket_number }}
                        </span>
                    @endif
                </div>

                <!-- Middle: Amount, Customer, Firm, Collected By -->
                <div class="flex items-start justify-between gap-3 pt-1">
                    <div class="space-y-1 min-w-0 flex-1 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-textMuted uppercase font-bold w-16">Customer</span>
                            <span class="font-bold text-slate-900 truncate">{{ $bill->customer?->name ?? 'Direct Client' }}</span>
                        </div>
                        @if($bill->organization)
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-textMuted uppercase font-bold w-16">Firm</span>
                                <span class="text-textMuted truncate">{{ $bill->organization->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-textMuted uppercase font-bold w-16">Collected By</span>
                            <span class="text-textMuted truncate">{{ $bill->engineer?->name ?? 'Office / Admin' }}</span>
                        </div>
                        @if($bill->gst_bill_number)
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-textMuted uppercase font-bold w-16">GST Inv #</span>
                                <span class="font-mono text-teal-800 font-bold truncate">{{ $bill->gst_bill_number }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Amount -->
                    <div class="text-right shrink-0">
                        <div class="text-[10px] text-textMuted uppercase font-bold">Amount</div>
                        <div class="text-base font-black text-slate-900">₹{{ number_format($bill->amount, 2) }}</div>
                    </div>
                </div>

                <!-- Bottom: Status / Handover + Date + Action Buttons -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        @if($bill->bill_type == 'cash')
                            <div class="flex items-center gap-1 text-[10px] font-bold {{ $bill->is_handed ? 'text-emerald-700' : 'text-rose-600' }}">
                                <i data-lucide="{{ $bill->is_handed ? 'check-circle' : 'clock' }}" class="w-3 h-3"></i>
                                <span>{{ $bill->is_handed ? 'Handed Over' : 'Pending Handover' }}</span>
                            </div>
                        @else
                            <div class="text-[10px] font-bold text-slate-600">
                                {{ strtoupper($bill->payment_status) }}
                            </div>
                        @endif
                        <div class="text-[9.5px] text-slate-400 mt-0.5">
                            {{ $bill->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($bill->photo || $bill->asset_photo)
                            <button type="button" 
                                    @click="openPhotoPreview('{{ asset('storage/' . ($bill->photo ?: $bill->asset_photo)) }}', 'Bill #{{ $bill->bill_number }}')" 
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition"
                                    title="View Photo">
                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                            </button>
                        @endif
                        <a href="{{ route('billing.show', $bill) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] flex items-center gap-1 transition">
                            <span>Details</span>
                            <i data-lucide="arrow-right" class="w-3 h-3 text-slate-500"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface rounded-2xl border border-slate-200 p-8 text-center text-textMuted text-xs">
                <i data-lucide="receipt" class="w-8 h-8 mx-auto text-slate-300 mb-1.5"></i>
                <p class="font-bold text-slate-600">No billing records found.</p>
            </div>
        @endforelse
    </div>

    <!-- ================= 5. EXCEL SHEET STYLE SPREADSHEET TABLE (DESKTOP / LAPTOP) ================= -->
    <!-- Matches client's exact Excel sheet photo: COMP. ID, Customer, Area, Mob No, Problem, Date, Dept, Bill No, Amount, Payment, Collected By, Photo, Action -->
    <div class="hidden md:block bg-surface rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Table Top Banner -->
        <div class="px-3.5 py-2.5 bg-slate-100/90 border-b border-slate-200 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                <span class="text-xs font-black uppercase tracking-wider text-slate-800">
                    Billing Ledger (Exact Excel Sheet Format)
                </span>
            </div>
            <span class="text-[10.5px] text-textMuted font-medium">
                {{ $bills->firstItem() ?? 0 }}-{{ $bills->lastItem() ?? 0 }} of {{ $bills->total() }} entries
            </span>
        </div>

        <!-- Scrollable Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-2.5 px-3 border-r border-slate-200">COMP. ID</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">CUSTOMER</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">AREA</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">MOB NO</th>
                        <th class="py-2.5 px-3 border-r border-slate-200 min-w-[160px]">PROBLEM / REMARK</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">DATE</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">DEPT</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">BILL NO</th>
                        <th class="py-2.5 px-3 border-r border-slate-200 text-right">AMOUNT (₹)</th>
                        <th class="py-2.5 px-3 border-r border-slate-200 text-center">PAYMENT</th>
                        <th class="py-2.5 px-3 border-r border-slate-200">COLLECTED BY</th>
                        <th class="py-2.5 px-3 border-r border-slate-200 text-center">PHOTO</th>
                        <th class="py-2.5 px-2.5 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($bills as $bill)
                        @php
                            $comp = $bill->complaint;
                            $cust = $bill->customer ?: $comp?->customer;
                            $areaName = $cust?->area?->name ?? ($comp?->customer?->area?->name ?? '—');
                            $phone = $cust?->contact_no ?? ($comp?->customer?->contact_no ?? '—');
                            $problemText = $comp?->issue_description ?? ($bill->remarks ?? '—');
                            $deptName = $comp?->productType?->name ?? 'General';
                        @endphp
                        <tr class="hover:bg-slate-50/90 transition group">
                            
                            <!-- 1. COMP. ID -->
                            <td class="py-2.5 px-3 border-r border-slate-100 font-mono font-black text-primary">
                                @if($comp)
                                    <a href="{{ route('complaints.show', $comp) }}" class="hover:underline">
                                        {{ $comp->ticket_number }}
                                    </a>
                                @else
                                    <span class="text-slate-300 font-normal">—</span>
                                @endif
                            </td>

                            <!-- 2. Customer -->
                            <td class="py-2.5 px-3 border-r border-slate-100 font-bold text-slate-900 max-w-[170px] truncate" title="{{ $cust?->name }}">
                                {{ $cust?->name ?? 'Direct Client' }}
                                @if($bill->organization)
                                    <span class="text-[9.5px] text-slate-500 font-normal block truncate">({{ $bill->organization->name }})</span>
                                @elseif($cust?->organization)
                                    <span class="text-[9.5px] text-slate-500 font-normal block truncate">({{ $cust->organization->name }})</span>
                                @endif
                            </td>

                            <!-- 3. Area -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-slate-600 max-w-[130px] truncate" title="{{ $areaName }}">
                                {{ $areaName }}
                            </td>

                            <!-- 4. Mob No -->
                            <td class="py-2.5 px-3 border-r border-slate-100 font-mono font-bold text-slate-800">
                                @if($phone !== '—')
                                    <a href="tel:{{ $phone }}" class="hover:text-primary hover:underline">
                                        {{ $phone }}
                                    </a>
                                @else
                                    <span class="text-slate-300 font-normal">—</span>
                                @endif
                            </td>

                            <!-- 5. Problem / Remark -->
                            <td class="py-2.5 px-3 border-r border-slate-100 max-w-[180px] truncate text-slate-700" title="{{ $problemText }}">
                                {{ $problemText }}
                            </td>

                            <!-- 6. Date -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-slate-700">
                                <div class="font-bold text-[11px] leading-tight">{{ $bill->created_at->format('d/m/Y') }}</div>
                                <div class="text-[9.5px] text-slate-400 font-medium">{{ $bill->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- 7. Department -->
                            <td class="py-2.5 px-3 border-r border-slate-100">
                                <span class="px-1.5 py-0.5 rounded text-[9.5px] font-black uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $deptName }}
                                </span>
                            </td>

                            <!-- 8. Bill No -->
                            <td class="py-2.5 px-3 border-r border-slate-100">
                                <a href="{{ route('billing.show', $bill) }}" class="font-extrabold text-primary hover:underline block leading-tight">
                                    {{ $bill->bill_number }}
                                </a>
                                @if($bill->gst_bill_number)
                                    <span class="text-[9px] font-mono text-teal-700 font-bold block">GST: {{ $bill->gst_bill_number }}</span>
                                @endif
                            </td>

                            <!-- 9. Amount -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-right">
                                <span class="font-black text-slate-900 text-sm">₹{{ number_format($bill->amount, 2) }}</span>
                            </td>

                            <!-- 10. Payment -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-center">
                                @if($bill->bill_type == 'cash')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        💵 CASH
                                    </span>
                                @elseif($bill->bill_type == 'credit')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                        💳 CREDIT
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-black bg-teal-100 text-teal-900 border border-teal-300">
                                        📄 GST BILL
                                    </span>
                                @endif
                            </td>

                            <!-- 11. Collected By -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-slate-700 text-[11px] font-semibold truncate max-w-[120px]">
                                {{ $bill->engineer?->name ?? '—' }}
                            </td>

                            <!-- 12. Photo -->
                            <td class="py-2.5 px-3 border-r border-slate-100 text-center">
                                @if($bill->photo)
                                    <button type="button" 
                                            @click="openPhotoPreview('{{ asset('storage/' . $bill->photo) }}', 'Bill #{{ $bill->bill_number }} - Photo')" 
                                            class="inline-block relative group/thumb">
                                        <img src="{{ asset('storage/' . $bill->photo) }}" 
                                             alt="Receipt" 
                                             class="w-7 h-7 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover/thumb:ring-2 group-hover/thumb:ring-primary transition">
                                    </button>
                                @elseif($bill->asset_photo)
                                    <button type="button" 
                                            @click="openPhotoPreview('{{ asset('storage/' . $bill->asset_photo) }}', 'Bill #{{ $bill->bill_number }} - Asset Photo')" 
                                            class="inline-block relative group/thumb">
                                        <img src="{{ asset('storage/' . $bill->asset_photo) }}" 
                                             alt="Asset Receipt" 
                                             class="w-7 h-7 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover/thumb:ring-2 group-hover/thumb:ring-primary transition">
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-300 italic">—</span>
                                @endif
                            </td>

                            <!-- 13. Action -->
                            <td class="py-2.5 px-2.5 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('billing.pdf', $bill) }}" title="Download PDF" class="p-1.5 rounded-lg text-slate-400 hover:text-primary hover:bg-slate-100 transition">
                                        <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('billing.show', $bill) }}" title="View Details" class="p-1.5 rounded-lg text-secondary hover:bg-blue-50 transition">
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-12 text-center text-textMuted text-xs">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto text-slate-300 mb-1.5"></i>
                                <p class="font-bold text-slate-600">No billing records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- 5. PAGINATION -->
    @if($bills->hasPages())
        <div class="bg-surface rounded-2xl border border-borderMain p-3">
            {{ $bills->links() }}
        </div>
    @endif

    <!-- 6. PHOTO PREVIEW LIGHTBOX MODAL -->
    <div x-show="previewPhotoOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="previewPhotoOpen = false"></div>

        <!-- Dialog -->
        <div class="relative max-w-lg w-full bg-slate-900 rounded-3xl shadow-2xl overflow-hidden z-50 p-4 border border-slate-700">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-800 text-white">
                <div>
                    <h3 class="font-bold text-xs sm:text-sm truncate" x-text="previewPhotoTitle"></h3>
                    <p class="text-[10px] text-slate-400">Bill Proof / Photo Receipt</p>
                </div>
                <div class="flex items-center space-x-1.5">
                    <a :href="previewPhotoUrl" target="_blank" download class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Open Full Size">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                    <button type="button" @click="previewPhotoOpen = false" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

            <!-- Image View -->
            <div class="mt-2.5 flex items-center justify-center bg-black/60 rounded-xl overflow-hidden max-h-[70vh]">
                <img :src="previewPhotoUrl" alt="Bill Photo" class="max-h-[65vh] w-auto object-contain">
            </div>
        </div>
    </div>

    <!-- 7. FILTER MODAL -->
    <div x-show="filterModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="relative w-full max-w-md bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 z-50 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3.5 border-b border-borderMain">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-textMain">Filter Bills</h3>
                        <p class="text-[11px] text-textMuted">Filter by payment mode & handover</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('billing.index') }}" method="GET" class="mt-4 space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Bill #, customer, GST..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                    </div>
                </div>

                <!-- Date Range Filters -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">From Date (कब से)</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-2.5 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">To Date (कब तक का)</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-2.5 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Payment Mode</label>
                    <select name="bill_type" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Payment Modes</option>
                        <option value="cash" {{ request('bill_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ request('bill_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="bill" {{ request('bill_type') == 'bill' ? 'selected' : '' }}>GST Tax Bill</option>
                    </select>
                </div>

                @if(isset($engineers) && $engineers->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Field Engineer</label>
                    <select name="engineer_id" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Engineers</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Handover Status</label>
                    <select name="is_handed" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_handed') === '1' ? 'selected' : '' }}>Verified Handover</option>
                        <option value="0" {{ request('is_handed') === '0' ? 'selected' : '' }}>Pending Handover</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-borderMain gap-3">
                    <a href="{{ route('billing.index') }}" class="px-3.5 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
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

    <!-- 8. EXPORT FILTER MODAL (ADMIN ONLY) -->
    @if(auth()->user()->isAdmin())
    <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="exportModalOpen = false"></div>

        <div class="relative w-full max-w-lg bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 sm:p-6 z-50 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3.5 border-b border-borderMain">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold shadow-2xs border border-emerald-200">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-textMain">Export Billing Ledger</h3>
                        <p class="text-[11px] text-textMuted">Excel / CSV export with date range & custom filters</p>
                    </div>
                </div>
                <button type="button" @click="exportModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('billing.export') }}" method="GET" class="mt-4 space-y-4">
                
                <!-- Quick Date Range Presets -->
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1.5">
                        Quick Date Presets / त्वरित अवधि
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-xs">
                        <button type="button" 
                                @click="setExportPreset('all')" 
                                class="py-1.5 px-2 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-slate-800 font-bold transition text-center text-[11px]">
                            All Records (सारा)
                        </button>
                        <button type="button" 
                                @click="setExportPreset('today')" 
                                class="py-1.5 px-2 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold transition text-center text-[11px]">
                            Today (आज)
                        </button>
                        <button type="button" 
                                @click="setExportPreset('this_month')" 
                                class="py-1.5 px-2 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold transition text-center text-[11px]">
                            This Month (इस माह)
                        </button>
                        <button type="button" 
                                @click="setExportPreset('last_month')" 
                                class="py-1.5 px-2 rounded-xl border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold transition text-center text-[11px]">
                            Last Month (पिछला)
                        </button>
                    </div>
                </div>

                <!-- Custom Date Range (Kab se & Kab tak ka bill chahiye) -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800">Date Range / कब तक का बिल चाहिए</span>
                        <span class="text-[10px] text-textMuted">Optional filter</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                                From Date (कब से)
                            </label>
                            <input type="date" 
                                   name="from_date" 
                                   x-model="exportFromDate" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                                To Date (कब तक का / Target Date)
                            </label>
                            <input type="date" 
                                   name="to_date" 
                                   x-model="exportToDate" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white outline-none focus:border-primary">
                        </div>
                    </div>
                    <p class="text-[10.5px] text-slate-500">
                        Leave blank to export all time records, or pick "To Date" to export bills up to that day.
                    </p>
                </div>

                <!-- Payment Type Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">
                        Payment Type (भुगतान का प्रकार)
                    </label>
                    <select name="bill_type" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Payment Types (Cash, Credit & GST)</option>
                        <option value="cash">Cash Only</option>
                        <option value="credit">Credit Only</option>
                        <option value="bill">GST Tax Invoices Only</option>
                    </select>
                </div>

                <!-- Field Engineer Filter -->
                @if(isset($engineers) && $engineers->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">
                        Field Engineer (इंजीनियर)
                    </label>
                    <select name="engineer_id" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Field Engineers</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">
                                {{ $eng->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Handover Status Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">
                        Cash Handover Status
                    </label>
                    <select name="is_handed" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Statuses</option>
                        <option value="1">Verified Handed Over</option>
                        <option value="0">Pending Handover</option>
                    </select>
                </div>

                <!-- Excel Formatting Notice -->
                <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-emerald-900 text-[11px] flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>Exported format: <strong>COMPLAINT ID, CUSTOMER NAME, AREA, MOBILE NUMBER, PROBLEM, DATE (DD-MM-YYYY)</strong>.</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-3 border-t border-borderMain gap-3">
                    <button type="button" @click="exportModalOpen = false" class="px-4 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMain hover:bg-bg transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            @click="setTimeout(() => { exportModalOpen = false; }, 800)"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md transition flex items-center space-x-2 active:scale-95">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Export File (CSV / Excel)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
