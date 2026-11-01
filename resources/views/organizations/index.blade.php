@extends('layouts.app')

@section('title', 'Client Organizations & Firms')
@section('page_title', 'B2B Client Organizations & Manufacturing Units')

@section('content')
<div class="space-y-6" x-data="{ filterModalOpen: false }">

    <!-- TOOLBAR -->
    <div class="bg-surface rounded-2xl p-3.5 sm:p-4 border border-borderMain shadow-sm flex items-center justify-between gap-2.5">
        <form action="{{ route('organizations.index') }}" method="GET" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-textMuted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, GST, contact person..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
            </div>
            <button type="submit" class="hidden sm:inline-flex px-3.5 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-sm">
                Search
            </button>
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Open Filter Modal Button -->
            <button type="button" @click="filterModalOpen = true" class="px-3 sm:px-3.5 py-2 rounded-xl border border-borderMain bg-bg hover:bg-slate-200 text-xs font-bold text-textMain transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-secondary"></i>
                <span>Filters</span>
                @if(request()->has('area_id') && request('area_id') != '')
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                @endif
            </button>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('organizations.create') }}" class="px-3 sm:px-4 py-2 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-accent"></i>
                    <span class="hidden sm:inline">Add Client Firm</span>
                    <span class="sm:hidden">Add Firm</span>
                </a>
            @endif
        </div>
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
                        <h3 class="font-bold text-base text-textMain">Filter Client Firms</h3>
                        <p class="text-xs text-textMuted">Filter by industrial area or firm details</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('organizations.index') }}" method="GET" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">Search Keyword</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-textMuted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Company name, GST, contact person..." class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">Industrial Zone / Area</label>
                    <select name="area_id" class="w-full px-3 py-2.5 text-xs rounded-xl border border-borderMain bg-bg outline-none focus:border-secondary">
                        <option value="">All Industrial Areas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-borderMain gap-3">
                    <a href="{{ route('organizations.index') }}" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-semibold text-textMuted hover:text-textMain hover:bg-bg transition text-center">
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

    <!-- FIRMS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($organizations as $org)
            <div class="bg-surface rounded-3xl border border-borderMain shadow-sm p-6 flex flex-col justify-between hover:border-secondary transition">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary font-black text-lg flex items-center justify-center">
                                <i data-lucide="building-2" class="w-6 h-6 text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-textMain leading-tight">{{ $org->name }}</h3>
                                <p class="text-xs text-secondary font-medium">{{ $org->area?->name ?? 'Surat Industrial' }}</p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $org->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $org->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs text-textMuted py-3 border-y border-borderMain">
                        <div class="flex items-center justify-between">
                            <span>Contact Person:</span>
                            <strong class="text-textMain">{{ $org->contact_person ?? 'N/A' }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phone:</span>
                            <strong class="text-textMain">{{ $org->contact_no ?? 'N/A' }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>GST Number:</span>
                            <span class="font-mono text-textMain font-bold">{{ $org->gst_no ?? 'Unregistered' }}</span>
                        </div>
                        <div>
                            <span class="text-textMuted block">Plant Address:</span>
                            <p class="text-textMain truncate">{{ $org->address ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 py-3 text-center text-xs">
                        <div class="p-2 rounded-xl bg-bg border border-borderMain">
                            <div class="text-sm font-black text-primary">{{ $org->customers_count }}</div>
                            <div class="text-[10px] text-textMuted">Contacts</div>
                        </div>
                        <div class="p-2 rounded-xl bg-bg border border-borderMain">
                            <div class="text-sm font-black text-secondary">{{ $org->complaints_count }}</div>
                            <div class="text-[10px] text-textMuted">Service Calls</div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-borderMain flex items-center justify-between gap-2">
                    <a href="{{ route('organizations.show', $org) }}" class="flex-1 py-2 rounded-xl bg-bg hover:bg-slate-200 text-center text-xs font-bold text-textMain transition">
                        View Firm History
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('organizations.edit', $org) }}" class="p-2 rounded-xl border border-borderMain hover:bg-bg text-textMuted transition" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('organizations.toggle', $org) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl border border-borderMain hover:bg-bg {{ $org->is_active ? 'text-rose-600' : 'text-emerald-600' }} transition">
                                <i data-lucide="{{ $org->is_active ? 'power' : 'check' }}" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface rounded-3xl p-12 text-center text-textMuted border border-borderMain">
                No organizations found matching search criteria.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $organizations->links() }}
    </div>

</div>
@endsection
