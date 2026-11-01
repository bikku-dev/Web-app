@extends('layouts.app')

@section('title', 'Machinery & Equipment Types')
@section('page_title', 'Machinery & Product Types')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-textMain">Machinery & Product Types</h1>
            <p class="text-xs text-textMuted mt-0.5">Configure industrial machinery, equipment models, and asset categories</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('masters.areas') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-primary"></i>
                <span>Areas & Cities</span>
            </a>
            <a href="{{ route('masters.services') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="settings" class="w-3.5 h-3.5 text-accent"></i>
                <span>Service Types</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Form -->
        <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain p-5 sm:p-6 shadow-sm space-y-4 h-fit">
            <div class="border-b border-borderMain pb-3">
                <h3 class="text-sm font-black text-textMain flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-secondary flex items-center justify-center">
                        <i data-lucide="cpu" class="w-4 h-4"></i>
                    </div>
                    <span>Add Equipment Category</span>
                </h3>
                <p class="text-xs text-textMuted mt-0.5">Register a new machine type serviced by Bhumi Technocare</p>
            </div>

            <form action="{{ route('masters.products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Equipment Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="e.g. CNC VMC 5-Axis Milling Machine" 
                           class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Technical Scope / Description
                    </label>
                    <textarea name="description" rows="3" placeholder="Includes spindle maintenance, controller servicing, axis calibration..." 
                              class="w-full text-xs font-medium p-3.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none resize-none leading-relaxed"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 sm:py-3 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-colors flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add Machine Type</span>
                </button>
            </form>
        </div>

        <!-- Machinery List -->
        <div class="lg:col-span-2 bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                <span class="text-xs font-black text-primary uppercase tracking-wider flex items-center space-x-1.5">
                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-secondary"></i>
                    <span>Registered Industrial Machinery ({{ $productTypes->count() }})</span>
                </span>
            </div>

            <div class="divide-y divide-borderMain">
                @forelse($productTypes as $pt)
                <div class="p-4 sm:p-5 flex items-start justify-between gap-4 hover:bg-bg/40 transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-textMain">{{ $pt->name }}</span>
                            @if($pt->is_active)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">Inactive</span>
                            @endif
                        </div>
                        <p class="text-xs text-textMuted">{{ $pt->description ?? 'Industrial machinery maintenance scope' }}</p>
                        <p class="text-[11px] font-bold text-secondary">
                            <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-0.5"></i> {{ $pt->complaints_count }} service tickets logged
                        </p>
                    </div>

                    <form action="{{ route('masters.products.toggle', $pt) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl border border-borderMain text-xs font-bold {{ $pt->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }} transition-colors shadow-sm">
                            {{ $pt->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-textMuted">No equipment types found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
