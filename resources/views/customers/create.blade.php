@extends('layouts.app')

@section('title', 'Add Customer | Bhumi Technocare')

@section('content')
<div class="w-full space-y-5 pb-24 lg:pb-6">
    <!-- Breadcrumb & Top Header -->
    <div class="flex items-center justify-between pb-3 border-b border-app-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-primary-text flex items-center gap-2">
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </span>
                Add Customer
            </h1>
            <p class="text-[11px] sm:text-xs text-secondary-text mt-0.5">Quickly register customer details & location</p>
        </div>

        <div class="hidden sm:flex items-center gap-2">
            <a href="{{ route('customers.index') }}" class="px-3.5 py-1.5 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="customerForm" class="px-5 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="check" class="w-4 h-4"></i> Save Customer
            </button>
        </div>
    </div>

    <!-- Main Form Layout -->
    <form id="customerForm" action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        @csrf

        <!-- LEFT COLUMN: Primary & Optional Details (8 Columns on Desktop) -->
        <div class="lg:col-span-8 space-y-4">
            <!-- 1. Quick Add Card (Essential Details) -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-app-border bg-app-bg/30 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-secondary"></i> Quick Details
                    </h3>
                    <span class="text-[11px] text-secondary-text font-medium"><span class="text-app-error">*</span> Required</span>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Customer Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Customer Name <span class="text-app-error">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Enter customer full name" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 px-3">
                            @error('name')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Mobile Number <span class="text-app-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-secondary-text font-medium">+91</span>
                                <input type="tel" name="contact_no" value="{{ old('contact_no') }}" required placeholder="Enter 10-digit mobile number" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 pl-11 pr-3">
                            </div>
                            @error('contact_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Industrial Area -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-primary-text">Industrial Area</label>
                                <button type="button" onclick="openQuickAreaModal()" class="text-[11px] text-secondary font-bold hover:underline flex items-center gap-0.5">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add
                                </button>
                            </div>
                            <select id="customer_area_select" name="area_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 px-3">
                                <option value="">-- Select Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-primary-text">City</label>
                                <button type="button" onclick="openQuickCityModal()" class="text-[11px] text-secondary font-bold hover:underline flex items-center gap-0.5">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add
                                </button>
                            </div>
                            <select id="customer_city_select" name="city_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 px-3">
                                <option value="">-- Select City --</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Street Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Workshop / Factory Street Address
                            </label>
                            <textarea name="address" rows="2" placeholder="Plot No, Road No, Landmark..." class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary p-2.5">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Collapsible: More Details (Optional) -->
            @php
                $hasMoreData = old('firm_id') || old('department') || old('remarks') || $errors->has('firm_id') || $errors->has('department') || $errors->has('photo');
            @endphp
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <button type="button" onclick="toggleMoreDetails()" class="w-full px-4 py-3.5 bg-app-bg/20 hover:bg-app-bg/40 flex items-center justify-between transition-colors">
                    <span class="text-xs font-bold text-primary flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-secondary"></i>
                        <span>More Details (Company, Department, Remarks)</span>
                        <span class="text-[10px] font-normal text-secondary-text bg-app-bg px-2 py-0.5 rounded-full">Optional</span>
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-secondary font-semibold">
                        <span id="moreDetailsToggleText">{{ $hasMoreData ? 'Hide' : 'Add More' }}</span>
                        <i id="moreDetailsChevron" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 {{ $hasMoreData ? 'rotate-180' : '' }}"></i>
                    </span>
                </button>

                <div id="moreDetailsSection" class="{{ $hasMoreData ? '' : 'hidden' }} p-4 sm:p-5 border-t border-app-border space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Linked Firm -->
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Linked Company / Firm
                            </label>
                            <select name="firm_id" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 px-3">
                                <option value="">-- No Firm Linked (Independent) --</option>
                                @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ (old('firm_id', request('firm_id')) == $org->id) ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department / Designation -->
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Department / Designation
                            </label>
                            <input type="text" name="department" value="{{ old('department') }}" placeholder="e.g. Maintenance Manager" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary py-2.5 px-3">
                        </div>

                        <!-- Operational Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-primary-text mb-1">
                                Visiting Hours & Special Notes
                            </label>
                            <textarea name="remarks" rows="2" placeholder="e.g. Call before visiting; Gate pass needed..." class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-1 focus:ring-secondary p-2.5">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Photo Upload & Guidelines (4 Columns on Desktop) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Profile Photo Upload -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-4 space-y-3">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-secondary"></i> Visiting Card / Photo
                </h3>
                <div class="border border-dashed border-app-border hover:border-secondary/50 rounded-lg p-3 text-center bg-app-bg/30 transition-colors">
                    <i data-lucide="upload-cloud" class="w-6 h-6 mx-auto text-secondary-text mb-1"></i>
                    <p class="text-[11px] text-secondary-text mb-2">Upload card or photo (Optional)</p>
                    <input type="file" name="photo" accept="image/*" class="w-full text-[11px] text-secondary-text file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
                @error('photo')<p class="text-xs text-app-error">{{ $message }}</p>@enderror
            </div>

            <!-- Desktop Action Box -->
            <div class="hidden lg:block bg-surface rounded-xl border border-app-border shadow-sm p-4 space-y-2">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Customer
                </button>
                <a href="{{ route('customers.index') }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Cancel
                </a>
            </div>
        </div>

        <!-- MOBILE STICKY ACTION BAR (Visible only on mobile/tablet) -->
        <div class="block lg:hidden fixed bottom-0 left-0 right-0 p-3 bg-white/95 backdrop-blur-md border-t border-app-border z-40 shadow-lg">
            <div class="flex items-center gap-2.5 max-w-lg mx-auto">
                <a href="{{ route('customers.index') }}" class="w-1/3 py-2.5 text-center rounded-lg border border-app-border text-xs font-semibold text-secondary-text hover:bg-app-bg">
                    Cancel
                </a>
                <button type="submit" class="w-2/3 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Customer
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Quick Add Area Modal -->
<div id="quickAreaModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface rounded-2xl border border-app-border p-6 w-full max-w-sm shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-app-border">
            <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-secondary"></i> Add Industrial Area
            </h3>
            <button type="button" onclick="closeQuickAreaModal()" class="text-secondary-text hover:text-primary-text text-lg leading-none">&times;</button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Area Name *</label>
                <input type="text" id="quickAreaName" placeholder="e.g. Pandesara GIDC" class="w-full text-xs rounded-lg border-app-border bg-white p-2.5 outline-none focus:ring-1 focus:ring-secondary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeQuickAreaModal()" class="px-3 py-1.5 rounded-lg border border-app-border text-xs font-semibold text-secondary-text">Cancel</button>
                <button type="button" onclick="submitQuickArea()" class="px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold">Save Area</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add City Modal -->
<div id="quickCityModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface rounded-2xl border border-app-border p-6 w-full max-w-sm shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-app-border">
            <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-secondary"></i> Add Operating City
            </h3>
            <button type="button" onclick="closeQuickCityModal()" class="text-secondary-text hover:text-primary-text text-lg leading-none">&times;</button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Select Area *</label>
                <select id="quickCityAreaSelect" class="w-full text-xs rounded-lg border-app-border bg-white p-2.5 outline-none focus:ring-1 focus:ring-secondary">
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">City Name *</label>
                <input type="text" id="quickCityName" placeholder="e.g. Surat" class="w-full text-xs rounded-lg border-app-border bg-white p-2.5 outline-none focus:ring-1 focus:ring-secondary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeQuickCityModal()" class="px-3 py-1.5 rounded-lg border border-app-border text-xs font-semibold text-secondary-text">Cancel</button>
                <button type="button" onclick="submitQuickCity()" class="px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold">Save City</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleMoreDetails() {
    const el = document.getElementById('moreDetailsSection');
    const icon = document.getElementById('moreDetailsChevron');
    const text = document.getElementById('moreDetailsToggleText');
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
        if (text) text.innerText = 'Hide Details';
    } else {
        el.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
        if (text) text.innerText = 'Add More';
    }
}

