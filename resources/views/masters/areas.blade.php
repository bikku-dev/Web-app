@extends('layouts.app')

@section('title', 'Geographic Master Data - Areas & Cities')
@section('page_title', 'Areas & Operating Cities Master')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-textMain">Geographic Master Data</h1>
            <p class="text-xs text-textMuted mt-0.5">Configure industrial zones, GIDC clusters, and operating cities</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('masters.products') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="cpu" class="w-3.5 h-3.5 text-secondary"></i>
                <span>Machinery Types</span>
            </a>
            <a href="{{ route('masters.services') }}" class="px-3.5 py-2 rounded-xl border border-borderMain bg-surface text-xs font-bold text-textMuted hover:text-textMain hover:bg-bg transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="settings" class="w-3.5 h-3.5 text-accent"></i>
                <span>Service Types</span>
            </a>
        </div>
    </div>

    <!-- 2 Column Layout: Areas on left, Cities on right -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- INDUSTRIAL AREAS SECTION -->
        <div class="space-y-4">
            <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain p-5 sm:p-6 shadow-sm">
                <h3 class="text-sm font-black text-textMain flex items-center gap-2 mb-3 pb-2 border-b border-borderMain">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                    <span>Add Industrial Area / Zone</span>
                </h3>
                <form action="{{ route('masters.areas.store') }}" method="POST" class="flex flex-col sm:flex-row gap-2.5">
                    @csrf
                    <input type="text" name="name" required placeholder="e.g. Sachin GIDC Industrial Area" 
                           class="flex-1 text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none">
                    <button type="submit" class="px-4 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-colors whitespace-nowrap active:scale-95">
                        + Add Area
                    </button>
                </form>
            </div>

            <!-- Areas List -->
            <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm overflow-hidden">
                <div class="p-4 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                    <span class="text-xs font-black text-primary uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="map" class="w-3.5 h-3.5 text-secondary"></i>
                        <span>Industrial Zones ({{ $areas->count() }})</span>
                    </span>
                </div>
                <div class="divide-y divide-borderMain max-h-[500px] overflow-y-auto">
                    @forelse($areas as $area)
                    <div class="p-4 flex items-center justify-between hover:bg-bg/40 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-textMain">{{ $area->name }}</span>
                                @if($area->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">Inactive</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-textMuted mt-0.5">
                                {{ $area->cities_count }} cities • {{ $area->organizations_count }} client firms
                            </p>
                        </div>
                        <form action="{{ route('masters.areas.toggle', $area) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl border border-borderMain text-xs font-bold {{ $area->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }} transition-colors shadow-sm">
                                {{ $area->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-textMuted">No areas configured yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- CITIES SECTION -->
        <div class="space-y-4">
            <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain p-5 sm:p-6 shadow-sm">
                <h3 class="text-sm font-black text-textMain flex items-center gap-2 mb-3 pb-2 border-b border-borderMain">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-secondary flex items-center justify-center">
                        <i data-lucide="building-2" class="w-4 h-4"></i>
                    </div>
                    <span>Add Operating City</span>
                </h3>
                <form action="{{ route('masters.cities.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @csrf
                    <div>
                        <select name="area_id" required 
                                class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-borderMain bg-bg text-textMain shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none cursor-pointer">
                            <option value="">-- Select Linked Area --</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="name" required placeholder="City name (e.g. Surat)" 
                               class="flex-1 text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-borderMain bg-bg text-textMain placeholder:text-textMuted/60 shadow-sm transition focus:bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none">
                        <button type="submit" class="px-4 py-2.5 bg-secondary hover:bg-secondary/90 text-white text-xs font-bold rounded-xl shadow-md transition-colors whitespace-nowrap active:scale-95">
                            + Add City
                        </button>
                    </div>
                </form>
            </div>

            <!-- Cities List -->
            <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm overflow-hidden">
                <div class="p-4 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                    <span class="text-xs font-black text-secondary uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="buildings" class="w-3.5 h-3.5 text-primary"></i>
                        <span>Cities ({{ $cities->count() }})</span>
                    </span>
                </div>
                <div class="divide-y divide-borderMain max-h-[500px] overflow-y-auto">
                    @forelse($cities as $city)
                    <div class="p-4 flex items-center justify-between hover:bg-bg/40 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-textMain">{{ $city->name }}</span>
                                @if($city->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">Inactive</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-textMuted mt-0.5">
                                Area: <span class="font-bold text-textMain">{{ $city->area->name ?? 'General' }}</span>
                            </p>
                        </div>
                        <form action="{{ route('masters.cities.toggle', $city) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl border border-borderMain text-xs font-bold {{ $city->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }} transition-colors shadow-sm">
                                {{ $city->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-textMuted">No cities configured yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
