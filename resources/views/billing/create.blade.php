@extends('layouts.app')

@section('title', 'Add Bill | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </span>
                Add Bill / Invoice
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Record cash payment, credit dues, or GST invoice</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('billing.index') }}" class="px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="billingForm" class="px-6 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save Bill
            </button>
        </div>
    </div>

    <!-- Main Full-Width Form Layout -->
    <form id="billingForm" action="{{ route('billing.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="{ selectedType: 'cash', billAmount: '{{ old('amount', '') }}' }">
        @csrf

        <!-- LEFT COLUMN: 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Ticket Association Alert if coming from complaint -->
            @if($complaint)
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between text-xs text-blue-900 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-secondary shrink-0">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <div class="font-bold text-sm text-blue-950">Resolving Ticket: #{{ $complaint->ticket_number }}</div>
                        <p class="text-blue-800 text-[11px] mt-0.5">
                            Customer: <strong>{{ $complaint->customer?->name }}</strong> • Equipment: <strong>{{ $complaint->productType?->name }}</strong>
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-blue-200 text-blue-950 font-bold uppercase text-[10px]">
                    Auto-Linked
                </span>
                <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                <input type="hidden" name="customer_id" value="{{ $complaint->customer_id }}">
                <input type="hidden" name="firm_id" value="{{ $complaint->firm_id }}">
            </div>
            @elseif(auth()->user()->isEngineer())
            <!-- Engineer Assigned Job Selector -->
            <div class="bg-surface rounded-2xl border border-app-border shadow-sm p-4 sm:p-5 space-y-3.5" x-data="{
                selectedJobId: '{{ old('complaint_id', '') }}',
                jobs: {{ Js::from($myComplaints->map(fn($c) => ['id' => $c->id, 'number' => $c->ticket_number, 'customer_id' => $c->customer_id, 'customer_name' => $c->customer?->name, 'customer_phone' => $c->customer?->contact_no, 'firm_id' => $c->firm_id, 'firm_name' => $c->organization?->name, 'product' => $c->productType?->name])) }},
                activeJob: null,
                init() {
                    if (this.selectedJobId) {
                        this.activeJob = this.jobs.find(j => j.id == this.selectedJobId) || null;
                    } else if (this.jobs.length === 1) {
                        this.selectedJobId = this.jobs[0].id;
                        this.activeJob = this.jobs[0];
                    }
                },
                onSelectJob(id) {
                    this.selectedJobId = id;
                    this.activeJob = this.jobs.find(j => j.id == id) || null;
                }
            }">
                <div class="flex items-center space-x-2 text-primary font-bold text-xs uppercase tracking-wider pb-2 border-b border-app-border">
                    <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                    <span>Select Assigned Service Ticket to Bill & Complete</span>
                </div>

                @if($myComplaints->isEmpty())
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-center space-y-2">
                        <i data-lucide="alert-circle" class="w-8 h-8 text-amber-600 mx-auto"></i>
                        <p class="text-xs font-bold text-amber-900">No Pending Assigned Jobs</p>
                        <p class="text-[11px] text-amber-800">You don't have any active service jobs assigned right now. Bills must be generated for assigned service tickets.</p>
                        <a href="{{ route('complaints.index') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold shadow-xs">
                            <span>View My Tasks</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-primary-text mb-1.5">
                            Assigned Service Call <span class="text-rose-500">*</span>
                        </label>
                        <select name="complaint_id" required x-model="selectedJobId" @change="onSelectJob($event.target.value)" class="w-full text-xs rounded-xl border border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            <option value="">-- Choose Assigned Service Ticket --</option>
                            @foreach($myComplaints as $job)
                                <option value="{{ $job->id }}">
                                    #{{ $job->ticket_number }} - {{ $job->customer?->name }} ({{ $job->productType?->name ?? 'Machinery' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Auto-filled Customer info preview -->
                    <div x-show="activeJob" x-cloak class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200 text-xs text-blue-900 space-y-1">
                        <div class="font-extrabold text-blue-950 flex items-center justify-between">
                            <span x-text="'Client: ' + (activeJob ? activeJob.customer_name + ' (' + activeJob.customer_phone + ')' : '')"></span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-blue-200 text-blue-900 font-bold uppercase">Assigned Ticket</span>
                        </div>
                        <p class="text-[11px] text-blue-800" x-show="activeJob && activeJob.firm_name" x-text="'Firm: ' + (activeJob ? activeJob.firm_name : '')"></p>
                        <p class="text-[11px] text-slate-600" x-show="activeJob && activeJob.product" x-text="'Machine: ' + (activeJob ? activeJob.product : '')"></p>
                        <input type="hidden" name="customer_id" :value="activeJob ? activeJob.customer_id : ''">
                        <input type="hidden" name="firm_id" :value="activeJob ? activeJob.firm_id : ''">
                    </div>
                @endif
            </div>
            @else
            <!-- Client Selection (Admin) -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-secondary"></i> Customer Details
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Customer Contact <span class="text-app-error">*</span>
                            </label>
                            <select name="customer_id" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->name }} ({{ $cust->contact_no }}) - {{ $cust->organization?->name ?? 'Individual' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Firm / Company (Optional)
                            </label>
                            <select name="firm_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select Firm / Company --</option>
                                @foreach($firms as $firm)
                                <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                    {{ $firm->name }} (GST: {{ $firm->gst_no ?? 'N/A' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoice Details & Payment Structure -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4 text-accent"></i> Payment Details
                    </h3>
                    <span class="text-[11px] text-secondary-text">Tax & payment rules</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Total Bill Amount (₹) <span class="text-app-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-secondary-text font-bold">₹</span>
                                <input type="number" step="0.01" name="amount" x-model="billAmount" value="{{ old('amount') }}" required placeholder="5500.00" class="w-full text-sm font-bold rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 pl-8 pr-3">
                            </div>
                            @error('amount')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Internal Voucher / Serial No.
                            </label>
                            <input type="text" name="bill_number" value="{{ old('bill_number') }}" placeholder="Auto-generated if blank" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <!-- Payment Settlement Type: Radio Buttons -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-primary-text mb-2">
                                Payment / Settlement Type <span class="text-app-error">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-bold"
                                       :class="selectedType === 'cash' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 shadow-sm' : 'border-app-border bg-white text-secondary-text hover:bg-app-bg'">
                                    <input type="radio" name="bill_type" value="cash" x-model="selectedType" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <span>Cash (On-Site)</span>
                                </label>
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-bold"
                                       :class="selectedType === 'credit' ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm' : 'border-app-border bg-white text-secondary-text hover:bg-app-bg'">
                                    <input type="radio" name="bill_type" value="credit" x-model="selectedType" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                                    <span>Credit (Due)</span>
                                </label>
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-bold"
                                       :class="selectedType === 'bill' ? 'border-blue-500 bg-blue-50 text-blue-900 shadow-sm' : 'border-app-border bg-white text-secondary-text hover:bg-app-bg'">
                                    <input type="radio" name="bill_type" value="bill" x-model="selectedType" class="text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <span>GST Tax Bill</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- GST Invoice Number Field (shown when Tax Bill selected) -->
                    <div x-show="selectedType === 'bill'" x-cloak class="p-4 rounded-xl bg-blue-50/60 border border-blue-200 space-y-1">
                        <label class="block text-xs font-bold text-blue-900 uppercase tracking-wider">
                            Official GST Tax Invoice Number
                        </label>
                        <input type="text" name="gst_bill_number" value="{{ old('gst_bill_number') }}" placeholder="e.g. GST-INV-2026-0948" class="w-full text-xs rounded-lg border-blue-300 bg-white focus:ring-secondary focus:border-secondary py-2 px-3 font-mono">
                        <p class="text-[10px] text-blue-800">Enter the legally issued GST tax invoice identifier for formal accounting.</p>
                    </div>
                </div>
            </div>

            <!-- Photos: Signed Voucher & Machine Post-Service Photo -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4 text-secondary"></i> On-Site Work & Receipt Attachments
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="border border-app-border rounded-xl p-4 bg-app-bg/50">
                        <label class="block text-xs font-semibold text-primary-text mb-1">
                            Customer Signed Voucher / Receipt
                        </label>
                        <p class="text-[11px] text-secondary-text mb-2">Signed physical paper slip</p>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                    </div>

                    <div class="border border-app-border rounded-xl p-4 bg-app-bg/50">
                        <label class="block text-xs font-semibold text-primary-text mb-1">
                            Machine Post-Service Photo
                        </label>
                        <p class="text-[11px] text-secondary-text mb-2">Proof of restored machine</p>
                        <input type="file" name="asset_photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                    </div>
                </div>
            </div>

            <!-- Notes & Remarks -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-6 space-y-2">
                <label class="block text-xs font-semibold text-primary-text">
                    Work Summary & Spare Parts Billed
                </label>
                <textarea name="remarks" rows="3" placeholder="Replaced servo drive belt, calibrated PLC parameters, 4 hours labor..." class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <!-- RIGHT COLUMN: Financial Summary & Actions (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Settlement Summary Card -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="badge-percent" class="w-4 h-4 text-secondary"></i> Billing Summary
                </h3>

                <div class="p-4 rounded-xl bg-app-bg border border-app-border space-y-2">
                    <span class="text-xs text-secondary-text block">Invoice Total:</span>
                    <div class="text-3xl font-extrabold text-primary">
                        ₹<span x-text="billAmount ? parseFloat(billAmount).toFixed(2) : '0.00'">0.00</span>
                    </div>
                    <div class="pt-2 border-t border-app-border flex items-center justify-between text-xs">
                        <span class="text-secondary-text">Settlement:</span>
                        <span class="font-bold uppercase text-[11px]" :class="{
                            'text-app-success': selectedType === 'cash',
                            'text-accent': selectedType === 'credit',
                            'text-secondary': selectedType === 'bill'
                        }" x-text="selectedType"></span>
                    </div>
                </div>

                <div x-show="selectedType === 'cash'" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-[11px] text-amber-900 leading-relaxed">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 inline mr-1 text-amber-600"></i>
                    <strong>Physical Cash Handover:</strong> Cash collected remains recorded in your possession until an administrator marks it verified in the office ledger.
                </div>

                <div x-show="selectedType === 'credit'" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-[11px] text-blue-900 leading-relaxed">
                    <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1 text-secondary"></i>
                    <strong>Credit Outstanding:</strong> This bill will remain marked as Pending Payment until a subsequent collection is recorded against it.
                </div>
            </div>

            <!-- Submission Actions -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Bill
                </button>
                <a href="{{ route('billing.index') }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