function openQuickAreaModal() {
    document.getElementById('quickAreaName').value = '';
    document.getElementById('quickAreaModal').classList.remove('hidden');
}
function closeQuickAreaModal() {
    document.getElementById('quickAreaModal').classList.add('hidden');
}
function openQuickCityModal() {
    document.getElementById('quickCityName').value = '';
    const currentArea = document.getElementById('customer_area_select').value;
    if (currentArea) {
        document.getElementById('quickCityAreaSelect').value = currentArea;
    }
    document.getElementById('quickCityModal').classList.remove('hidden');
}
function closeQuickCityModal() {
    document.getElementById('quickCityModal').classList.add('hidden');
}

function submitQuickArea() {
    const name = document.getElementById('quickAreaName').value.trim();
    if (!name) return alert('Please enter area name');
    
    fetch('{{ route('masters.areas.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name: name })
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success' && res.data) {
            const select = document.getElementById('customer_area_select');
            const opt = new Option(res.data.name, res.data.id, true, true);
            select.add(opt);

            const quickSelect = document.getElementById('quickCityAreaSelect');
            quickSelect.add(new Option(res.data.name, res.data.id, true, true));

            closeQuickAreaModal();
        } else {
            alert(res.message || 'Error creating area');
        }
    })
    .catch(e => alert('Failed to create area: ' + e));
}

function submitQuickCity() {
    const areaId = document.getElementById('quickCityAreaSelect').value;
    const name = document.getElementById('quickCityName').value.trim();
    if (!areaId) return alert('Please select area');
    if (!name) return alert('Please enter city name');

    fetch('{{ route('masters.cities.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ area_id: areaId, name: name })
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success' && res.data) {
            const select = document.getElementById('customer_city_select');
            const opt = new Option(res.data.name, res.data.id, true, true);
            select.add(opt);
            closeQuickCityModal();
        } else {
            alert(res.message || 'Error creating city');
        }
    })
    .catch(e => alert('Failed to create city: ' + e));
}
</script>
@endpush
@endsection
