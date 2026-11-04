@extends('layouts.app')

@section('title', 'Book Service Call | Bhumi Technocare')
@section('page_title', 'New Service Complaint')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6 py-2 sm:py-4 pb-24" 
     x-data="{
        customerMode: '{{ old('customer_mode', 'existing') }}',
        selectedCustomerId: '{{ old('customer_id', '') }}',
        customerSearch: '',
        customerDropdownOpen: false,
        showNewAreaInput: {{ old('new_area_name') ? 'true' : 'false' }},
        showNewFirmInput: {{ old('new_firm_name') ? 'true' : 'false' }},
        showNewServiceInput: {{ old('new_service_type') ? 'true' : 'false' }},
        customers: {{ Js::from($customers) }},
        selectedCustomer: null,
        predictedIds: {{ Js::from($predictedIds) }},
        selectedProductId: '{{ old('product_type_id', $productTypes->first()?->id ?? '') }}',

        get currentTicketId() {
            return this.predictedIds[this.selectedProductId] || 'C2611001';
        },

        init() {
            this.updateCustomerDetails();
            this.$watch('selectedCustomerId', () => this.updateCustomerDetails());
            if (this.selectedCustomerId) {
                const c = this.customers.find(item => item.id == this.selectedCustomerId);
                if (c) {
                    this.customerSearch = c.name + ' (' + c.contact_no + ')';
                }
            }
        },

        updateCustomerDetails() {
            if (!this.selectedCustomerId) {
                this.selectedCustomer = null;
                return;
            }
            this.selectedCustomer = this.customers.find(c => c.id == this.selectedCustomerId) || null;
        },

        get filteredCustomers() {
            const raw = (this.customerSearch || '').trim();
            const isCurrentSelectedLabel = this.selectedCustomer && (raw === this.selectedCustomer.name + ' (' + this.selectedCustomer.contact_no + ')' || raw === this.selectedCustomer.name);
            if (!raw || isCurrentSelectedLabel) {
                return this.customers.slice(0, 25);
            }
            const q = raw.toLowerCase();
            return this.customers.filter(c => {
                const name = (c.name || '').toLowerCase();
                const phone = (c.contact_no || '').toLowerCase();
                const firm = (c.organization?.name || '').toLowerCase();
                const area = (c.area?.name || '').toLowerCase();
                return name.includes(q) || phone.includes(q) || firm.includes(q) || area.includes(q);
            }).slice(0, 40);
        },

        selectCustomer(c) {
            this.selectedCustomerId = c.id;
            this.selectedCustomer = c;
            this.customerSearch = c.name + ' (' + c.contact_no + ')';
            this.customerDropdownOpen = false;
        },

        clearCustomer() {
            this.selectedCustomerId = '';
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.customerDropdownOpen = true;
        },

        addIssueTag(tag) {
            const textarea = document.getElementById('issue_description');
            if (!textarea) return;
            const current = textarea.value.trim();
            textarea.value = current ? `${current}, ${tag}` : tag;
            textarea.dispatchEvent(new Event('input'));
        },

        setDueDate(daysAhead) {
            const now = new Date();
            now.setDate(now.getDate() + daysAhead);
            now.setHours(18, 0, 0, 0); // Default to 6:00 PM
            const pad = (n) => String(n).padStart(2, '0');
            const formatted = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
            const dueInput = document.getElementById('due_date');
            if (dueInput) {
                dueInput.value = formatted;
            }
        }
     }">

    <!-- Top App Navigation / Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-3 border-b border-borderMain">
        <div class="flex items-center space-x-3">
            <a href="{{ route('complaints.index') }}" 
               class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white border border-borderMain text-xs font-bold text-textMain hover:bg-slate-50 transition active:scale-95 shadow-2xs">
                <i data-lucide="arrow-left" class="w-4 h-4 text-textMuted"></i>
                <span>Back</span>
            </a>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-textMain flex items-center gap-2">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i data-lucide="wrench" class="w-4 h-4 text-secondary"></i>
                    </span>
                    Book Service Call / नई सर्विस शिकायत
                </h1>
                <p class="text-xs text-textMuted mt-0.5 hidden sm:block">
                    Register service ticket with auto product-wise & month-wise Complaint ID
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs font-black uppercase tracking-wider text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20 flex items-center gap-1.5">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-secondary"></i>
                <span>Auto ID: <strong class="font-mono text-secondary" x-text="currentTicketId"></strong></span>
            </span>
        </div>
    </div>

    <!-- Main Responsive Form (Desktop 2-Cols: 7 cols Left + 5 cols Right; Phone: 1-Col Stack) -->
    <form id="serviceTicketForm" action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="customer_mode" :value="customerMode">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            <!-- ================= LEFT COLUMN (7 COLS ON DESKTOP): CUSTOMER & EQUIPMENT ================= -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- CARD 1: CUSTOMER INFORMATION -->
                <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-2xs relative z-20">
                    <div class="p-3.5 sm:p-4 border-b border-borderMain bg-bg/50 rounded-t-2xl sm:rounded-t-3xl flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center font-bold text-xs">
                                1
                            </span>
                            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-800">
                                Customer Information / ग्राहक जानकारी
                            </h2>
                        </div>
                        <span class="text-[10px] text-textMuted font-semibold">* Required</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <!-- Mode Selector Tabs: Existing Customer vs New Customer -->
                        <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl border border-slate-200 gap-1">
                            <button type="button" 
                                    @click="customerMode = 'existing'"
                                    :class="customerMode === 'existing' ? 'bg-white text-primary font-bold shadow-xs border border-slate-200' : 'text-slate-500 font-medium hover:text-slate-800'"
                                    class="py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-1.5 transition">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span>Existing Customer</span>
                            </button>

                            <button type="button" 
                                    @click="customerMode = 'new'"
                                    :class="customerMode === 'new' ? 'bg-white text-secondary font-bold shadow-xs border border-slate-200' : 'text-slate-500 font-medium hover:text-slate-800'"
                                    class="py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-1.5 transition">
                                <i data-lucide="user-plus" class="w-4 h-4 text-emerald-600"></i>
                                <span>New Customer (नया ग्राहक)</span>
                            </button>
                        </div>

                        <!-- MODE A: SEARCHABLE EXISTING CUSTOMER COMBOBOX (Instant Name / Number Search for 1k+ Customers) -->
                        <div x-show="customerMode === 'existing'" x-transition class="space-y-3">
                            <div class="relative" @click.outside="customerDropdownOpen = false">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Search Customer / ग्राहक खोजें <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-textMuted" x-text="customers.length + ' clients registered'"></span>
                                </div>

                                <!-- Hidden input for form submission -->
                                <input type="hidden" name="customer_id" :value="selectedCustomerId">

                                <!-- Search Input with icon and clear button -->
                                <div class="relative">
                                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-slate-400"></i>
                                    <input type="text"
                                           x-model="customerSearch"
                                           @focus="customerDropdownOpen = true; if(selectedCustomerId) { $event.target.select(); }"
                                           @click="customerDropdownOpen = true"
                                           @input="customerDropdownOpen = true"
                                           placeholder="Type customer name, 10-digit mobile, firm or area..."
                                           class="w-full pl-9 pr-9 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-2xs font-medium">

                                    <button type="button" 
                                            x-show="customerSearch || selectedCustomerId" 
                                            @click="clearCustomer()" 
                                            class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-600 p-0.5 rounded transition">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                <!-- Dynamic Search Results Dropdown (Floats over following cards smoothly) -->
                                <div x-show="customerDropdownOpen" 
                                     x-cloak 
                                     class="absolute z-50 left-0 right-0 mt-1 max-h-80 overflow-y-auto bg-white rounded-2xl shadow-2xl border border-slate-300 ring-1 ring-black/10 divide-y divide-slate-100">
                                    
                                    <div class="p-2.5 bg-slate-50 text-[10.5px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between sticky top-0 border-b border-slate-200">
                                        <span x-text="customerSearch ? 'Matching Clients' : 'Recent Registered Clients'"></span>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold" x-text="filteredCustomers.length + ' found'"></span>
                                    </div>

                                    <template x-for="c in filteredCustomers" :key="c.id">
                                        <div @click="selectCustomer(c)" 
                                             class="p-3 hover:bg-blue-50/90 cursor-pointer transition flex items-center justify-between gap-2 text-xs border-b border-slate-100 last:border-b-0"
                                             :class="selectedCustomerId == c.id ? 'bg-blue-50 font-bold border-l-4 border-l-primary' : ''">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-extrabold text-slate-900" x-text="c.name"></span>
                                                    <span x-show="c.organization?.name" class="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] text-slate-600 font-semibold" x-text="c.organization?.name"></span>
                                                </div>
                                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-1">
                                                    <span class="font-bold text-slate-700" x-text="'📞 ' + (c.contact_no || 'No Phone')"></span>
                                                    <span x-show="c.area?.name" class="text-slate-600 font-medium" x-text="'📍 ' + c.area?.name"></span>
                                                </div>
                                            </div>
                                            <button type="button" class="px-3 py-1.5 bg-slate-100 group-hover:bg-primary group-hover:text-white rounded-xl text-[11px] font-bold shrink-0 transition shadow-2xs">
                                                Select
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="filteredCustomers.length === 0">
                                        <div class="p-5 text-center text-xs text-slate-500">
                                            <p class="font-bold text-slate-700">No customer matching "<span x-text="customerSearch"></span>"</p>
                                            <button type="button" @click="customerMode = 'new'" class="mt-2.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold rounded-xl hover:bg-emerald-100 text-xs inline-flex items-center gap-1.5 shadow-2xs">
                                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                                <span>Register as New Customer</span>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                @error('customer_id')
                                    <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Live Preview Card -->
                            <template x-if="selectedCustomer">
                                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-start justify-between gap-3 text-xs">
                                    <div class="space-y-1 min-w-0">
                                        <div class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5 flex-wrap">
                                            <span x-text="selectedCustomer.name"></span>
                                            <span class="text-[10px] bg-blue-200/70 text-blue-900 px-2 py-0.2 rounded-full font-bold uppercase" x-text="selectedCustomer.organization?.name || 'Direct Client'"></span>
                                        </div>
                                        <div class="text-slate-600 flex items-center gap-2">
                                            <span class="font-semibold" x-text="'📞 ' + selectedCustomer.contact_no"></span>
                                            <span x-show="selectedCustomer.area?.name" x-text="'📍 ' + (selectedCustomer.area?.name || '')"></span>
                                        </div>
                                        <div x-show="selectedCustomer.address" class="text-slate-500 text-[11px] truncate" x-text="selectedCustomer.address"></div>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <a :href="'tel:' + selectedCustomer.contact_no" 
                                           class="px-2.5 py-1.5 bg-white border border-blue-300 text-blue-700 rounded-xl text-xs font-bold shrink-0 hover:bg-blue-50 active:scale-95 transition flex items-center gap-1">
                                            <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                                            <span>Call</span>
                                        </a>
                                        <button type="button" @click="clearCustomer()" class="px-2 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-xs font-bold hover:bg-slate-50" title="Change Customer">
                                            Change
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <p class="text-[11px] text-textMuted flex items-center gap-1">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-secondary shrink-0"></i>
                                <span>Customer not registered? Switch to <strong>"New Customer"</strong> above for instant registration.</span>
                            </p>
                        </div>

                        <!-- MODE B: NEW CUSTOMER REGISTRATION -->
                        <div x-show="customerMode === 'new'" x-cloak x-transition class="space-y-3 p-3.5 sm:p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200">
                            <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                                <div class="flex items-center space-x-1.5 text-emerald-800 font-extrabold text-xs">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Instant Customer Registration</span>
                                </div>
                                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-full">Fast Form</span>
                            </div>

                            <!-- Customer Name & Mobile -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="new_customer_name" class="block text-xs font-bold text-slate-700 mb-1">
                                        Customer Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="new_customer_name" 
                                           name="new_customer_name" 
                                           value="{{ old('new_customer_name') }}" 
                                           placeholder="Enter customer full name" 
                                           class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                                    @error('new_customer_name')
                                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="new_customer_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                        Mobile Number <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           id="new_customer_phone" 
                                           name="new_customer_phone" 
                                           value="{{ old('new_customer_phone') }}" 
                                           placeholder="Enter 10-digit mobile number" 
                                           class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                                    @error('new_customer_phone')
                                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Area & Company Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">Area / Location</label>
                                        <button type="button" 
                                                @click="showNewAreaInput = !showNewAreaInput" 
                                                class="text-[11px] font-bold text-secondary hover:underline">
                                            <span x-text="showNewAreaInput ? '← Select Area' : '+ New Area'"></span>
                                        </button>
                                    </div>
                                    <div x-show="!showNewAreaInput">
                                        <select name="area_id" class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                                            <option value="">-- Select Area --</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div x-show="showNewAreaInput" x-cloak>
                                        <input type="text" 
                                               name="new_area_name" 
                                               value="{{ old('new_area_name') }}" 
                                               placeholder="Enter area or location name" 
                                               class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-emerald-400 bg-white">
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">Company / Firm Name</label>
                                        <button type="button" 
                                                @click="showNewFirmInput = !showNewFirmInput" 
                                                class="text-[11px] font-bold text-secondary hover:underline">
                                            <span x-text="showNewFirmInput ? '← Select Firm' : '+ New Firm'"></span>
                                        </button>
                                    </div>
                                    <div x-show="!showNewFirmInput">
                                        <select name="firm_id" class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                                            <option value="">-- None (Individual Client) --</option>
                                            @foreach($firms as $firm)
                                                <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                                    {{ $firm->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div x-show="showNewFirmInput" x-cloak>
                                        <input type="text" 
                                               name="new_firm_name" 
                                               value="{{ old('new_firm_name') }}" 
                                               placeholder="Enter company or firm name (optional)" 
                                               class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                                    </div>
                                </div>
                            </div>

                            <!-- Street Address -->
                            <div>
                                <label for="new_customer_address" class="block text-xs font-bold text-slate-700 mb-1">
                                    Full Site Address (Optional)
                                </label>
                                <input type="text" 
                                       id="new_customer_address" 
                                       name="new_customer_address" 
                                       value="{{ old('new_customer_address') }}" 
                                       placeholder="Enter site / street address (optional)" 
                                       class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: EQUIPMENT & PROBLEM DIAGNOSIS -->
                <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-2xs overflow-hidden relative z-10">
                    <div class="p-3.5 sm:p-4 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center font-bold text-xs">
                                2
                            </span>
                            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-800">
                                Machine & Problem Diagnosis / मशीन और समस्या
                            </h2>
                        </div>
                        <span class="text-[10px] text-textMuted font-semibold">* Required</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <!-- Machine Type & Service Type: 2 EQUAL COLUMNS ON DESKTOP, 1 ON MOBILE (Only Computer, Printer, CCTV, Other) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- Product Type (Computer, Printer, CCTV, Other) -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Equipment / Product <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="font-mono text-[11px] font-black text-secondary bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200" x-text="currentTicketId"></span>
                                </div>

                                <select name="product_type_id" 
                                        x-model="selectedProductId"
                                        class="w-full text-xs sm:text-sm py-2.5 px-3 rounded-xl border border-slate-300 bg-white outline-none focus:border-primary font-semibold">
                                    @foreach($productTypes as $pt)
                                        <option value="{{ $pt->id }}" {{ old('product_type_id') == $pt->id ? 'selected' : '' }}>
                                            {{ $pt->name }} (Prefix: {{ $pt->prefix }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10.5px] text-slate-500 mt-1 font-medium">
                                    Format: <strong>C</strong> (Computer), <strong>P</strong> (Printer), <strong>S</strong> (CCTV), <strong>O</strong> (Other) + YYMM + 001.
                                </p>
                            </div>

                            <!-- Service Type -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Service Classification <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" 
                                            @click="showNewServiceInput = !showNewServiceInput" 
                                            class="text-[11px] font-bold text-secondary hover:underline">
                                        <span x-text="showNewServiceInput ? '← Select List' : '+ Add Service'"></span>
                                    </button>
                                </div>

                                <div x-show="!showNewServiceInput">
                                    <select name="service_type_id" class="w-full text-xs sm:text-sm py-2.5 px-3 rounded-xl border border-slate-300 bg-white outline-none focus:border-primary">
                                        @foreach($serviceTypes as $st)
                                            <option value="{{ $st->id }}" {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                                                {{ $st->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="showNewServiceInput" x-cloak>
                                    <input type="text" 
                                           name="new_service_type" 
                                           value="{{ old('new_service_type') }}" 
                                           placeholder="Enter service classification" 
                                           class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-amber-400 bg-white focus:border-amber-600">
                                </div>
                            </div>
                        </div>

                        <!-- Problem Description with Rapid Mobile & Desktop Chips -->
                        <div>
                            <label for="issue_description" class="block text-xs font-bold text-slate-700 mb-1">
                                Complaint / Problem Description <span class="text-rose-500">*</span>
                            </label>

                            <!-- One-Tap Chips (Aligned with Computer, Printer & CCTV breakdowns) -->
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <button type="button" @click="addIssueTag('No Power / Not Turning On (चालू नहीं हो रहा)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    ⚡ No Power
                                </button>
                                <button type="button" @click="addIssueTag('Paper Jam / Roller Issue (कागज़ अटकना)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    🖨️ Paper Jam
                                </button>
                                <button type="button" @click="addIssueTag('Toner Refill / Cartridge Empty (टोनर खाली)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    🧴 Toner Refill
                                </button>
                                <button type="button" @click="addIssueTag('Camera Offline / No Display (कैमरा बंद)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    📹 Camera Offline
                                </button>
                                <button type="button" @click="addIssueTag('Windows / OS Boot Error (विंडोज एरर)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    💻 OS Boot Error
                                </button>
                                <button type="button" @click="addIssueTag('Virus / Malware Infection (एंटीवायरस)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    🛡️ Virus Error
                                </button>
                                <button type="button" @click="addIssueTag('DVR / NVR Password Reset (पासवर्ड)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    🔑 Password Reset
                                </button>
                                <button type="button" @click="addIssueTag('Network / LAN Sharing Issue')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                                    🌐 Network Issue
                                </button>
                            </div>

                            <textarea id="issue_description" 
                                      name="issue_description" 
                                      rows="3" 
                                      placeholder="Describe the problem, fault symptoms, or customer request..." 
                                      class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white leading-relaxed">{{ old('issue_description') }}</textarea>
                            @error('issue_description')
                                <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= RIGHT COLUMN (5 COLS ON DESKTOP): ASSIGNMENT, SLA & SUBMIT ================= -->
            <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-20">
                
                <!-- CARD 3: FIELD ASSIGNMENT & TARGET SCHEDULE -->
                <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-2xs overflow-hidden">
                    <div class="p-3.5 sm:p-4 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center font-bold text-xs">
                                3
                            </span>
                            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-800">
                                Field Assignment & Schedule / इंजीनियर और समय
                            </h2>
                        </div>
                        <span class="text-[10px] text-textMuted font-semibold">* Required</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <!-- Assign Field Engineer -->
                        <div>
                            <label for="engineer_id" class="block text-xs font-bold text-slate-700 mb-1">
                                Assign Field Engineer / इंजीनियर नियुक्त करें <span class="text-rose-500">*</span>
                            </label>
                            <select id="engineer_id" name="engineer_id" class="w-full text-xs sm:text-sm py-2.5 px-3 rounded-xl border border-slate-300 bg-white outline-none focus:border-primary font-medium">
                                <option value="">-- Select Active Field Engineer --</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}" {{ old('engineer_id') == $eng->id ? 'selected' : '' }}>
                                        👷 {{ $eng->name }} • {{ $eng->phone_no }}
                                    </option>
                                @endforeach
                            </select>
                            @error('engineer_id')
                                <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority Level -->
                        <div x-data="{ priority: '{{ old('priority', 'medium') }}' }">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Priority Level / प्राथमिकता <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="priority" :value="priority">

                            <div class="grid grid-cols-4 gap-1.5">
                                <button type="button" 
                                        @click="priority = 'low'" 
                                        :class="priority === 'low' ? 'bg-slate-700 text-white font-black ring-2 ring-slate-800 shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                                    Low
                                </button>
                                <button type="button" 
                                        @click="priority = 'medium'" 
                                        :class="priority === 'medium' ? 'bg-blue-600 text-white font-black ring-2 ring-blue-700 shadow-2xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                                    Medium
                                </button>
                                <button type="button" 
                                        @click="priority = 'high'" 
                                        :class="priority === 'high' ? 'bg-amber-600 text-white font-black ring-2 ring-amber-700 shadow-2xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                                    High
                                </button>
                                <button type="button" 
                                        @click="priority = 'urgent'" 
                                        :class="priority === 'urgent' ? 'bg-rose-600 text-white font-black ring-2 ring-rose-700 shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                                    🚨 Urgent
                                </button>
                            </div>
                        </div>

                        <!-- Target Due Date with Quick Pills -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="due_date" class="block text-xs font-bold text-slate-700">
                                    Target Due Date & Time <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="setDueDate(0)" class="px-2 py-0.5 rounded-lg bg-slate-100 text-[10px] font-bold text-slate-700 hover:bg-slate-200 transition">
                                        Today
                                    </button>
                                    <button type="button" @click="setDueDate(1)" class="px-2 py-0.5 rounded-lg bg-blue-50 text-[10px] font-bold text-blue-700 hover:bg-blue-100 transition">
                                        Tomorrow
                                    </button>
                                    <button type="button" @click="setDueDate(2)" class="px-2 py-0.5 rounded-lg bg-purple-50 text-[10px] font-bold text-purple-700 hover:bg-purple-100 transition">
                                        +2 Days
                                    </button>
                                </div>
                            </div>

                            @php
                                $defaultDue = old('due_date', now()->addDay()->setHour(18)->setMinute(0)->format('Y-m-d\TH:i'));
                            @endphp
                            <input type="datetime-local" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ $defaultDue }}" 
                                   class="w-full text-xs sm:text-sm py-2 px-3 rounded-xl border border-slate-300 bg-white">
                            @error('due_date')
                                <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Problem Photo Upload -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Equipment Photo (Optional / फोटो)
                            </label>
                            <label class="flex items-center justify-center space-x-2 px-3 py-2.5 border-2 border-dashed border-borderMain rounded-xl cursor-pointer hover:border-secondary hover:bg-blue-50/30 transition text-xs font-semibold text-textMuted">
                                <i data-lucide="camera" class="w-4 h-4 text-secondary"></i>
                                <span>Choose photo or take camera snap</span>
                                <input type="file" name="photo" accept="image/*" class="hidden" onchange="if(this.files[0]) document.getElementById('photo-label').innerText = this.files[0].name;">
                            </label>
                            <p id="photo-label" class="text-[11px] text-emerald-700 font-bold mt-1 truncate"></p>
                            @error('photo')
                                <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- CARD 4: BOOKING CONFIRMATION & ACTION CARD -->
                <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm p-4 sm:p-5 space-y-3">
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-borderMain">
                        <span class="font-extrabold text-slate-800">Dispatch Status</span>
                        <span class="font-mono px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 uppercase" x-text="currentTicketId"></span>
                    </div>

                    <p class="text-[11px] text-textMuted leading-relaxed">
                        Ticket ID is auto-generated based on equipment category and resets to 001 every month. Assigned engineer will be notified immediately.
                    </p>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl bg-primary hover:bg-primary-dark text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center space-x-2 active:scale-98">
                        <i data-lucide="check-circle" class="w-4 h-4 text-amber-300"></i>
                        <span>Book Service Ticket / शिकायत दर्ज करें</span>
                    </button>

                    <div class="text-center pt-1">
                        <a href="{{ route('complaints.index') }}" class="text-xs font-bold text-textMuted hover:text-textMain transition">
                            Cancel & Return to Tickets
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
@endsection