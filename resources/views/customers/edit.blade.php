@extends('layouts.app')

@section('title', 'Edit ' . $customer->name . ' | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-secondary/15 text-secondary flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </span>
                Edit Customer Contact: {{ $customer->name }}
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Update personal details, company designation, or plant address</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="customerEditForm" class="px-6 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Update Contact
            </button>
        </div>
    </div>

    <!-- Main Full-Width Form Layout -->
    <form id="customerEditForm" action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- LEFT COLUMN: Main Form Inputs (8 Columns) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Card 1: Core Contact & Company Association -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-secondary"></i> Personal & Organizational Details
                    </h3>
                    <span class="text-[11px] text-secondary-text">Registered ID: #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Full Contact Name <span class="text-app-error">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('name')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Mobile / Phone Number <span class="text-app-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-secondary-text font-medium">+91</span>
                                <input type="tel" name="contact_no" value="{{ old('contact_no', $customer->contact_no) }}" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 pl-11 pr-3">
                            </div>
                            @error('contact_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Linked Company / Manufacturing Firm
                            </label>
                            <select name="firm_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Independent Client / No Firm Linked --</option>
                                @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ old('firm_id', $customer->firm_id) == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }} ({{ $org->city->name ?? 'Industrial' }})
                                </option>
                                @endforeach
                            </select>
                            @error('firm_id')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Department / Designation
                            </label>
                            <input type="text" name="department" value="{{ old('department', $customer->department) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('department')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Plant & Geographic Location -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-accent"></i> Location & Workshop Coordinates
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Industrial Area / GIDC Zone
                            </label>
                            <select name="area_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select Industrial Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id', $customer->area_id) == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Operating City
                            </label>
                            <select name="city_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select City --</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', $customer->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Workshop / Factory Street Address
                            </label>
                            <textarea name="address" rows="3" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('address', $customer->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Internal Remarks & Instructions -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-6 space-y-3">
                <label class="block text-xs font-semibold text-primary-text">
                    Operational Notes & Preferred Visiting Hours
                </label>
                <textarea name="remarks" rows="2" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('remarks', $customer->remarks) }}</textarea>
            </div>
        </div>

        <!-- RIGHT COLUMN: Attachment & Status Sidebar (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Current Photo & Upload -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-secondary"></i> Visiting Card / Photo
                </h3>

                @if($customer->photo)
                <div class="flex items-center gap-3 p-3 bg-app-bg rounded-lg border border-app-border">
                    <img src="{{ Storage::url($customer->photo) }}" class="w-14 h-14 rounded-lg object-cover border border-app-border">
                    <div>
                        <span class="text-xs font-semibold text-primary-text block">Current Photo Saved</span>
                        <span class="text-[10px] text-secondary-text">Upload a new file below to replace it</span>
                    </div>
                </div>
                @endif

                <div class="border-2 border-dashed border-app-border hover:border-secondary/50 rounded-xl p-4 text-center bg-app-bg/50 transition-colors">
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
                @error('photo')<p class="text-xs text-app-error">{{ $message }}</p>@enderror
            </div>

            <!-- Actions Panel -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Update Contact
                </button>
                <a href="{{ route('customers.show', $customer) }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Discard Changes
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
