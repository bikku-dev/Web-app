@extends('layouts.app')

@section('title', 'Book Service Call | Bhumi Technocare')
@section('page_title', 'New Service Complaint')

@section('content')
<div class="max-w-2xl mx-auto px-1 sm:px-4 py-2 sm:py-4 pb-20" 
     x-data="{
        customerMode: '{{ old('customer_mode', 'existing') }}',
        selectedCustomerId: '{{ old('customer_id', '') }}',
        showNewAreaInput: {{ old('new_area_name') ? 'true' : 'false' }},
        showNewFirmInput: {{ old('new_firm_name') ? 'true' : 'false' }},
        showNewProductInput: {{ old('new_product_type') ? 'true' : 'false' }},
        showNewServiceInput: {{ old('new_service_type') ? 'true' : 'false' }},
        customers: {{ Js::from($customers) }},
        selectedCustomer: null,

        init() {
            this.updateCustomerDetails();
            this.$watch('selectedCustomerId', () => this.updateCustomerDetails());
        },

        updateCustomerDetails() {
            if (!this.selectedCustomerId) {
                this.selectedCustomer = null;
                return;
            }
            this.selectedCustomer = this.customers.find(c => c.id == this.selectedCustomerId) || null;
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

    <!-- Top App Navigation Bar -->
    <div class="flex items-center justify-between gap-3 mb-4">
        <a href="{{ route('complaints.index') }}" 
           class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white border border-borderMain text-xs font-bold text-textMain hover:bg-slate-50 transition active:scale-95 shadow-2xs">
            <i data-lucide="arrow-left" class="w-4 h-4 text-textMuted"></i>
            <span>Back</span>
        </a>
        <div class="text-right">
            <span class="text-[10px] font-black uppercase tracking-wider text-secondary bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                Quick Service Booking
            </span>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="bg-surface rounded-2xl sm:rounded-3xl border border-borderMain shadow-sm overflow-hidden">
        
        <!-- Header Banner -->
        <div class="p-4 sm:p-5 bg-gradient-to-r from-primary to-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                    <i data-lucide="wrench" class="w-6 h-6 text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black tracking-tight leading-tight">
                        Book Service Call
                    </h2>
                    <p class="text-xs text-slate-300">
                        Create breakdown ticket & assign engineer
                    </p>
                </div>
            </div>
            <div class="hidden sm:block text-right">
                <span class="text-xs font-bold text-amber-300">Auto-Generates Ticket ID</span>
            </div>
        </div>

        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-5">
            @csrf
            <input type="hidden" name="customer_mode" :value="customerMode">

            <!-- 1. CUSTOMER SELECTION / CREATION MODE TOGGLE -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                    Customer Information / ग्राहक जानकारी
                </label>

                <!-- Modern Segmented Pill Tabs -->
                <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl border border-slate-200 gap-1">
                    <button type="button" 
                            @click="customerMode = 'existing'"
                            :class="customerMode === 'existing' ? 'bg-white text-primary font-bold shadow-xs border border-slate-200' : 'text-slate-500 font-medium hover:text-slate-800'"
                            class="py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-2 transition">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Existing Customer</span>
                    </button>

                    <button type="button" 
                            @click="customerMode = 'new'"
                            :class="customerMode === 'new' ? 'bg-white text-secondary font-bold shadow-xs border border-slate-200' : 'text-slate-500 font-medium hover:text-slate-800'"
                            class="py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-center space-x-2 transition">
                        <i data-lucide="user-plus" class="w-4 h-4 text-emerald-600"></i>
                        <span>New Customer (नया)</span>
                    </button>
                </div>
            </div>

            <!-- MODE A: EXISTING CUSTOMER DROPDOWN & LIVE PREVIEW -->
            <div x-show="customerMode === 'existing'" x-transition class="space-y-3">
                <div>
                    <label for="customer_id" class="block text-xs font-bold text-slate-700 mb-1">
                        Select Customer / ग्राहक चुनें <span class="text-rose-500">*</span>
                    </label>
                    <select id="customer_id" 
                            name="customer_id" 
                            x-model="selectedCustomerId" 
                            class="w-full text-xs sm:text-sm font-medium py-2.5 px-3">
                        <option value="">-- Search / Select from registered customers --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} • {{ $c->contact_no }} {{ $c->organization ? '('.$c->organization->name.')' : '' }} {{ $c->area ? '['.$c->area->name.']' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Details Card Preview -->
                <template x-if="selectedCustomer">
                    <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                                <span x-text="selectedCustomer.name"></span>
                                <span class="text-[10px] bg-blue-200/70 text-blue-900 px-2 py-0.2 rounded-full font-bold uppercase" x-text="selectedCustomer.organization?.name || 'Direct Client'"></span>
                            </div>
                            <div class="text-slate-600 flex items-center gap-2">
                                <span class="font-semibold" x-text="'📞 ' + selectedCustomer.contact_no"></span>
                                <span x-show="selectedCustomer.area?.name" x-text="'📍 ' + (selectedCustomer.area?.name || '')"></span>
                            </div>
                            <div x-show="selectedCustomer.address" class="text-slate-500 text-[11px]" x-text="selectedCustomer.address"></div>
                        </div>
                        <a :href="'tel:' + selectedCustomer.contact_no" 
                           class="px-2.5 py-1.5 bg-white border border-blue-300 text-blue-700 rounded-xl text-xs font-bold shrink-0 hover:bg-blue-50 active:scale-95 transition flex items-center gap-1">
                            <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                            <span>Call</span>
                        </a>
                    </div>
                </template>

                <p class="text-[11px] text-textMuted flex items-center gap-1">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-secondary"></i>
                    <span>Customer not in list? Switch to <strong>"New Customer"</strong> above to add them right here!</span>
                </p>
            </div>

            <!-- MODE B: NEW CUSTOMER INLINE REGISTRATION -->
            <div x-show="customerMode === 'new'" x-cloak x-transition class="space-y-3.5 p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200">
                <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                    <div class="flex items-center space-x-1.5 text-emerald-800 font-extrabold text-xs">
                        <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600"></i>
                        <span>Instant Customer Registration (कोई अलग पेज नहीं)</span>
                    </div>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-full">All-in-One</span>
                </div>

                <!-- Customer Name & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="new_customer_name" class="block text-xs font-bold text-slate-700 mb-1">
                            Customer Name / नाम <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="new_customer_name" 
                               name="new_customer_name" 
                               value="{{ old('new_customer_name') }}" 
                               placeholder="e.g. Ramesh Patel" 
                               class="w-full text-xs sm:text-sm py-2 px-3">
                        @error('new_customer_name')
                            <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_customer_phone" class="block text-xs font-bold text-slate-700 mb-1">
                            Mobile Number / मोबाइल <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" 
                               id="new_customer_phone" 
                               name="new_customer_phone" 
                               value="{{ old('new_customer_phone') }}" 
                               placeholder="e.g. 9876543210" 
                               class="w-full text-xs sm:text-sm py-2 px-3">
                        @error('new_customer_phone')
                            <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Area / Location with Inline Add Area -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Area / Location / इलाका
                        </label>
                        <button type="button" 
                                @click="showNewAreaInput = !showNewAreaInput" 
                                class="text-[11px] font-bold text-secondary hover:underline flex items-center gap-1">
                            <span x-text="showNewAreaInput ? '← Choose Existing Area' : '+ Type New Area'"></span>
                        </button>
                    </div>

                    <!-- Existing Area Select -->
                    <div x-show="!showNewAreaInput">
                        <select name="area_id" class="w-full text-xs sm:text-sm py-2 px-3">
                            <option value="">-- Select Area / Location --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Area Input -->
                    <div x-show="showNewAreaInput" x-cloak>
                        <input type="text" 
                               name="new_area_name" 
                               value="{{ old('new_area_name') }}" 
                               placeholder="Type new area name (e.g. GIDC Ankleshwar, Kim, Surat)" 
                               class="w-full text-xs sm:text-sm py-2 px-3 border-emerald-400 focus:border-emerald-600">
                        <p class="text-[10px] text-emerald-700 mt-1 font-medium">✨ This area will be automatically saved for future use.</p>
                    </div>
                </div>

                <!-- Company / Organization (Optional) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Company / Firm Name (Optional)
                        </label>
                        <button type="button" 
                                @click="showNewFirmInput = !showNewFirmInput" 
                                class="text-[11px] font-bold text-secondary hover:underline flex items-center gap-1">
                            <span x-text="showNewFirmInput ? '← Choose Existing Firm' : '+ Type New Firm'"></span>
                        </button>
                    </div>

                    <!-- Existing Firm Select -->
                    <div x-show="!showNewFirmInput">
                        <select name="firm_id" class="w-full text-xs sm:text-sm py-2 px-3">
                            <option value="">-- None (Individual Customer) --</option>
                            @foreach($firms as $firm)
                                <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                    {{ $firm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Firm Input -->
                    <div x-show="showNewFirmInput" x-cloak>
                        <input type="text" 
                               name="new_firm_name" 
                               value="{{ old('new_firm_name') }}" 
                               placeholder="Enter Company / Factory name" 
                               class="w-full text-xs sm:text-sm py-2 px-3">
                    </div>
                </div>

                <!-- Street / Site Address -->
                <div>
                    <label for="new_customer_address" class="block text-xs font-bold text-slate-700 mb-1">
                        Full Site Address / पता (Optional)
                    </label>
                    <input type="text" 
                           id="new_customer_address" 
                           name="new_customer_address" 
                           value="{{ old('new_customer_address') }}" 
                           placeholder="Plot No, Street, Landmark..." 
                           class="w-full text-xs sm:text-sm py-2 px-3">
                </div>
            </div>

            <hr class="border-borderMain/60">

            <!-- 2. MACHINE & SERVICE DETAILS -->
            <div class="space-y-4">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                    Machine & Issue Details / मशीन और समस्या
                </label>

                <!-- Product Type (Machine Category) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Machine / Product Type <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" 
                                @click="showNewProductInput = !showNewProductInput" 
                                class="text-[11px] font-bold text-secondary hover:underline">
                            <span x-text="showNewProductInput ? '← Select from list' : '+ Add New Machine Type'"></span>
                        </button>
                    </div>

                    <div x-show="!showNewProductInput">
                        <select name="product_type_id" class="w-full text-xs sm:text-sm py-2.5 px-3">
                            @foreach($productTypes as $pt)
                                <option value="{{ $pt->id }}" {{ old('product_type_id') == $pt->id ? 'selected' : '' }}>
                                    {{ $pt->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="showNewProductInput" x-cloak>
                        <input type="text" 
                               name="new_product_type" 
                               value="{{ old('new_product_type') }}" 
                               placeholder="e.g. CNC Lathe 300, Hydraulic Press, Laser Cutter..." 
                               class="w-full text-xs sm:text-sm py-2 px-3 border-amber-400 focus:border-amber-600">
                        <p class="text-[10px] text-amber-700 mt-1 font-medium">✨ Will be saved as a new equipment category.</p>
                    </div>
                </div>

                <!-- Service Type -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Service Type / कार्य का प्रकार <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" 
                                @click="showNewServiceInput = !showNewServiceInput" 
                                class="text-[11px] font-bold text-secondary hover:underline">
                            <span x-text="showNewServiceInput ? '← Select from list' : '+ Add New Service Type'"></span>
                        </button>
                    </div>

                    <div x-show="!showNewServiceInput">
                        <select name="service_type_id" class="w-full text-xs sm:text-sm py-2.5 px-3">
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
                               placeholder="e.g. Emergency Breakdown, Quarterly AMC, Spares Replacement..." 
                               class="w-full text-xs sm:text-sm py-2 px-3 border-amber-400 focus:border-amber-600">
                    </div>
                </div>

                <!-- Issue Description with Quick Tap Chips -->
                <div>
                    <label for="issue_description" class="block text-xs font-bold text-slate-700 mb-1">
                        Complaint / Problem Description <span class="text-rose-500">*</span>
                    </label>

                    <!-- Quick Issue Tap Chips for Rapid Mobile Entry -->
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <button type="button" @click="addIssueTag('Power Failure (बिजली नहीं आ रही)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                            ⚡ Power Failure
                        </button>
                        <button type="button" @click="addIssueTag('Abnormal Motor Noise (मोटर आवाज)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                            🔊 Motor Noise
                        </button>
                        <button type="button" @click="addIssueTag('Overheating (अत्यधिक गर्म होना)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                            🌡️ Overheating
                        </button>
                        <button type="button" @click="addIssueTag('Oil / Air Leakage (लीकेज)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                            💧 Leakage
                        </button>
                        <button type="button" @click="addIssueTag('Display / Sensor Error (सेंसर खराबी)')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 active:scale-95 transition">
                            📟 Sensor Error
                        </button>
                        <button type="button" @click="addIssueTag('Complete Breakdown (मशीन बंद)')" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-semibold border border-rose-200 active:scale-95 transition">
                            🛑 Breakdown
                        </button>
                    </div>

                    <textarea id="issue_description" 
                              name="issue_description" 
                              rows="3" 
                              placeholder="Describe the fault or customer complaints..." 
                              class="w-full text-xs sm:text-sm py-2 px-3 leading-relaxed">{{ old('issue_description') }}</textarea>
                    @error('issue_description')
                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="border-borderMain/60">

            <!-- 3. ASSIGNMENT, PRIORITY & DUE DATE -->
            <div class="space-y-4">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                    Assignment & Target Schedule / इंजीनियर और समय
                </label>

                <!-- Assign Field Engineer -->
                <div>
                    <label for="engineer_id" class="block text-xs font-bold text-slate-700 mb-1">
                        Assign Field Engineer / इंजीनियर नियुक्त करें <span class="text-rose-500">*</span>
                    </label>
                    <select id="engineer_id" name="engineer_id" class="w-full text-xs sm:text-sm py-2.5 px-3">
                        <option value="">-- Select Active Engineer --</option>
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

                <!-- Priority Segmented Buttons -->
                <div x-data="{ priority: '{{ old('priority', 'medium') }}' }">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Priority Level / प्राथमिकता <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="priority" :value="priority">

                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" 
                                @click="priority = 'low'" 
                                :class="priority === 'low' ? 'bg-slate-700 text-white font-black ring-2 ring-slate-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="py-2 rounded-xl text-xs font-bold transition text-center">
                            Low
                        </button>
                        <button type="button" 
                                @click="priority = 'medium'" 
                                :class="priority === 'medium' ? 'bg-blue-600 text-white font-black ring-2 ring-blue-700' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                                class="py-2 rounded-xl text-xs font-bold transition text-center">
                            Medium
                        </button>
                        <button type="button" 
                                @click="priority = 'high'" 
                                :class="priority === 'high' ? 'bg-amber-600 text-white font-black ring-2 ring-amber-700' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
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

                <!-- Due Date & Quick Selection Pills -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="due_date" class="block text-xs font-bold text-slate-700">
                            Target / Due Date & Time <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="setDueDate(0)" class="px-2 py-0.5 rounded-lg bg-slate-100 text-[10px] font-bold text-slate-700 hover:bg-slate-200 transition">
                                Today (आज)
                            </button>
                            <button type="button" @click="setDueDate(1)" class="px-2 py-0.5 rounded-lg bg-blue-50 text-[10px] font-bold text-blue-700 hover:bg-blue-100 transition">
                                Tomorrow (कल)
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
                           class="w-full text-xs sm:text-sm py-2 px-3">
                    @error('due_date')
                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Machine Breakdown Photo (Optional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Machine / Problem Photo (Optional / फोटो)
                    </label>
                    <div class="flex items-center space-x-3">
                        <label class="flex-1 flex items-center justify-center space-x-2 px-4 py-3 border-2 border-dashed border-borderMain rounded-2xl cursor-pointer hover:border-secondary hover:bg-blue-50/30 transition text-xs font-semibold text-textMuted">
                            <i data-lucide="camera" class="w-5 h-5 text-secondary"></i>
                            <span>Tap to take photo or choose file</span>
                            <input type="file" name="photo" accept="image/*" class="hidden" onchange="if(this.files[0]) document.getElementById('photo-label').innerText = this.files[0].name;">
                        </label>
                    </div>
                    <p id="photo-label" class="text-[11px] text-emerald-700 font-bold mt-1 truncate"></p>
                    @error('photo')
                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sticky / Prominent Action Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-primary via-slate-800 to-secondary text-white font-extrabold text-sm sm:text-base shadow-lg shadow-primary/25 hover:shadow-xl active:scale-[0.98] transition flex items-center justify-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-amber-400"></i>
                    <span>Book Service Ticket / शिकायत दर्ज करें</span>
                </button>
                <p class="text-center text-[11px] text-textMuted mt-2">
                    Instant ticket number & engineer assignment notification
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
