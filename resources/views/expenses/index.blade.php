@extends('layouts.app')

@section('title', 'Field Expense Claims')
@section('page_title', 'Field Expense & Parts Reimbursement')

@section('content')
<div class="space-y-6" x-data="{ newExpenseOpen: false, reviewModalOpen: false, currentExpenseId: null, reviewAction: 'approved', filterModalOpen: false }">

    <!-- METRICS ROW -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-surface p-4 rounded-3xl border border-borderMain shadow-sm">
            <span class="text-[11px] font-bold text-textMuted uppercase">Approved Total</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">₹{{ number_format($totalApproved, 2) }}</div>
            <div class="text-xs text-textMuted">Reimbursed to engineers</div>
        </div>

        <div class="bg-surface p-4 rounded-3xl border {{ $totalPending > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-borderMain' }} shadow-sm">
            <span class="text-[11px] font-bold text-amber-700 uppercase">Pending Review</span>
            <div class="text-2xl font-black text-amber-600 mt-1">₹{{ number_format($totalPending, 2) }}</div>
            <div class="text-xs text-textMuted">Claims awaiting admin</div>
        </div>

        <div class="bg-surface p-4 rounded-3xl border border-borderMain shadow-sm">
            <span class="text-[11px] font-bold text-textMuted uppercase">Travel & Fuel</span>
            <div class="text-2xl font-black text-textMain mt-1">₹{{ number_format($totalTravel, 2) }}</div>
            <div class="text-xs text-textMuted">On-site conveyance</div>
        </div>

        <div class="bg-surface p-4 rounded-3xl border border-borderMain shadow-sm">
            <span class="text-[11px] font-bold text-textMuted uppercase">Components & Parts</span>
            <div class="text-2xl font-black text-textMain mt-1">₹{{ number_format($totalComponents, 2) }}</div>
            <div class="text-xs text-textMuted">Emergency hardware bought</div>
        </div>
    </div>

    <!-- COMPACT TOOLBAR & FILTER MODAL TRIGGER -->
    @php
        $activeFilterCount = (request('status') ? 1 : 0) + (request('expense_type') ? 1 : 0) + (request('search') ? 1 : 0);
    @endphp
    <div class="bg-surface rounded-2xl p-3 border border-borderMain shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="{{ route('expenses.index') }}" method="GET" class="flex-1 flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('expense_type'))
                <input type="hidden" name="expense_type" value="{{ request('expense_type') }}">
            @endif
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-textMuted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search details or staff..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-textMain text-xs font-semibold shrink-0">
                Search
            </button>
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Filter Modal Trigger Button -->
            <button type="button" @click="filterModalOpen = true" class="relative flex-1 sm:flex-initial px-4 py-2 rounded-xl border border-borderMain bg-surface hover:bg-bg text-textMain text-xs font-bold flex items-center justify-center gap-2 transition shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-primary"></i>
                <span>Filters</span>
                @if($activeFilterCount > 0)
                    <span class="w-5 h-5 rounded-full bg-accent text-primary text-[10px] font-black flex items-center justify-center">{{ $activeFilterCount }}</span>
                @endif
            </button>

            <button type="button" @click="newExpenseOpen = true" class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
                <i data-lucide="plus-circle" class="w-4 h-4 text-accent"></i>
                <span>Submit Claim</span>
            </button>
        </div>
    </div>

    <!-- EXPENSES CARDS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($expenses as $exp)
            <div class="bg-surface rounded-2xl border border-borderMain p-5 shadow-sm hover:border-secondary hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Header: Engineer & Category -->
                    <div class="flex items-start justify-between gap-2 pb-3 border-b border-borderMain">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary font-black text-xs flex items-center justify-center shrink-0">
                                {{ substr($exp->engineer?->name ?? 'E', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-textMain truncate">{{ $exp->engineer?->name ?? 'Staff' }}</h4>
                                <span class="text-[10px] text-textMuted">{{ $exp->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 bg-slate-100 text-slate-800">
                            {{ $exp->expense_type }}
                        </span>
                    </div>

                    <!-- Amount & Description -->
                    <div class="py-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-textMuted">Claim Amount:</span>
                            <span class="font-black text-base text-textMain">₹{{ number_format($exp->amount, 2) }}</span>
                        </div>

                        <div>
                            <span class="text-textMuted block mb-0.5">Purpose / Details:</span>
                            <p class="text-textMain font-medium leading-relaxed bg-bg/50 p-2.5 rounded-xl border border-borderMain text-xs line-clamp-3">
                                {{ $exp->details }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-textMuted">Linked Job:</span>
                            @if($exp->complaint)
                                <a href="{{ route('complaints.show', $exp->complaint) }}" class="text-secondary font-bold hover:underline flex items-center gap-1">
                                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                                    <span>{{ $exp->complaint->ticket_number }}</span>
                                </a>
                            @else
                                <span class="text-textMuted italic">General Expense</span>
                            @endif
                        </div>

                        @if($exp->photo)
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-textMuted">Voucher Proof:</span>
                                <a href="{{ asset('storage/' . $exp->photo) }}" target="_blank" class="inline-flex items-center space-x-1 text-secondary font-bold hover:underline">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                    <span>View Receipt</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer: Status & Review Action -->
                <div class="pt-3 border-t border-borderMain flex items-center justify-between text-xs gap-2">
                    <div>
                        @if($exp->status === 'approved')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">APPROVED</span>
                        @elseif($exp->status === 'declined')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">DECLINED</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">PENDING</span>
                        @endif
                        @if($exp->admin_remarks)
                            <div class="text-[10px] text-textMuted mt-0.5 italic truncate max-w-[150px]">{{ $exp->admin_remarks }}</div>
                        @endif
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div>
                            @if($exp->status === 'pending')
                                <button type="button" @click="currentExpenseId = {{ $exp->id }}; reviewModalOpen = true; $el.scrollIntoView({behavior: 'smooth'})" class="px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/95 transition shadow-sm">
                                    Audit Review
                                </button>
                            @else
                                <span class="text-textMuted text-[10px]">By {{ $exp->reviewer?->name ?? 'Admin' }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-textMuted bg-surface rounded-2xl border border-borderMain">
                <i data-lucide="receipt" class="w-10 h-10 mx-auto text-textMuted/40 mb-2"></i>
                <p class="font-medium text-xs">No expense claims recorded matching your filters.</p>
            </div>
        @endforelse
    </div>

    @if($expenses->hasPages())
        <div class="bg-surface rounded-2xl border border-borderMain p-4">
            {{ $expenses->links() }}
        </div>
    @endif

    <!-- IN-PAGE SUBMIT CLAIM FORM (COLLAPSIBLE) -->
    <div x-show="newExpenseOpen" x-cloak class="bg-surface rounded-2xl border-2 border-secondary shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-app-border">
            <div>
                <h3 class="font-bold text-sm text-primary-text flex items-center gap-2">
                    <i data-lucide="receipt-text" class="w-4 h-4 text-secondary"></i> Submit Field Expense Claim
                </h3>
                <p class="text-xs text-secondary-text">Conveyance, fuel, bus/train ticket, or on-site spare part purchase for field operations</p>
            </div>
            <button type="button" @click="newExpenseOpen = false" class="text-secondary-text hover:text-primary-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Category <span class="text-app-error">*</span></label>
                <select name="expense_type" required class="w-full text-xs rounded-lg border border-app-border bg-white focus:ring-secondary py-2 px-3">
                    <option value="Travel">Travel & Fuel</option>
                    <option value="Component">Spare Component / Hardware</option>
                    <option value="Other">Other Operational</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Amount (₹) <span class="text-app-error">*</span></label>
                <input type="number" step="0.01" name="amount" required placeholder="350.00" class="w-full text-xs font-bold rounded-lg border border-app-border bg-white focus:ring-secondary py-2 px-3">
            </div>

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Link with Ticket (Optional)</label>
                <select name="complaint_id" class="w-full text-xs rounded-lg border border-app-border bg-white focus:ring-secondary py-2 px-3">
                    <option value="">General (Not Job Specific)</option>
                    @foreach($activeComplaints as $ac)
                        <option value="{{ $ac->id }}">{{ $ac->ticket_number }} - {{ $ac->customer?->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Receipt / Bill Image</label>
                <input type="file" name="photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-primary-text mb-1">Purpose / Details <span class="text-app-error">*</span></label>
                <textarea name="details" rows="2" required placeholder="Fuel for Surat GIDC visit or emergency sensor purchase..." class="w-full text-xs rounded-lg border border-app-border bg-white focus:ring-secondary p-3"></textarea>
            </div>

            <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-app-border">
                <button type="button" @click="newExpenseOpen = false" class="px-4 py-2 rounded-lg border border-app-border text-xs font-medium text-secondary-text hover:bg-app-bg">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i> Submit Claim
                </button>
            </div>
        </form>
    </div>

    <!-- IN-PAGE ADMIN REVIEW FORM (ACCORDION) -->
    <div x-show="reviewModalOpen" x-cloak class="bg-surface rounded-2xl border-2 border-primary shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-app-border">
            <div>
                <h3 class="font-bold text-sm text-primary-text flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-primary"></i> Review Field Expense Claim
                </h3>
                <p class="text-xs text-secondary-text">Approve reimbursement or decline with required audit remarks</p>
            </div>
            <button type="button" @click="reviewModalOpen = false" class="text-secondary-text hover:text-primary-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form :action="'{{ url('/expenses') }}/' + currentExpenseId + '/review'" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1.5">Decision</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="p-2.5 rounded-lg border border-app-border flex items-center justify-center gap-1.5 cursor-pointer font-bold text-xs" :class="reviewAction === 'approved' ? 'bg-emerald-50 border-emerald-500 text-emerald-800' : 'bg-app-bg text-secondary-text'">
                        <input type="radio" name="status" value="approved" x-model="reviewAction" class="hidden">
                        <span>Approve</span>
                    </label>
                    <label class="p-2.5 rounded-lg border border-app-border flex items-center justify-center gap-1.5 cursor-pointer font-bold text-xs" :class="reviewAction === 'declined' ? 'bg-rose-50 border-rose-500 text-rose-800' : 'bg-app-bg text-secondary-text'">
                        <input type="radio" name="status" value="declined" x-model="reviewAction" class="hidden">
                        <span>Decline</span>
                    </label>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-primary-text mb-1.5">
                    Admin Remarks <span x-show="reviewAction === 'declined'" class="text-app-error">*</span>
                </label>
                <input type="text" name="admin_remarks" placeholder="e.g. Approved per conveyance policy, or missing signed bill memo..." class="w-full px-3 py-2.5 rounded-lg border border-app-border bg-white text-xs">
            </div>

            <div class="sm:col-span-3 flex items-center justify-end gap-2 pt-2 border-t border-app-border">
                <button type="button" @click="reviewModalOpen = false" class="px-4 py-2 rounded-lg border border-app-border text-xs font-medium text-secondary-text hover:bg-app-bg">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm">
                    Save Decision
                </button>
            </div>
        </form>
    </div>

    <!-- FILTER POPUP MODAL -->
    <div x-show="filterModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-surface rounded-3xl max-w-md w-full p-6 border border-borderMain shadow-2xl relative"
             @click.away="filterModalOpen = false"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between pb-4 border-b border-borderMain">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-textMain">Filter Expense Claims</h3>
                        <p class="text-[11px] text-textMuted">Narrow down reimbursement claims</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg hover:bg-bg transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('expenses.index') }}" method="GET" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">Search Keywords</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search details or staff..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">Review Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                        <option value="">All Review Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">Expense Category</label>
                    <select name="expense_type" class="w-full px-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                        <option value="">All Categories</option>
                        <option value="Travel" {{ request('expense_type') == 'Travel' ? 'selected' : '' }}>Travel</option>
                        <option value="Component" {{ request('expense_type') == 'Component' ? 'selected' : '' }}>Component</option>
                        <option value="Other" {{ request('expense_type') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-borderMain flex items-center justify-between gap-3">
                    @if(request()->hasAny(['search', 'status', 'expense_type']))
                        <a href="{{ route('expenses.index') }}" class="px-4 py-2 rounded-xl border border-borderMain text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
                            Clear Filters
                        </a>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="filterModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-textMuted hover:text-textMain">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary/95 text-white text-xs font-bold shadow-md transition">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
