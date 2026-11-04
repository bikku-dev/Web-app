@extends('layouts.app')

@section('title', 'Service Tickets | Bhumi Technocare')
@section('page_title', 'Service Tickets & Complaint Management')

@section('content')
<div class="space-y-4 sm:space-y-5" x-data="{ 
    filterModalOpen: false 
}">

    <!-- 1. HEADER ACTION & SEARCH TOOLBAR -->
    <div class="bg-surface rounded-2xl p-2.5 sm:p-3.5 border border-borderMain shadow-2xs space-y-2 sm:space-y-0 sm:flex sm:items-center sm:justify-between sm:gap-2.5">
        
        <!-- Search Input -->
        <form action="{{ route('complaints.index') }}" method="GET" class="flex-1 flex items-center gap-1.5">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-2.5 text-textMuted"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search COMP. ID (e.g. S2611001), customer, phone, area..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
            </div>
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('product_type_id'))
                <input type="hidden" name="product_type_id" value="{{ request('product_type_id') }}">
            @endif
            <button type="submit" class="hidden sm:inline-flex px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-2xs">
                Search
            </button>
        </form>

        <!-- Action Buttons (Responsive flex row on phone) -->
        <div class="flex items-center justify-between sm:justify-end gap-1.5 shrink-0">
            <!-- Filter Modal Button -->
            <button type="button" 
                    @click="filterModalOpen = true" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1 px-2.5 sm:px-3 py-1.5 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-xs font-bold text-textMain transition shadow-2xs">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-secondary"></i>
                <span>Filters</span>
                @if(request()->hasAny(['product_type_id', 'status', 'priority', 'engineer_id']))
                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                @endif
            </button>

            <!-- Book Service Button (Admin Only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('complaints.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center space-x-1 px-3 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-xs shadow-2xs transition shrink-0">
                    <i data-lucide="plus" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>+ Book</span>
                </a>
            @endif
        </div>

    </div>

    <!-- 2. QUICK STATUS FILTER PILLS (SWIPEABLE ROW) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none text-xs">
        <a href="{{ route('complaints.index') }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ !request('status') && !request('overdue') ? 'bg-primary text-white shadow-2xs' : 'bg-surface border border-borderMain text-textMuted hover:bg-slate-100' }}">
            All Calls ({{ $complaints->total() }})
        </a>
        <a href="{{ route('complaints.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('status') === 'pending' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-blue-800 hover:bg-blue-50' }}">
            Pending
        </a>
        <a href="{{ route('complaints.index', array_merge(request()->query(), ['status' => 'in_progress'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('status') === 'in_progress' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-amber-800 hover:bg-amber-50' }}">
            In Progress
        </a>
        <a href="{{ route('complaints.index', array_merge(request()->query(), ['overdue' => '1'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('overdue') == '1' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-rose-700 hover:bg-rose-50' }}">
            🚨 Overdue
        </a>
        <a href="{{ route('complaints.index', array_merge(request()->query(), ['status' => 'completed'])) }}" 
           class="px-2.5 py-1 rounded-xl font-bold shrink-0 transition {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-surface border border-borderMain text-emerald-800 hover:bg-emerald-50' }}">
            Resolved
        </a>
    </div>

    <!-- 3. COMPACT SERVICE CARDS VIEW (Clean, Shorter, WhatsApp Dropdown, Call, Map) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3">
        @forelse($complaints as $ticket)
            @php
                $rawTicketPhone = preg_replace('/[^0-9]/', '', $ticket->customer?->contact_no ?? '');
                $waTicketPhone = strlen($rawTicketPhone) === 10 ? '91' . $rawTicketPhone : $rawTicketPhone;
                $prodPrefix = $ticket->productType?->prefix ?? 'O';
            @endphp

            <div class="bg-surface rounded-2xl border border-borderMain p-3 sm:p-3.5 shadow-2xs hover:shadow-xs transition relative flex flex-col justify-between gap-2.5 border-l-4 {{ $ticket->isOverdue() ? 'border-l-rose-500 bg-rose-50/10' : ($ticket->status === 'completed' ? 'border-l-emerald-500' : ($ticket->status === 'in_progress' ? 'border-l-amber-500' : 'border-l-secondary')) }}">

                <div class="space-y-2">
                    <!-- Top Line: Complaint ID + Product Badge + Status Pill + Due Date -->
                    <div class="flex items-center justify-between gap-1.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <!-- Complaint ID -->
                            <a href="{{ route('complaints.show', $ticket) }}" class="font-mono font-black text-xs text-primary hover:text-secondary transition bg-slate-100 px-1.5 py-0.5 rounded-lg border border-slate-200">
                                {{ $ticket->ticket_number }}
                            </a>

                            <!-- Equipment Badge (Computer, Printer, CCTV, Other) -->
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-black uppercase tracking-wider {{ $prodPrefix === 'S' ? 'bg-purple-100 text-purple-800' : ($prodPrefix === 'P' ? 'bg-blue-100 text-blue-800' : ($prodPrefix === 'C' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700')) }}">
                                {{ $ticket->productType?->name ?? 'Equipment' }}
                            </span>

                            <!-- Status Pill -->
                            @if($ticket->isOverdue())
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-black bg-rose-100 text-rose-700">OVERDUE</span>
                            @elseif($ticket->status === 'completed')
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">RESOLVED</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-amber-100 text-amber-800">IN PROGRESS</span>
                            @else
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800">PENDING</span>
                            @endif
                        </div>

                        <!-- Due Date -->
                        <div class="text-[10.5px] font-semibold shrink-0 {{ $ticket->isOverdue() ? 'text-rose-600 font-extrabold' : 'text-textMuted' }}">
                            {{ $ticket->due_date?->format('d M, h:i A') }}
                        </div>
                    </div>

                    <!-- Customer Name & Area / Firm Line -->
                    <div class="flex items-start justify-between gap-2 pt-0.5">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-extrabold text-xs sm:text-sm text-textMain truncate leading-tight">
                                {{ $ticket->customer?->name ?? 'Walk-in Client' }}
                            </h4>
                            <div class="text-[11px] text-textMuted truncate flex items-center gap-1 mt-0.5">
                                @if($ticket->customer?->area?->name)
                                    <span class="font-semibold text-slate-700">📍 {{ $ticket->customer->area->name }}</span>
                                    <span>•</span>
                                @endif
                                <span class="truncate">{{ $ticket->organization?->name ?? 'Direct Customer' }}</span>
                            </div>
                        </div>

                        <!-- 1-Tap Communication Actions: Call, WhatsApp (Choice Dropdown), Map -->
                        <div class="flex items-center gap-1 shrink-0">
                            @if($ticket->customer?->contact_no)
                                <!-- 1. CALL BUTTON -->
                                <a href="tel:{{ $ticket->customer->contact_no }}" 
                                   class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" 
                                   title="Call {{ $ticket->customer->contact_no }}">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- 2. WHATSAPP BUTTON (With Choice Popup: Regular vs Business) -->
                                <div class="relative" x-data="{ waMenuOpen: false }">
                                    <button type="button" 
                                            @click="waMenuOpen = !waMenuOpen" 
                                            class="w-7 h-7 rounded-xl bg-emerald-50 text-[#25D366] hover:bg-[#25D366] hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" 
                                            title="WhatsApp Customer (Select Regular or Business)">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- WhatsApp Choice Dropdown Menu -->
                                    <div x-show="waMenuOpen" 
                                         @click.outside="waMenuOpen = false" 
                                         x-cloak 
                                         class="absolute right-0 top-8 z-30 w-52 bg-white rounded-2xl shadow-xl border border-borderMain p-1.5 divide-y divide-slate-100 text-left">
                                        <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            WhatsApp Customer
                                        </div>
                                        <div class="py-1 space-y-0.5">
                                            <!-- Option A: Regular WhatsApp -->
                                            <a href="https://web.whatsapp.com/send?phone={{ $waTicketPhone }}" 
                                               onclick="window.openWhatsAppDirect(event, '{{ $waTicketPhone }}', 'regular')" 
                                               target="_blank" 
                                               @click="waMenuOpen = false"
                                               class="w-full px-2 py-1.5 rounded-xl hover:bg-emerald-50 flex items-center space-x-2 text-xs font-bold text-slate-800 transition">
                                                <span class="w-5 h-5 rounded-lg bg-emerald-100 text-[#25D366] flex items-center justify-center shrink-0">
                                                    <i data-lucide="message-circle" class="w-3 h-3"></i>
                                                </span>
                                                <div class="truncate">
                                                    <div>WhatsApp</div>
                                                    <div class="text-[9.5px] text-textMuted font-normal">Regular / Personal</div>
                                                </div>
                                            </a>

                                            <!-- Option B: WhatsApp Business -->
                                            <a href="https://wa.me/{{ $waTicketPhone }}" 
                                               onclick="window.openWhatsAppDirect(event, '{{ $waTicketPhone }}', 'business')" 
                                               target="_blank" 
                                               @click="waMenuOpen = false"
                                               class="w-full px-2 py-1.5 rounded-xl hover:bg-teal-50 flex items-center space-x-2 text-xs font-bold text-slate-800 transition">
                                                <span class="w-5 h-5 rounded-lg bg-teal-100 text-[#128C7E] flex items-center justify-center shrink-0">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                                        <text x="12" y="14.5" font-size="8" font-weight="900" text-anchor="middle" fill="currentColor" stroke="none" font-family="system-ui, -apple-system, sans-serif">B</text>
                                                    </svg>
                                                </span>
                                                <div class="truncate">
                                                    <div>WhatsApp Business</div>
                                                    <div class="text-[9.5px] text-textMuted font-normal">Business App direct</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- 3. MAP NAVIGATION BUTTON -->
                            @if($ticket->customer?->address || $ticket->customer?->area?->name || $ticket->customer?->name)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($ticket->customer?->address ?? '') . ' ' . ($ticket->customer?->area?->name ?? 'Surat') . ' ' . ($ticket->customer?->name ?? '')) }}" 
                                   target="_blank" 
                                   class="w-7 h-7 rounded-xl bg-blue-50 text-secondary hover:bg-secondary hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" 
                                   title="Open Google Maps Location">
                                    <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Problem / Issue Snippet -->
                    <p class="text-xs text-slate-600 line-clamp-1 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                        {{ $ticket->issue_description }}
                    </p>
                </div>

                <!-- Card Footer: Engineer Info & Quick Action Link -->
                <div class="pt-2 border-t border-borderMain/60 flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <div class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 font-bold text-[9px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($ticket->engineer?->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-textMuted font-semibold text-[10.5px] truncate">{{ $ticket->engineer?->name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        @if(auth()->user()->isEngineer() && $ticket->engineer_id === auth()->id() && !$ticket->is_started)
                            <form action="{{ route('complaints.start', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10.5px] shadow-2xs flex items-center space-x-1 active:scale-95 transition">
                                    <i data-lucide="play" class="w-3 h-3"></i>
                                    <span>Start</span>
                                </button>
                            </form>
                        @elseif(auth()->user()->isEngineer() && $ticket->engineer_id === auth()->id() && $ticket->status === 'in_progress')
                            <a href="{{ route('billing.create', ['complaint_id' => $ticket->id]) }}" class="px-2.5 py-1 rounded-lg bg-accent hover:bg-amber-400 text-primary font-black text-[10.5px] shadow-2xs flex items-center space-x-1 active:scale-95 transition">
                                <i data-lucide="receipt" class="w-3 h-3"></i>
                                <span>Bill</span>
                            </a>
                        @endif

                        <a href="{{ route('complaints.pdf', $ticket) }}" title="Download Job Sheet PDF" class="p-1 rounded-lg border border-borderMain hover:bg-slate-100 text-primary transition">
                            <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                        </a>

                        <a href="{{ route('complaints.show', $ticket) }}" class="px-2.5 py-1 rounded-lg bg-bg hover:bg-slate-200 text-textMain font-bold text-[10.5px] flex items-center space-x-0.5 transition">
                            <span>Details</span>
                            <i data-lucide="chevron-right" class="w-3 h-3 text-textMuted"></i>
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-surface rounded-2xl p-10 text-center text-textMuted border border-borderMain">
                <i data-lucide="wrench" class="w-10 h-10 mx-auto text-secondary/40 mb-2"></i>
                <h4 class="font-bold text-sm text-textMain">No tickets found</h4>
                <p class="text-xs text-textMuted mt-0.5">Try clearing your filters or search keywords.</p>
            </div>
        @endforelse
    </div>

    <!-- 4. PAGINATION -->
    <div class="mt-3">
        {{ $complaints->links() }}
    </div>

    <!-- 6. FILTER POPUP MODAL -->
    <div x-show="filterModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="relative w-full max-w-md bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 sm:p-6 z-50 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3.5 border-b border-borderMain">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-textMain">Filter Service Complaints</h3>
                        <p class="text-[11px] text-textMuted">Filter by equipment, status, priority</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('complaints.index') }}" method="GET" class="mt-4 space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket #, customer, area..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Equipment / Category</label>
                    <select name="product_type_id" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Categories (Computer, Printer, CCTV, Other)</option>
                        @foreach($productTypes as $pt)
                            <option value="{{ $pt->id }}" {{ request('product_type_id') == $pt->id ? 'selected' : '' }}>
                                {{ $pt->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Ticket Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase tracking-wider mb-1">Field Engineer</label>
                    <select name="engineer_id" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-primary">
                        <option value="">All Field Engineers</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-borderMain gap-3">
                    <a href="{{ route('complaints.index') }}" class="px-3.5 py-2 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
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

</div>
@endsection
