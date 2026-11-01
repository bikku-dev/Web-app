@extends('layouts.app')

@section('title', 'Service Types Master')
@section('page_title', 'Service Classifications Master')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-textMain">Service Types Master</h1>
            <p class="text-xs text-textMuted mt-0.5">Configure servicing categories such as Breakdown, Routine AMC, Installation, and Overhaul</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('masters.areas') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-primary"></i>
                <span>Areas & Cities</span>
            </a>
            <a href="{{ route('masters.products') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="cpu" class="w-3.5 h-3.5 text-secondary"></i>
                <span>Machinery Types</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Form -->
        <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain p-5 sm:p-6 shadow-sm space-y-4 h-fit">
            <div class="border-b border-borderMain pb-3">
                <h3 class="text-sm font-black text-textMain flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-accent flex items-center justify-center">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </div>
                    <span>Add Service Category</span>
                </h3>
                <p class="text-xs text-textMuted mt-0.5">Define service contract classification</p>
            </div>

            <form action="{{ route('masters.services.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Service Type Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="e.g. Breakdown & Emergency Repair" 
                           class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Scope Description
                    </label>
                    <textarea name="description" rows="3" placeholder="Urgent on-site restoration of machinery downtime, spare parts fitting..." 
                              class="w-full text-xs font-medium p-3.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none resize-none leading-relaxed"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 sm:py-3 bg-accent hover:bg-amber-500 text-primary font-black text-xs rounded-xl shadow-md transition-colors flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add Service Type</span>
                </button>
            </form>
        </div>

        <!-- Service Types List -->
        <div class="lg:col-span-2 bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                <span class="text-xs font-black text-accent uppercase tracking-wider flex items-center space-x-1.5">
                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-secondary"></i>
                    <span>Configured Service Classifications ({{ $serviceTypes->count() }})</span>
                </span>
            </div>

            <div class="divide-y divide-borderMain">
                @forelse($serviceTypes as $st)
                <div class="p-4 sm:p-5 flex items-start justify-between gap-4 hover:bg-bg/40 transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-textMain">{{ $st->name }}</span>
                            @if($st->is_active)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">Inactive</span>
                            @endif
                        </div>
                        <p class="text-xs text-textMuted">{{ $st->description ?? 'Industrial field service category' }}</p>
                        <p class="text-[11px] font-bold text-secondary">
                            <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-0.5"></i> {{ $st->complaints_count }} service tickets associated
                        </p>
                    </div>

                    <form action="{{ route('masters.services.toggle', $st) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl border border-borderMain text-xs font-bold {{ $st->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }} transition-colors shadow-sm">
                            {{ $st->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-textMuted">No service types found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
