@extends('layouts.app')

@section('title', $organization->name . ' - Firm Details')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('organizations.index') }}" class="text-xs font-semibold text-secondary hover:underline flex items-center gap-1 mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Organizations
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-primary-text">{{ $organization->name }}</h1>
                @if($organization->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-app-success/10 text-app-success border border-app-success/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-app-success mr-1"></span> Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-secondary-text/10 text-secondary-text border border-secondary-text/20">
                        Inactive
                    </span>
                @endif
            </div>
            <p class="text-xs text-secondary-text mt-0.5">
                GSTIN: <span class="font-mono font-medium text-primary-text">{{ $organization->gst_no ?? 'Unregistered / Not Provided' }}</span> • Added {{ $organization->created_at->format('M d, Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('organizations.edit', $organization) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-primary-text transition-colors">
                <i data-lucide="edit-3" class="w-4 h-4 text-secondary"></i> Edit Firm
            </a>
            <form action="{{ route('organizations.toggle', $organization) }}" method="POST" onsubmit="return confirm('Change active status for this organization?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-medium text-secondary-text transition-colors">
                    <i data-lucide="power" class="w-4 h-4"></i> {{ $organization->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('complaints.create', ['firm_id' => $organization->id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Book Service Call
                </a>
            @endif
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Firm Card -->
        <div class="bg-surface rounded-xl border border-app-border p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-4">
                @if($organization->photo)
                    <img src="{{ Storage::url($organization->photo) }}" class="w-16 h-16 rounded-lg object-cover border border-app-border">
                @else
                    <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-xl border border-app-border">
                        {{ strtoupper(substr($organization->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-bold text-primary-text">{{ $organization->name }}</h3>
                    <p class="text-xs text-secondary-text">{{ $organization->area->name ?? 'Industrial Zone' }}, {{ $organization->city->name ?? 'City' }}</p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-app-border text-xs">
                <div class="flex justify-between">
                    <span class="text-secondary-text">Primary Contact:</span>
                    <span class="font-medium text-primary-text">{{ $organization->contact_person ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-text">Phone:</span>
                    <span class="font-medium text-primary-text">{{ $organization->contact_no ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-text">Address:</span>
                    <span class="font-medium text-primary-text text-right max-w-[200px]">{{ $organization->address ?? '—' }} ({{ $organization->pincode }})</span>
                </div>
                @if($organization->remarks)
                <div class="pt-2 border-t border-app-border">
                    <span class="text-secondary-text block mb-1">Remarks:</span>
                    <p class="text-primary-text italic bg-app-bg p-2 rounded-md">{{ $organization->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Metrics -->
        <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Service Calls</span>
                    <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-primary-text">{{ $organization->complaints->count() }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">Total tickets recorded</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Active / Pending</span>
                    <i data-lucide="clock" class="w-4 h-4 text-accent"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-accent">{{ $organization->complaints->whereIn('status', ['Pending', 'In Progress'])->count() }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">Under resolution</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Total Invoiced</span>
                    <i data-lucide="receipt" class="w-4 h-4 text-primary"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-primary">₹{{ number_format($organization->bills->sum('amount'), 2) }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">{{ $organization->bills->count() }} bill records</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-xl border border-app-border shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-secondary-text mb-2">
                    <span class="text-xs font-medium">Staff Contacts</span>
                    <i data-lucide="users" class="w-4 h-4 text-app-success"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-primary-text">{{ $organization->customers->count() }}</p>
                    <p class="text-[11px] text-secondary-text mt-0.5">Representatives</p>
                </div>
            </div>

            <!-- Customer Contacts Table snippet -->
            <div class="col-span-2 sm:col-span-4 bg-surface rounded-xl border border-app-border p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-primary uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-secondary"></i> Company Representatives & Contacts
                    </h3>
                    <a href="{{ route('customers.create', ['firm_id' => $organization->id]) }}" class="text-xs font-semibold text-secondary hover:underline flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Add Representative
                    </a>
                </div>
                @if($organization->customers->isEmpty())
                    <p class="text-xs text-secondary-text py-2">No individual representatives registered for this organization yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($organization->customers as $cust)
                        <div class="p-2.5 rounded-lg border border-app-border bg-app-bg/50 flex items-center justify-between">
                            <div>
                                <a href="{{ route('customers.show', $cust) }}" class="text-xs font-semibold text-primary-text hover:text-secondary">{{ $cust->name }}</a>
                                <p class="text-[11px] text-secondary-text">{{ $cust->department ?? 'General' }} • {{ $cust->contact_no }}</p>
                            </div>
                            <a href="tel:{{ $cust->contact_no }}" class="p-1 rounded text-secondary hover:bg-secondary/10">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Service Complaints History -->
    <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
        <div class="p-4 border-b border-app-border flex items-center justify-between bg-app-bg/40">
            <h3 class="text-sm font-semibold text-primary-text flex items-center gap-2">
                <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i> Machinery Service & Complaint History
            </h3>
            <span class="text-xs text-secondary-text">{{ $organization->complaints->count() }} records</span>
        </div>

        @if($organization->complaints->isEmpty())
            <div class="p-8 text-center text-secondary-text text-xs">
                No service calls logged for this firm yet.
            </div>
        @else
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($organization->complaints as $c)
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
                            <span class="text-textMuted text-[10px]">{{ $c->created_at->format('d M Y') }}</span>
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
@endsection
