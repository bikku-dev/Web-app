@extends('layouts.app')

@section('title', 'Service Tickets')
@section('page_title', 'Service Tickets & Breakdown Calls')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{ filterModalOpen: false }">

    <!-- HEADER ACTION & SEARCH TOOLBAR -->
    <div class="bg-surface rounded-2xl p-3 sm:p-4 border border-borderMain shadow-sm flex items-center justify-between gap-2.5">
        <!-- Quick Search -->
        <form action="{{ route('complaints.index') }}" method="GET" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-textMuted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ticket #, customer, firm..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
            </div>
            <button type="submit" class="hidden sm:inline-flex px-3.5 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-sm">
                Search
            </button>
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Open Filter Modal Button -->
            <button type="button" @click="filterModalOpen = true" class="px-3 sm:px-3.5 py-2 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-xs font-bold text-textMain transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-secondary"></i>
                <span>More</span>
                @if(request()->hasAny(['product_type_id', 'firm_id', 'engineer_id']))
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                @endif
            </button>

            <!-- Create Button (Admin) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('complaints.create') }}" class="px-3 sm:px-4 py-2 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-accent"></i>
                    <span class="hidden sm:inline">Book Service</span>
                    <span class="sm:hidden">+ Book</span>
                </a>
            @endif
        </div>
    </div>

    <!-- QUICK MOBILE STATUS FILTER PILLS (HORIZONTAL SCROLL) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('complaints.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ !request('status') && !request('overdue') ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-borderMain text-textMuted hover:bg-slate-100' }}">
            All Calls
        </a>
        <a href="{{ route('complaints.index', ['status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('status') === 'pending' ? 'bg-blue-600 text-white shadow-xs' : 'bg-surface border border-borderMain text-blue-800 hover:bg-blue-50' }}">
            ⏳ Pending
        </a>
        <a href="{{ route('complaints.index', ['status' => 'in_progress']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('status') === 'in_progress' ? 'bg-amber-600 text-white shadow-xs' : 'bg-surface border border-borderMain text-amber-800 hover:bg-amber-50' }}">
            ⚙️ In Progress
        </a>
        <a href="{{ route('complaints.index', ['overdue' => '1']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('overdue') == '1' ? 'bg-rose-600 text-white shadow-xs' : 'bg-surface border border-borderMain text-rose-700 hover:bg-rose-50' }}">
            🚨 Overdue
        </a>
        <a href="{{ route('complaints.index', ['status' => 'completed']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-surface border border-borderMain text-emerald-800 hover:bg-emerald-50' }}">
            ✅ Resolved
        </a>
    </div>

    <!-- FILTER POPUP MODAL -->
    <div x-show="filterModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <!-- Modal Dialog -->
        <div class="relative w-full max-w-md bg-surface rounded-3xl shadow-2xl border border-borderMain p-6 z-50 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-borderMain">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center font-bold">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-textMain">Filter Service Tickets</h3>
                        <p class="text-xs text-textMuted">Filter breakdown calls and maintenance jobs</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('complaints.index') }}" method="GET" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket #, customer, firm..." class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">Ticket Status</label>
                    <select name="status" class="w-full px-3 py-2.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">Urgency / Priority</label>
                    <select name="priority" class="w-full px-3 py-2.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div class="p-3 bg-bg rounded-xl border border-borderMain">
                    <label class="flex items-center space-x-2.5 cursor-pointer text-xs font-bold text-rose-600">
                        <input type="checkbox" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }} class="rounded border-borderMain text-rose-600 focus:ring-rose-500 w-4 h-4">
                        <span>Show Overdue Service Tickets Only</span>
                    </label>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-borderMain gap-3">
                    <a href="{{ route('complaints.index') }}" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
                        Reset
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="filterModalOpen = false" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-semibold text-textMain hover:bg-bg transition">
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

    <!-- TICKETS GRID / LIST (CLEAN, COMPACT, NON-NESTED CARDS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @forelse($complaints as $ticket)
            <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain p-4 sm:p-5 shadow-sm hover:shadow-md transition relative flex flex-col justify-between gap-3 {{ $ticket->isOverdue() ? 'border-l-4 border-l-rose-500 bg-rose-50/15' : 'hover:border-secondary/60' }}">

                <div class="space-y-2.5">
                    <!-- 1. Top Bar: Ticket #, Status Pill, Priority & Due Date -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <a href="{{ route('complaints.show', $ticket) }}" class="font-mono font-black text-xs sm:text-sm text-primary hover:text-secondary transition">
                                #{{ $ticket->ticket_number }}
                            </a>

                            <!-- Status Badge -->
                            @if($ticket->isOverdue())
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-100 text-rose-700">OVERDUE</span>
                            @elseif($ticket->status === 'completed')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">RESOLVED</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">IN PROGRESS</span>
                            @elseif($ticket->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">CANCELLED</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800">PENDING</span>
                            @endif

                            <!-- Priority Badge -->
                            @if($ticket->priority === 'urgent')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-red-50 text-red-700 border border-red-200 flex items-center space-x-0.5">
                                    <i data-lucide="flame" class="w-3 h-3 text-red-600"></i>
                                    <span>URGENT</span>
                                </span>
                            @elseif($ticket->priority === 'high')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700">High</span>
                            @endif
                        </div>

                        <!-- Due Date on Right -->
                        <div class="text-[11px] font-bold shrink-0 {{ $ticket->isOverdue() ? 'text-rose-600 font-extrabold' : 'text-textMuted' }}">
                            {{ $ticket->due_date?->format('d M, h:i A') }}
                        </div>
                    </div>

                    <!-- 2. Customer & Organization Line + Quick Action Call & Map Icons -->
                    <div class="flex items-center justify-between gap-2 pt-0.5">
                        <div class="min-w-0 flex-1">
                            <div class="font-black text-sm text-textMain truncate">
                                {{ $ticket->customer?->name ?? 'Walk-in Customer' }}
                            </div>
                            <div class="text-[11px] text-textMuted truncate">
                                {{ $ticket->organization?->name ?? 'Direct Client' }}
                            </div>
                        </div>

                        <!-- Quick Action Icons (Call, WhatsApp, WA Business & Map) -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($ticket->customer?->contact_no)
                                @php
                                    $rawTicketPhone = preg_replace('/[^0-9]/', '', $ticket->customer->contact_no);
                                    $waTicketPhone = strlen($rawTicketPhone) === 10 ? '91' . $rawTicketPhone : $rawTicketPhone;
                                @endphp
                                <a href="tel:{{ $ticket->customer->contact_no }}" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition shadow-sm active:scale-95" title="Call Client">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                </a>
                                <!-- Regular WhatsApp -->
                                <a href="https://web.whatsapp.com/send?phone={{ $waTicketPhone }}" 
                                   onclick="window.openWhatsAppDirect(event, '{{ $waTicketPhone }}', 'regular')" 
                                   target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-emerald-50 text-[#25D366] hover:bg-[#25D366] hover:text-white flex items-center justify-center transition shadow-sm active:scale-95" 
                                   title="WhatsApp (Personal / Consumer)">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- WhatsApp Business -->
                                <a href="https://wa.me/{{ $waTicketPhone }}" 
                                   onclick="window.openWhatsAppDirect(event, '{{ $waTicketPhone }}', 'business')" 
                                   target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-teal-50 text-[#128C7E] hover:bg-[#128C7E] hover:text-white flex items-center justify-center transition shadow-sm active:scale-95" 
                                   title="WhatsApp Business (Direct App)">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                        <text x="12" y="14.5" font-size="8" font-weight="900" text-anchor="middle" fill="currentColor" stroke="none" font-family="system-ui, -apple-system, sans-serif">B</text>
                                    </svg>
                                </a>
                            @endif
                            @if($ticket->customer?->address || $ticket->organization?->address || $ticket->customer?->name)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($ticket->customer?->address ?? $ticket->organization?->address ?? 'Surat') . ' ' . ($ticket->customer?->name ?? '')) }}" target="_blank" class="w-8 h-8 rounded-xl bg-blue-50 text-secondary hover:bg-secondary hover:text-white flex items-center justify-center transition shadow-sm active:scale-95" title="Google Maps Navigation">
                                    <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Equipment & Service -->
                    <div class="flex items-center space-x-2 text-xs text-secondary font-bold">
                        <i data-lucide="wrench" class="w-3.5 h-3.5 shrink-0 text-secondary"></i>
                        <span class="text-textMain font-extrabold truncate">{{ $ticket->productType?->name ?? 'Machinery' }}</span>
                        <span class="text-textMuted font-normal text-[11px] truncate">• {{ $ticket->serviceType?->name ?? 'Service' }}</span>
                    </div>

                    <!-- 4. Issue Snippet -->
                    <p class="text-xs text-textMuted line-clamp-2 leading-relaxed">
                        {{ $ticket->issue_description }}
                    </p>
                </div>

                <!-- 5. Card Footer: Engineer Name & Direct Action -->
                <div class="pt-2.5 border-t border-borderMain flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                            {{ substr($ticket->engineer?->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-textMuted font-semibold text-[11px] truncate">{{ $ticket->engineer?->name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if(auth()->user()->isEngineer() && $ticket->engineer_id === auth()->id() && !$ticket->is_started)
                            <form action="{{ route('complaints.start', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm flex items-center space-x-1 active:scale-95 transition">
                                    <i data-lucide="play" class="w-3 h-3"></i>
                                    <span>Start</span>
                                </button>
                            </form>
                        @elseif(auth()->user()->isEngineer() && $ticket->engineer_id === auth()->id() && $ticket->status === 'in_progress')
                            <a href="{{ route('billing.create', ['complaint_id' => $ticket->id]) }}" class="px-3 py-1.5 rounded-xl bg-accent hover:bg-amber-400 text-primary font-black text-[11px] shadow-sm flex items-center space-x-1 active:scale-95 transition">
                                <i data-lucide="receipt" class="w-3 h-3"></i>
                                <span>Bill</span>
                            </a>
                        @endif

                        <a href="{{ route('complaints.pdf', $ticket) }}" title="Download Job Sheet PDF" class="px-2 py-1.5 rounded-xl border border-borderMain hover:bg-slate-100 text-primary font-bold text-[11px] flex items-center space-x-1 transition">
                            <i data-lucide="file-down" class="w-3 h-3"></i>
                            <span class="hidden sm:inline">PDF</span>
                        </a>

                        <a href="{{ route('complaints.show', $ticket) }}" class="px-3 py-1.5 rounded-xl bg-bg hover:bg-slate-200 text-textMain font-bold text-[11px] flex items-center space-x-1 transition">
                            <span>Details</span>
                            <i data-lucide="chevron-right" class="w-3 h-3 text-textMuted"></i>
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-surface rounded-3xl p-12 text-center text-textMuted border border-borderMain">
                <i data-lucide="wrench" class="w-12 h-12 mx-auto text-secondary/40 mb-3"></i>
                <h4 class="font-bold text-base text-textMain">No tickets found</h4>
                <p class="text-xs text-textMuted mt-1">Try clearing your filters or search keywords.</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $complaints->links() }}
    </div>

</div>
@endsection
