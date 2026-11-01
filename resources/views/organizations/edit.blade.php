@extends('layouts.app')

@section('title', 'Edit ' . $organization->name . ' | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-secondary/15 text-secondary flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </span>
                Edit Firm: {{ $organization->name }}
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Update business details, GSTIN, and plant address</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.show', $organization) }}" class="px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="orgEditForm" class="px-6 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Update Organization
            </button>
        </div>
    </div>

    <!-- Main Full-Width Form Layout -->
    <form id="orgEditForm" action="{{ route('organizations.update', $organization) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- LEFT COLUMN: 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Company Legal Information -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 text-secondary"></i> Legal & Tax Identification
                    </h3>
                    <span class="text-[11px] text-secondary-text">Firm ID: #ORG-{{ str_pad($organization->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Company / Firm Legal Name <span class="text-app-error">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $organization->name) }}" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium">
                            @error('name')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                GSTIN / Tax Identification
                            </label>
                            <input type="text" name="gst_no" value="{{ old('gst_no', $organization->gst_no) }}" class="w-full text-xs rounded-lg border-app-border bg-white uppercase font-mono focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('gst_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Primary Office Phone / Landline
                            </label>
                            <input type="tel" name="contact_no" value="{{ old('contact_no', $organization->contact_no) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('contact_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Contact Person -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-accent"></i> Key Maintenance Contact / Factory Supervisor
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Contact Person Name</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $organization->contact_person) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Direct Mobile Number</label>
                            <input type="tel" name="contact_no" value="{{ old('contact_no', $organization->contact_no) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plant Location & Address -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-primary"></i> Factory Premises & Operating Zone
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Industrial Area / GIDC Zone</label>
                            <select name="area_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select Industrial Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id', $organization->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">City</label>
                            <select name="city_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">-- Select City --</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', $organization->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Postal Pincode</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $organization->pincode) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Full Factory / Workshop Street Address</label>
                            <textarea name="address" rows="3" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('address', $organization->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Machinery Installed & Remarks -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-6 space-y-2">
                <label class="block text-xs font-semibold text-primary-text">Installed Equipment & Plant Notes</label>
                <textarea name="remarks" rows="2" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('remarks', $organization->remarks) }}</textarea>
            </div>
        </div>

        <!-- RIGHT COLUMN: 4 Columns -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Factory Premises Photo -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4 text-secondary"></i> Factory / Gate Photo
                </h3>

                @if($organization->photo)
                <div class="flex items-center gap-3 p-3 bg-app-bg rounded-lg border border-app-border">
                    <img src="{{ Storage::url($organization->photo) }}" class="w-14 h-14 rounded-lg object-cover border border-app-border">
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

            <!-- Submission Actions -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Update Organization
                </button>
                <a href="{{ route('organizations.show', $organization) }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Discard Changes
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
