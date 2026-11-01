@extends('layouts.app')

@section('title', 'Customers & Client Directory')

@section('content')
<div class="space-y-6" x-data="{ filterModalOpen: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-primary-text">Customer Directory</h1>
            <p class="text-xs text-secondary-text">Manage client representatives, factory managers and machinery owners</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Open Filter Modal Button -->
            <button type="button" @click="filterModalOpen = true" class="px-3.5 py-2 rounded-xl border border-app-border bg-surface hover:bg-app-bg text-xs font-bold text-primary-text transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-secondary"></i>
                <span>Filters</span>
                @if(request()->hasAny(['firm_id', 'search']))
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                @endif
            </button>

            <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition">
                <i data-lucide="user-plus" class="w-4 h-4 text-accent"></i>
                <span>Add Contact</span>
            </a>
        </div>
    </div>

    <!-- Quick Search Bar -->
    <div class="bg-surface rounded-2xl p-3 sm:p-3.5 border border-app-border shadow-sm">
        <form action="{{ route('customers.index') }}" method="GET" class="flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-secondary-text"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by contact name, phone, dept..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-app-border bg-app-bg/50 outline-none focus:border-secondary">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-secondary hover:bg-secondary/90 text-white text-xs font-semibold shadow-sm transition">
                Search
            </button>
        </form>
    </div>

    <!-- FILTER POPUP MODAL -->
    <div x-show="filterModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <!-- Modal Dialog -->
        <div class="relative w-full max-w-md bg-surface rounded-3xl shadow-2xl border border-app-border p-6 z-50 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-app-border">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center font-bold">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-primary-text">Filter Customer Directory</h3>
                        <p class="text-xs text-secondary-text">Filter by firm or contact details</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-secondary-text hover:text-primary-text p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('customers.index') }}" method="GET" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-primary-text uppercase tracking-wider mb-1.5">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-secondary-text"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contact name, phone, designation..." class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-app-border bg-app-bg/50 outline-none focus:border-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-primary-text uppercase tracking-wider mb-1.5">Organization / Firm</label>
                    <select name="firm_id" class="w-full px-3 py-2.5 text-xs rounded-xl border border-app-border bg-app-bg/50 outline-none focus:border-secondary">
                        <option value="">All Organizations / Firms</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ request('firm_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-app-border gap-3">
                    <a href="{{ route('customers.index') }}" class="px-4 py-2.5 rounded-xl border border-app-border text-xs font-semibold text-secondary-text hover:text-primary-text hover:bg-app-bg transition text-center">
                        Reset
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="filterModalOpen = false" class="px-4 py-2.5 rounded-xl border border-app-border text-xs font-semibold text-primary-text hover:bg-app-bg transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-md transition">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($customers as $c)
            <div class="bg-surface rounded-2xl border border-app-border p-5 shadow-sm hover:border-secondary hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Top: Avatar, Name, Status -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-app-border">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($c->photo)
                                <img src="{{ Storage::url($c->photo) }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $c->name }}') + '&background=42919e&color=FFFFFF&bold=true';" class="w-11 h-11 rounded-xl object-cover border border-app-border shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-secondary/15 text-secondary flex items-center justify-center font-black text-sm shrink-0">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('customers.show', $c) }}" class="font-bold text-sm text-primary-text hover:text-secondary truncate block">
                                    {{ $c->name }}
                                </a>
                                <p class="text-[11px] text-secondary-text truncate">{{ $c->department ?? 'Contact Person' }}</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $c->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $c->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="py-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-secondary-text">Firm / Client:</span>
                            @if($c->organization)
                                <a href="{{ route('organizations.show', $c->organization) }}" class="font-semibold text-primary hover:underline truncate max-w-[170px] text-right">
                                    {{ $c->organization->name }}
                                </a>
                            @else
                                <span class="text-secondary-text italic">Individual Client</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-secondary-text">Phone:</span>
                            <div class="flex items-center gap-1.5">
                                @php
                                    $rawCustPhone = preg_replace('/[^0-9]/', '', $c->contact_no);
                                    $waCustPhone = strlen($rawCustPhone) === 10 ? '91' . $rawCustPhone : $rawCustPhone;
                                @endphp
                                <a href="tel:{{ $c->contact_no }}" class="font-bold text-primary-text hover:text-secondary flex items-center gap-1">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-secondary"></i>
                                    <span>{{ $c->contact_no }}</span>
                                </a>
                                <!-- Regular WhatsApp -->
                                <a href="https://web.whatsapp.com/send?phone={{ $waCustPhone }}" 
                                   onclick="window.openWhatsAppDirect(event, '{{ $waCustPhone }}', 'regular')" 
                                   target="_blank" 
                                   class="p-1 rounded-lg bg-emerald-50 text-[#25D366] hover:bg-[#25D366] hover:text-white transition shadow-sm" 
                                   title="WhatsApp (Personal)">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- WhatsApp Business -->
                                <a href="https://wa.me/{{ $waCustPhone }}" 
                                   onclick="window.openWhatsAppDirect(event, '{{ $waCustPhone }}', 'business')" 
                                   target="_blank" 
                                   class="p-1 rounded-lg bg-teal-50 text-[#128C7E] hover:bg-[#128C7E] hover:text-white transition shadow-sm" 
                                   title="WhatsApp Business (Direct App)">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                        <text x="12" y="14.5" font-size="8" font-weight="900" text-anchor="middle" fill="currentColor" stroke="none" font-family="system-ui, -apple-system, sans-serif">B</text>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-secondary-text">Location:</span>
                            <span class="text-primary-text font-medium">{{ $c->city->name ?? 'Surat' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-secondary-text">Service History:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">
                                {{ $c->complaints_count }} calls
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="pt-3 border-t border-app-border flex items-center justify-between gap-2">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('complaints.create', ['customer_id' => $c->id]) }}" class="px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90 flex items-center gap-1.5 transition shadow-sm">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-accent"></i>
                            <span>New Ticket</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-1">
                        <a href="{{ route('customers.show', $c) }}" title="View Profile" class="p-1.5 rounded-lg border border-app-border text-secondary hover:bg-app-bg transition">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('customers.edit', $c) }}" title="Edit Contact" class="p-1.5 rounded-lg border border-app-border text-secondary-text hover:bg-app-bg transition">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center text-secondary-text bg-surface rounded-2xl border border-app-border">
                <i data-lucide="users" class="w-10 h-10 mx-auto text-secondary-text/40 mb-2"></i>
                <p class="font-medium text-xs">No customers found. Try different filters or register a new contact.</p>
            </div>
        @endforelse
    </div>

    @if($customers->hasPages())
        <div class="p-4 bg-surface rounded-2xl border border-app-border">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
