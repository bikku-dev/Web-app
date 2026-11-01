@extends('layouts.app')

@section('title', 'Ticket #' . $complaint->ticket_number . ' | Bhumi Technocare')
@section('page_title', 'Service Ticket Details')

@section('content')
<div class="w-full space-y-6" x-data="{ 
    expensePanelOpen: false, 
    resolveModalOpen: false,
    lightboxOpen: false,
    lightboxImage: '',
    lightboxTitle: ''
}">

    <!-- BREADCRUMB & BACK NAV -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2">
            <a href="{{ route('complaints.index') }}" class="inline-flex items-center space-x-1.5 font-bold text-secondary hover:text-primary transition py-1 px-2.5 rounded-lg hover:bg-slate-100">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>All Service Tickets</span>
            </a>
            <span class="text-textMuted">/</span>
            <span class="font-extrabold text-primary font-mono">{{ $complaint->ticket_number }}</span>
        </div>
        <div class="text-textMuted flex items-center space-x-2">
            <span class="inline-flex items-center space-x-1">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Created {{ $complaint->created_at->format('d M Y, h:i A') }} ({{ $complaint->created_at->diffForHumans() }})</span>
            </span>
        </div>
    </div>

    <!-- TOP HERO BANNER & PRIMARY ACTIONS -->
    <div class="bg-surface rounded-3xl p-6 sm:p-7 border border-borderMain shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            <!-- Left: Ticket Meta & Badges -->
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-xl bg-primary text-white font-black text-sm tracking-wide shadow-sm font-mono">
                        #{{ $complaint->ticket_number }}
                    </span>

                    <!-- STATUS BADGE -->
                    @if($complaint->isOverdue())
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                            <span>OVERDUE</span>
                        </span>
                    @elseif($complaint->status === 'completed')
                        <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>RESOLVED & BILLED</span>
                        </span>
                    @elseif($complaint->status === 'in_progress')
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <span>IN PROGRESS</span>
                        </span>
                    @elseif($complaint->status === 'cancelled')
                        <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            <span>CANCELLED</span>
                        </span>
                    @else
                        <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 whitespace-nowrap">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>PENDING</span>
                        </span>
                    @endif

                    <!-- PRIORITY BADGE -->
                    @php
                        $priorityClass = match($complaint->priority) {
                            'urgent' => 'bg-red-50 text-red-700 border-red-200',
                            'high' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'medium' => 'bg-blue-50 text-blue-700 border-blue-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wider border {{ $priorityClass }} flex items-center space-x-1">
                        @if($complaint->priority === 'urgent')
                            <i data-lucide="flame" class="w-3 h-3 text-red-600"></i>
                        @endif
                        <span>Priority: {{ $complaint->priority }}</span>
                    </span>
                </div>

                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-textMain tracking-tight">
                        {{ $complaint->productType?->name ?? 'Equipment Service' }}
                        <span class="text-textMuted font-normal text-base sm:text-lg">
                            • {{ $complaint->serviceType?->name ?? 'General Maintenance' }}
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-textMuted mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span><strong>Client:</strong> {{ $complaint->customer?->name }}</span>
                        @if($complaint->organization)
                            <span><strong>Firm:</strong> {{ $complaint->organization->name }}</span>
                        @endif
                        <span><strong>Assigned:</strong> {{ $complaint->engineer?->name ?? 'Unassigned' }}</span>
                    </p>
                </div>
            </div>

            <!-- Right: Action Buttons Group -->
            <div class="flex flex-wrap items-center gap-2.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-borderMain">
                
                <a href="{{ route('complaints.pdf', $complaint) }}" class="px-4 py-3 rounded-2xl bg-primary text-white hover:bg-slate-800 font-bold text-xs shadow-md transition flex items-center space-x-1.5 active:scale-95">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Job Sheet PDF</span>
                </a>
                <a href="{{ route('complaints.print', $complaint) }}" target="_blank" class="px-3.5 py-3 rounded-2xl border border-borderMain bg-surface hover:bg-slate-100 text-textMain font-bold text-xs shadow-sm transition flex items-center space-x-1.5 active:scale-95">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Print</span>
                </a>

                <!-- START SERVICE CALL -->
                @if(!$complaint->is_started && $complaint->status !== 'cancelled')
                    <form action="{{ route('complaints.start', $complaint) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md hover:shadow-lg transition flex items-center space-x-2 active:scale-95">
                            <i data-lucide="play" class="w-4 h-4 fill-white"></i>
                            <span>Start Service Call</span>
                        </button>
                    </form>
                @endif

                <!-- GENERATE BILL & RESOLVE (If Started & Not Resolved) -->
                @if($complaint->is_started && !$complaint->is_resolved && $complaint->status !== 'cancelled')
                    <a href="{{ route('billing.create', ['complaint_id' => $complaint->id]) }}" class="px-5 py-3 rounded-2xl bg-accent hover:bg-amber-500 text-primary font-black text-xs shadow-md hover:shadow-lg transition flex items-center space-x-2 active:scale-95">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Generate Bill & Resolve</span>
                    </a>

                    <!-- DIRECT RESOLUTION (No Bill / Warranty) -->
                    <button type="button" @click="resolveModalOpen = true" class="px-4 py-3 rounded-2xl bg-surface border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-bold text-xs shadow-sm transition flex items-center space-x-1.5 active:scale-95">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        <span>Mark Resolved</span>
                    </button>
                @endif

                <!-- CLAIM JOB EXPENSE -->
                <button type="button" @click="expensePanelOpen = !expensePanelOpen" class="px-4 py-3 rounded-2xl bg-secondary hover:bg-secondary/90 text-white font-bold text-xs shadow-md transition flex items-center space-x-2 active:scale-95">
                    <i data-lucide="receipt-text" class="w-4 h-4"></i>
                    <span x-text="expensePanelOpen ? 'Hide Expense Form' : 'Claim Job Expense'">Claim Job Expense</span>
                </button>

                <!-- EDIT TICKET (Admin Only) -->
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('complaints.edit', $complaint) }}" class="px-4 py-3 rounded-2xl border border-borderMain bg-surface hover:bg-bg text-textMain font-bold text-xs shadow-sm transition flex items-center space-x-1.5 active:scale-95">
                        <i data-lucide="edit-3" class="w-4 h-4 text-secondary"></i>
                        <span>Edit Ticket</span>
                    </a>
                @endif

                <!-- CANCEL TICKET (Admin Only) -->
                @if(auth()->user()->isAdmin() && $complaint->status !== 'cancelled' && $complaint->status !== 'completed')
                    <form action="{{ route('complaints.cancel', $complaint) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this complaint ticket?')">
                        @csrf
                        <button type="submit" class="px-3.5 py-3 rounded-2xl border border-rose-300 text-rose-600 hover:bg-rose-50 font-bold text-xs transition">
                            Cancel Ticket
                        </button>
                    </form>
                @endif
            </div>

        </div>

        <!-- SERVICE LIFECYCLE MILESTONES PROGRESS TRACKER -->
        <div class="mt-6 pt-6 border-t border-borderMain">
            <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-textMuted mb-3">Service Lifecycle Progression</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                
                <!-- STEP 1: LOGGED -->
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <span class="text-xs font-extrabold text-emerald-900">1. Logged</span>
                    </div>
                    <p class="text-[11px] text-emerald-800 font-medium mt-1 pl-8">
                        {{ $complaint->created_at->format('d M, h:i A') }}
                    </p>
                </div>

                <!-- STEP 2: STARTED -->
                @if($complaint->is_started)
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-900">2. Started</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 font-medium mt-1 pl-8">
                            {{ $complaint->started_at?->format('d M, h:i A') ?? 'In Progress' }}
                        </p>
                    </div>
                @else
                    <div class="p-3.5 rounded-2xl bg-bg border border-borderMain opacity-75">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">
                                2
                            </div>
                            <span class="text-xs font-bold text-textMuted">2. Start Call</span>
                        </div>
                        <p class="text-[11px] text-textMuted mt-1 pl-8">Pending Start</p>
                    </div>
                @endif

                <!-- STEP 3: RESOLVED -->
                @if($complaint->is_resolved)
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-900">3. Resolved</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 font-medium mt-1 pl-8">
                            {{ $complaint->resolved_at?->format('d M, h:i A') ?? 'Completed' }}
                        </p>
                    </div>
                @else
                    <div class="p-3.5 rounded-2xl bg-bg border border-borderMain opacity-75">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">
                                3
                            </div>
                            <span class="text-xs font-bold text-textMuted">3. Resolution</span>
                        </div>
                        <p class="text-[11px] text-textMuted mt-1 pl-8">Awaiting Fix</p>
                    </div>
                @endif

                <!-- STEP 4: BILLED / SETTLED -->
                @if($complaint->bill)
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-900">4. Billed</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 font-medium mt-1 pl-8">
                            ₹{{ number_format($complaint->bill->amount, 2) }}
                        </p>
                    </div>
                @else
                    <div class="p-3.5 rounded-2xl bg-bg border border-borderMain opacity-75">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">
                                4
                            </div>
                            <span class="text-xs font-bold text-textMuted">4. Billing</span>
                        </div>
                        <p class="text-[11px] text-textMuted mt-1 pl-8">Not Billed Yet</p>
                    </div>
                @endif

            </div>
        </div>

    </div>

    <!-- MAIN TWO COLUMN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT 2 COLUMNS: Ticket Details & Field Activity -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Problem & Breakdown Description Card -->
            <div class="bg-surface rounded-3xl p-6 sm:p-7 border border-borderMain shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-borderMain pb-3">
                    <h3 class="font-black text-sm text-textMain flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-secondary flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <span>Breakdown & Reported Issue Description</span>
                    </h3>
                    <span class="text-xs font-bold text-textMuted">
                        Due: {{ $complaint->due_date?->format('d M Y, h:i A') }}
                    </span>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl bg-bg border border-borderMain text-textMain text-sm sm:text-base leading-relaxed font-medium">
                    @linkify($complaint->issue_description)
                </div>

                <!-- Initial Fault Photo -->
                @if($complaint->photo)
                    <div class="pt-2">
                        <div class="text-xs font-extrabold text-textMuted uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                            <i data-lucide="camera" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Attached Fault Photo / Machine Snapshot</span>
                        </div>
                        <div class="relative group max-w-md rounded-2xl overflow-hidden border border-borderMain shadow-sm bg-black/5 cursor-pointer"
                             @click="lightboxImage = '{{ asset('storage/' . $complaint->photo) }}'; lightboxTitle = 'Machine Fault Photo (Ticket #{{ $complaint->ticket_number }})'; lightboxOpen = true">
                            <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Machine Fault Photo" class="w-full h-52 object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white font-bold text-xs gap-1.5">
                                <i data-lucide="zoom-in" class="w-4 h-4"></i>
                                <span>Click to View Fullscreen</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Resolution Summary (If notes exist) -->
                @if($complaint->resolution_notes)
                    <div class="pt-4 border-t border-borderMain">
                        <div class="text-xs font-black text-emerald-800 uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                            <i data-lucide="check-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Resolution Notes & Work Completed</span>
                        </div>
                        <div class="text-xs sm:text-sm text-textMain leading-relaxed bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200">
                            @linkify($complaint->resolution_notes)
                        </div>
                    </div>
                @endif
            </div>

            <!-- Linked Bill & Invoicing Record -->
            <div class="bg-surface rounded-3xl p-6 sm:p-7 border border-borderMain shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-borderMain mb-4">
                    <h3 class="font-black text-sm text-textMain flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <span>Billing & Payment Record</span>
                    </h3>
                    @if(!$complaint->bill && $complaint->is_started && $complaint->status !== 'cancelled')
                        <a href="{{ route('billing.create', ['complaint_id' => $complaint->id]) }}" class="text-xs text-secondary font-extrabold hover:underline flex items-center space-x-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Generate Bill</span>
                        </a>
                    @endif
                </div>

                @if($complaint->bill)
                    <div class="p-5 rounded-2xl bg-bg border border-borderMain flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono font-black text-base text-primary">{{ $complaint->bill->bill_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold uppercase {{ $complaint->bill->bill_type == 'cash' ? 'bg-emerald-100 text-emerald-800' : ($complaint->bill->bill_type == 'credit' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $complaint->bill->bill_type == 'bill' ? 'GST Invoice' : $complaint->bill->bill_type }}
                                </span>
                                @if($complaint->bill->is_handed)
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Handover Verified</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 flex items-center space-x-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span>Handover Pending</span>
                                    </span>
                                @endif
                            </div>
                            <div class="text-2xl font-black text-textMain tracking-tight">₹{{ number_format($complaint->bill->amount, 2) }}</div>
                            <p class="text-xs text-textMuted">
                                Recorded on {{ $complaint->bill->created_at->format('d M Y, h:i A') }} by {{ $complaint->bill->engineer?->name ?? 'Staff' }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                            <a href="{{ route('billing.pdf', $complaint->bill) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-borderMain bg-surface hover:bg-slate-100 text-textMain text-xs font-bold transition shadow-sm text-center flex items-center justify-center space-x-1.5">
                                <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                                <span>Invoice PDF</span>
                            </a>
                            <a href="{{ route('billing.show', $complaint->bill) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/95 text-white text-xs font-bold transition shadow-sm text-center flex items-center justify-center space-x-1.5 active:scale-95">
                                <span>View Full Invoice</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center rounded-2xl bg-bg border border-borderMain space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-textMuted mx-auto flex items-center justify-center">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs font-bold text-textMain">No billing record generated yet</p>
                        <p class="text-xs text-textMuted max-w-md mx-auto">
                            The service invoice and cash collection entry is generated on-site once maintenance work is initiated or finalized.
                        </p>
                        @if($complaint->is_started && $complaint->status !== 'cancelled')
                            <div class="pt-2">
                                <a href="{{ route('billing.create', ['complaint_id' => $complaint->id]) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-accent hover:bg-amber-500 text-primary font-black text-xs shadow-sm transition">
                                    <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                    <span>Create Bill Now</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Field Expenses Claimed & Inline Claim Panel -->
            <div class="bg-surface rounded-3xl p-6 sm:p-7 border border-borderMain shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-borderMain">
                    <div>
                        <h3 class="font-black text-sm text-textMain flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-secondary flex items-center justify-center">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                            </div>
                            <span>Field Expenses Claimed on this Job</span>
                        </h3>
                        @if($complaint->expenses->count() > 0)
                            <p class="text-xs text-textMuted mt-0.5">
                                Total Claimed: <strong class="text-primary font-extrabold">₹{{ number_format($complaint->expenses->sum('amount'), 2) }}</strong> ({{ $complaint->expenses->count() }} claims)
                            </p>
                        @endif
                    </div>

                    <button type="button" @click="expensePanelOpen = !expensePanelOpen" class="px-3.5 py-2 rounded-xl bg-secondary/10 hover:bg-secondary/20 text-secondary font-extrabold text-xs transition flex items-center space-x-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span x-text="expensePanelOpen ? 'Close Form' : 'Claim Expense'">Claim Expense</span>
                    </button>
                </div>

                <!-- INLINE EXPENSE CLAIM FORM -->
                <div x-show="expensePanelOpen" x-cloak x-transition class="p-5 rounded-2xl bg-blue-50/50 border-2 border-secondary shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-blue-200/60 pb-3">
                        <div>
                            <h4 class="font-black text-xs text-primary flex items-center gap-1.5">
                                <i data-lucide="receipt-text" class="w-4 h-4 text-secondary"></i>
                                <span>Submit On-Site Expense for Ticket #{{ $complaint->ticket_number }}</span>
                            </h4>
                            <p class="text-[11px] text-textMuted">Record travel, spare parts, or component purchase costs directly linked to this service call.</p>
                        </div>
                        <button type="button" @click="expensePanelOpen = false" class="text-textMuted hover:text-textMain p-1">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        @csrf
                        <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">

                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1">Expense Type <span class="text-rose-600">*</span></label>
                            <select name="expense_type" required class="w-full text-xs font-semibold rounded-xl border border-borderMain bg-surface focus:ring-2 focus:ring-secondary py-2.5 px-3">
                                <option value="Travel">Travel / Fuel / Auto / Toll</option>
                                <option value="Component">Spare Parts / Hardware / Fitting</option>
                                <option value="Other">Other Miscellaneous</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1">Amount (₹) <span class="text-rose-600">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-textMuted">₹</span>
                                <input type="number" step="0.01" name="amount" required placeholder="350.00" class="w-full text-xs font-bold rounded-xl border border-borderMain bg-surface focus:ring-2 focus:ring-secondary py-2.5 pl-7 pr-3">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-textMain mb-1">Receipt / Bill Photo (Optional)</label>
                            <input type="file" name="photo" accept="image/*" class="w-full text-xs text-textMuted file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-textMain mb-1">Expense Description & Purpose <span class="text-rose-600">*</span></label>
                            <textarea name="details" rows="2" required placeholder="Describe what was purchased or travel route taken..." class="w-full text-xs rounded-xl border border-borderMain bg-surface focus:ring-2 focus:ring-secondary p-3"></textarea>
                        </div>

                        <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-blue-200/60">
                            <button type="button" @click="expensePanelOpen = false" class="px-4 py-2 rounded-xl border border-borderMain text-xs font-bold text-textMuted hover:bg-surface">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-extrabold shadow-sm flex items-center gap-1.5 active:scale-95 transition">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Submit Expense Claim</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- LIST OF CLAIMED EXPENSES -->
                <div class="space-y-3">
                    @forelse($complaint->expenses as $exp)
                        <div class="p-4 rounded-2xl bg-bg border border-borderMain flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-extrabold uppercase text-[11px] px-2.5 py-0.5 rounded-lg bg-slate-200 text-slate-800">
                                        {{ $exp->expense_type }}
                                    </span>
                                    <span class="font-black text-sm text-textMain">₹{{ number_format($exp->amount, 2) }}</span>
                                    <span class="text-[11px] text-textMuted">by {{ $exp->engineer?->name ?? 'Engineer' }}</span>
                                </div>
                                <p class="text-textMain font-medium">{{ $exp->details }}</p>
                                <span class="text-[10px] text-textMuted">{{ $exp->created_at->format('d M Y, h:i A') }}</span>
                            </div>

                            <div class="flex items-center space-x-2 self-start sm:self-center">
                                @if($exp->photo)
                                    <button type="button" @click="lightboxImage = '{{ asset('storage/' . $exp->photo) }}'; lightboxTitle = 'Expense Receipt (₹{{ number_format($exp->amount, 2) }})'; lightboxOpen = true"
                                            class="px-2.5 py-1 rounded-lg bg-surface border border-borderMain text-secondary font-bold text-[11px] hover:bg-blue-50 flex items-center space-x-1">
                                        <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                        <span>Receipt</span>
                                    </button>
                                @endif

                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $exp->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($exp->status === 'declined' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $exp->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-bg rounded-2xl border border-dashed border-borderMain text-xs text-textMuted space-y-1">
                            <i data-lucide="receipt" class="w-6 h-6 mx-auto text-textMuted opacity-60"></i>
                            <p class="font-bold text-textMain">No expense claims filed for this job yet.</p>
                            <p class="text-[11px]">Click 'Claim Expense' above to log transport or spare component charges.</p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

        <!-- RIGHT COLUMN: Customer, Equipment & Engineer Meta Cards -->
        <div class="space-y-6">

            <!-- Customer & Plant Location Card -->
            <div class="bg-surface rounded-3xl p-6 border border-borderMain shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-borderMain pb-3">
                    <h4 class="font-black text-xs text-textMuted uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rose-500"></i>
                        <span>Client & Machine Location</span>
                    </h4>
                    @if($complaint->customer)
                        <a href="{{ route('customers.show', $complaint->customer) }}" class="text-[11px] font-bold text-secondary hover:underline">
                            Profile →
                        </a>
                    @endif
                </div>

                <!-- Customer Name & Org -->
                <div>
                    <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider block">Customer Name</span>
                    <div class="font-black text-base text-textMain mt-0.5">{{ $complaint->customer?->name ?? 'Walk-in Client' }}</div>
                    
                    <div class="mt-2 pt-2 border-t border-borderMain">
                        <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider block">Company / Firm</span>
                        <div class="font-bold text-sm text-textMain mt-0.5">{{ $complaint->organization?->name ?? 'Individual / Walk-in' }}</div>
                        @if($complaint->organization?->gst_no)
                            <div class="text-[11px] text-textMuted font-mono mt-0.5">GST: {{ $complaint->organization->gst_no }}</div>
                        @endif
                    </div>
                </div>

                <!-- Quick Action Buttons for On-Site Engineer -->
                @if($complaint->customer?->contact_no)
                    @php
                        $rawShowPhone = preg_replace('/[^0-9]/', '', $complaint->customer->contact_no);
                        $waShowPhone = strlen($rawShowPhone) === 10 ? '91' . $rawShowPhone : $rawShowPhone;
                    @endphp
                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <a href="tel:{{ $complaint->customer->contact_no }}" class="py-2.5 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition" title="Call Client">
                            <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                            <span>Call</span>
                        </a>

                        <!-- Regular WhatsApp -->
                        <a href="https://web.whatsapp.com/send?phone={{ $waShowPhone }}" 
                           onclick="window.openWhatsAppDirect(event, '{{ $waShowPhone }}', 'regular')" 
                           target="_blank" 
                           class="py-2.5 px-2 rounded-xl bg-[#25D366] hover:bg-[#20ba59] text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition" 
                           title="WhatsApp (Personal)">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>WhatsApp</span>
                        </a>

                        <!-- WhatsApp Business -->
                        <a href="https://wa.me/{{ $waShowPhone }}" 
                           onclick="window.openWhatsAppDirect(event, '{{ $waShowPhone }}', 'business')" 
                           target="_blank" 
                           class="py-2.5 px-2 rounded-xl bg-[#128C7E] hover:bg-[#0e6f63] text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition" 
                           title="WhatsApp Business (Direct App)">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                <text x="12" y="14.5" font-size="8" font-weight="900" text-anchor="middle" fill="currentColor" stroke="none" font-family="system-ui, -apple-system, sans-serif">B</text>
                            </svg>
                            <span>WA Business</span>
                        </a>
                    </div>
                @endif

                <!-- Workshop Address & Maps Link -->
                @php
                    $address = $complaint->customer?->address ?? $complaint->organization?->address ?? '';
                @endphp
                <div class="pt-2 border-t border-borderMain">
                    <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider block">Workshop / Factory Address</span>
                    <p class="text-xs text-textMain font-medium leading-relaxed mt-1 bg-bg p-3 rounded-xl border border-borderMain">
                        {{ !empty($address) ? $address : 'Surat, Gujarat, India' }}
                    </p>

                    @if(!empty($address) || $complaint->customer?->name)
                        <div class="mt-2.5">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($address ?: 'Surat Gujarat') . ' ' . ($complaint->organization?->name ?? '')) }}" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-secondary hover:bg-secondary/90 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition">
                                <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                                <span>Navigate on Google Maps</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Machinery & Service Master Card -->
            <div class="bg-surface rounded-3xl p-6 border border-borderMain shadow-sm space-y-3.5">
                <h4 class="font-black text-xs text-textMuted uppercase tracking-wider pb-2 border-b border-borderMain flex items-center space-x-1.5">
                    <i data-lucide="cpu" class="w-4 h-4 text-primary"></i>
                    <span>Machinery & Service Master</span>
                </h4>

                <div class="flex items-start space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-textMuted font-bold uppercase tracking-wider block">Equipment / Product</span>
                        <div class="font-black text-sm text-primary">{{ $complaint->productType?->name ?? 'General Machine' }}</div>
                    </div>
                </div>

                <div class="flex items-start space-x-3 pt-2 border-t border-borderMain">
                    <div class="w-9 h-9 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <i data-lucide="check-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-textMuted font-bold uppercase tracking-wider block">Service Category</span>
                        <div class="font-bold text-sm text-secondary">{{ $complaint->serviceType?->name ?? 'Maintenance' }}</div>
                    </div>
                </div>

                <div class="flex items-start space-x-3 pt-2 border-t border-borderMain">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-textMuted font-bold uppercase tracking-wider block">Target Due Date</span>
                        <div class="font-bold text-xs {{ $complaint->isOverdue() ? 'text-rose-600 font-extrabold' : 'text-textMain' }}">
                            {{ $complaint->due_date?->format('d M Y, h:i A') }}
                            @if($complaint->isOverdue())
                                <span class="block text-[11px] text-rose-500 font-bold">(Overdue by {{ $complaint->due_date->diffForHumans(null, true) }})</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Field Staff Card -->
            <div class="bg-surface rounded-3xl p-6 border border-borderMain shadow-sm space-y-4">
                <h4 class="font-black text-xs text-textMuted uppercase tracking-wider pb-2 border-b border-borderMain flex items-center space-x-1.5">
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>Assigned Field Engineer</span>
                </h4>

                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-primary text-white font-black flex items-center justify-center text-base shadow-sm">
                        {{ substr($complaint->engineer?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-black text-sm text-textMain">{{ $complaint->engineer?->name ?? 'Not Assigned' }}</div>
                        <div class="text-xs text-textMuted">{{ $complaint->engineer?->post ?? 'Field Service Engineer' }}</div>
                    </div>
                </div>

                @if($complaint->engineer?->phone_no)
                    <div class="pt-2 border-t border-borderMain flex items-center justify-between text-xs">
                        <div>
                            <span class="text-textMuted font-bold text-[11px] block">Engineer Phone:</span>
                            <strong class="text-textMain font-mono">{{ $complaint->engineer->phone_no }}</strong>
                        </div>
                        <a href="tel:{{ $complaint->engineer->phone_no }}" class="py-1.5 px-3 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs flex items-center space-x-1 transition">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                            <span>Call</span>
                        </a>
                    </div>
                @endif
            </div>

    </div>

    <!-- BOTTOM ACTIONS BAR (Admin Edit & Back to Tickets) -->
    <div class="bg-surface rounded-2xl p-4 border border-borderMain shadow-sm flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('complaints.index') }}" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-bold text-textMain hover:bg-bg transition">
            &larr; Back to Service Tickets
        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('complaints.edit', $complaint) }}" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center space-x-1.5 active:scale-95">
                <i data-lucide="edit-3" class="w-4 h-4 text-accent"></i>
                <span>Edit Ticket Details</span>
            </a>
        @endif
    </div>

    <!-- RESOLUTION MODAL -->
    <div x-show="resolveModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-surface rounded-3xl p-6 sm:p-7 max-w-lg w-full border border-borderMain shadow-2xl space-y-4" @click.away="resolveModalOpen = false">
            <div class="flex items-center justify-between border-b border-borderMain pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="check-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm text-textMain">Complete & Resolve Service Ticket</h3>
                        <p class="text-[11px] text-textMuted">Record work completed and technical observations</p>
                    </div>
                </div>
                <button type="button" @click="resolveModalOpen = false" class="text-textMuted hover:text-textMain p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('complaints.resolve', $complaint) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Resolution Notes & Work Summary <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="resolution_notes" rows="4" required placeholder="Describe what was repaired, components cleaned/adjusted, laser calibration figures, and any advice given to customer..." class="w-full text-xs rounded-xl border border-borderMain bg-bg focus:ring-2 focus:ring-emerald-500 p-3"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-borderMain">
                    <button type="button" @click="resolveModalOpen = false" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-bold text-textMuted hover:bg-bg">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md flex items-center space-x-1.5 active:scale-95 transition">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Confirm Service Resolved</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- LIGHTBOX MODAL FOR IMAGES -->
    <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         @click.self="lightboxOpen = false">
        <div class="relative max-w-3xl w-full bg-surface rounded-3xl p-4 border border-borderMain shadow-2xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-borderMain">
                <span class="text-xs font-bold text-textMain" x-text="lightboxTitle"></span>
                <button type="button" @click="lightboxOpen = false" class="p-1 rounded-lg hover:bg-slate-100 text-textMuted hover:text-textMain">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="rounded-2xl overflow-hidden max-h-[75vh] flex items-center justify-center bg-black/5">
                <img :src="lightboxImage" alt="Preview" class="max-h-[70vh] w-auto object-contain rounded-xl">
            </div>
        </div>
    </div>

</div>
@endsection
