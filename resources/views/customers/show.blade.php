@extends('layouts.app')

@section('title', $customer->name . ' - Customer Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-secondary hover:underline flex items-center gap-1 mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Customers
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-primary-text">{{ $customer->name }}</h1>
                @if($customer->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-app-success/10 text-app-success">
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-secondary-text/10 text-secondary-text">
                        Inactive
                    </span>
                @endif
            </div>
            <p class="text-xs text-secondary-text mt-0.5">
                {{ $customer->department ?? 'General' }} • {{ $customer->organization->name ?? 'Direct Individual Client' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-primary-text transition-colors">
                <i data-lucide="edit-3" class="w-4 h-4 text-secondary"></i> Edit Contact
            </a>
            <form action="{{ route('customers.toggle', $customer) }}" method="POST" onsubmit="return confirm('Toggle active status?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-medium text-secondary-text transition-colors">
                    <i data-lucide="power" class="w-4 h-4"></i> {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('complaints.create', ['customer_id' => $customer->id, 'firm_id' => $customer->firm_id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> New Complaint
                </a>
            @endif
        </div>
    </div>

    <!-- Details Card & Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Contact Profile -->
        <div class="bg-surface rounded-xl border border-app-border p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-4">
                @if($customer->photo)
                    <img src="{{ Storage::url($customer->photo) }}" class="w-16 h-16 rounded-xl object-cover border border-app-border">
                @else
                    <div class="w-16 h-16 rounded-xl bg-secondary/15 text-secondary flex items-center justify-center font-bold text-2xl border border-app-border">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-bold text-primary-text">{{ $customer->name }}</h3>
                    <p class="text-xs text-secondary-text">{{ $customer->department ?? 'Client Contact' }}</p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-app-border text-xs">
                <div class="flex justify-between">
                    <span class="text-secondary-text">Phone:</span>
                    <a href="tel:{{ $customer->contact_no }}" class="font-semibold text-primary hover:underline">{{ $customer->contact_no }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-text">Firm:</span>
                    @if($customer->organization)
                        <a href="{{ route('organizations.show', $customer->organization) }}" class="font-medium text-secondary hover:underline text-right max-w-[180px]">
                            {{ $customer->organization->name }}
                        </a>
                    @else
                        <span class="text-secondary-text italic">Individual</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-text">Location:</span>
                    <span class="font-medium text-primary-text">{{ $customer->area->name ?? '—' }}, {{ $customer->city->name ?? '—' }}</span>
                </div>
                @if($customer->address)
                <div class="flex justify-between">
                    <span class="text-secondary-text">Address:</span>
                    <span class="font-medium text-primary-text text-right max-w-[180px]">{{ $customer->address }}</span>
                </div>
                @endif
                @if($customer->remarks)
                <div class="pt-2 border-t border-app-border">
                    <span class="text-secondary-text block mb-1">Notes:</span>
                    <p class="text-primary-text italic bg-app-bg p-2 rounded-md">{{ $customer->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="md:col-span-2 grid grid-cols-3 gap-4">
            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Total Complaints</span>
                    <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-primary-text">{{ $customer->complaints->count() }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">Tickets registered</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Pending Service</span>
                    <i data-lucide="clock" class="w-4 h-4 text-accent"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-accent">{{ $customer->complaints->whereIn('status', ['Pending', 'In Progress'])->count() }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">Under work</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Total Billed</span>
                    <i data-lucide="receipt" class="w-4 h-4 text-primary"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-primary">₹{{ number_format($customer->bills->sum('amount'), 2) }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">{{ $customer->bills->count() }} invoices</p>
                </div>
            </div>

            <!-- Complaint List -->
            <div class="col-span-3 bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border flex items-center justify-between bg-app-bg/40">
                    <h3 class="text-sm font-semibold text-primary-text flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-secondary"></i> Service Call History
                    </h3>
                    <span class="text-xs text-secondary-text">{{ $customer->complaints->count() }} calls</span>
                </div>

                @if($customer->complaints->isEmpty())
                    <div class="p-8 text-center text-secondary-text text-xs">
                        No service complaints booked for this contact yet.
                    </div>
                @else
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($customer->complaints as $c)
                            <div class="p-4 rounded-xl border border-app-border bg-surface hover:border-secondary transition shadow-sm flex flex-col justify-between">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-xs text-primary">{{ $c->ticket_number ?? ('#CMP-' . str_pad($c->id, 4, '0', STR_PAD_LEFT)) }}</span>
                                        @if(strtolower($c->status) === 'completed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                        @elseif(strtolower($c->status) === 'in_progress' || strtolower($c->status) === 'in progress')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">In Progress</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">{{ $c->status }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-xs text-textMain">{{ $c->productType->name ?? 'Machinery' }}</h4>
                                    <p class="text-[11px] text-textMuted line-clamp-2">{{ $c->nature_of_complaint ?? $c->details }}</p>
                                    <div class="text-[11px] text-secondary-text pt-1">
                                        Engineer: <strong class="text-textMain">{{ $c->engineer->name ?? 'Unassigned' }}</strong>
                                    </div>
                                </div>
                                <div class="pt-3 mt-2 border-t border-app-border flex items-center justify-between text-xs">
                                    <span class="text-textMuted text-[10px]">Due: {{ $c->due_date ? \Carbon\Carbon::parse($c->due_date)->format('d M Y') : '—' }}</span>
                                    <a href="{{ route('complaints.show', $c) }}" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                                        <span>View Ticket</span>
                                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
