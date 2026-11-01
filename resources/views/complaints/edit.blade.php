@extends('layouts.app')

@section('title', 'Edit Ticket #' . $complaint->ticket_number . ' | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-borderMain">
        <div>
            <div class="flex items-center space-x-2 text-xs text-textMuted mb-1">
                <a href="{{ route('complaints.index') }}" class="hover:text-primary">&larr; Service Tickets</a>
                <span>/</span>
                <a href="{{ route('complaints.show', $complaint) }}" class="hover:text-primary">Ticket #{{ $complaint->ticket_number }}</a>
                <span>/</span>
                <span class="text-textMain font-bold">Edit Details</span>
            </div>
            <h1 class="text-2xl font-bold text-textMain flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                </span>
                Edit Service Ticket #{{ $complaint->ticket_number }}
            </h1>
            <p class="text-xs text-textMuted mt-0.5">Modify ticket parameters, reassign field engineer, or adjust urgency SLA</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- CANCEL: Safely returns to ticket details without clearing original data -->
            <a href="{{ route('complaints.show', $complaint) }}" class="px-4 py-2.5 rounded-xl border border-borderMain bg-surface hover:bg-bg text-xs font-semibold text-textMain transition-colors">
                Cancel / Back to Details
            </a>
            <button type="submit" form="complaintEditForm" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-md transition-colors flex items-center gap-2 active:scale-95">
                <i data-lucide="check" class="w-4 h-4"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Main Form Layout -->
    <form id="complaintEditForm" action="{{ route('complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- LEFT COLUMN: 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Client & Machine Equipment Card -->
            <div class="bg-surface rounded-2xl border border-borderMain shadow-sm overflow-hidden">
                <div class="p-4 border-b border-borderMain bg-bg/50 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-secondary"></i> Client & Equipment Details
                    </h3>
                    <span class="text-[11px] text-textMuted">* Pre-filled with saved data</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1.5">
                                Client / Representative <span class="text-rose-600">*</span>
                            </label>
                            <select name="customer_id" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $complaint->customer_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->organization?->name ?? 'Direct Client' }}) - {{ $c->contact_no }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1.5">
                                Firm / Organization
                            </label>
                            <select name="firm_id" class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                                <option value="">-- Auto-link with Customer Firm --</option>
                                @foreach($firms as $f)
                                <option value="{{ $f->id }}" {{ old('firm_id', $complaint->firm_id) == $f->id ? 'selected' : '' }}>
                                    {{ $f->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('firm_id')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1.5">
                                Machinery / Equipment Type <span class="text-rose-600">*</span>
                            </label>
                            <select name="product_type_id" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                                @foreach($productTypes as $prod)
                                <option value="{{ $prod->id }}" {{ old('product_type_id', $complaint->product_type_id) == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('product_type_id')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-textMain mb-1.5">
                                Service Classification <span class="text-rose-600">*</span>
                            </label>
                            <select name="service_type_id" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                                @foreach($serviceTypes as $srv)
                                <option value="{{ $srv->id }}" {{ old('service_type_id', $complaint->service_type_id) == $srv->id ? 'selected' : '' }}>
                                    {{ $srv->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('service_type_id')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Problem / Fault Description & Resolution Notes -->
            <div class="bg-surface rounded-2xl border border-borderMain shadow-sm p-6 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Breakdown Symptoms & Issue Description <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="issue_description" rows="4" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary p-3 font-medium text-textMain">{{ old('issue_description', $complaint->issue_description) }}</textarea>
                    @error('issue_description')<p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-4 border-t border-borderMain">
                    <label class="block text-xs font-bold text-textMain mb-1.5">
                        Technician Resolution Notes / Work Summary (Optional)
                    </label>
                    <textarea name="resolution_notes" rows="3" placeholder="Enter findings, parts replaced, calibration values, or remarks..." class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary p-3 font-medium text-textMain">{{ old('resolution_notes', $complaint->resolution_notes) }}</textarea>
                    @error('resolution_notes')<p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Status, Priority, Dispatch SLA (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Ticket Status & Priority Card (RADIO BUTTONS) -->
            <div class="bg-surface rounded-2xl border border-borderMain shadow-sm p-5 space-y-5">
                
                <!-- 1. TICKET STATUS RADIO BUTTONS -->
                <div x-data="{ currentStatus: '{{ old('status', $complaint->status) }}' }">
                    <label class="block text-xs font-bold text-textMain mb-2 uppercase tracking-wider">
                        Ticket Status <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentStatus === 'pending' ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="status" value="pending" x-model="currentStatus" class="text-amber-600 focus:ring-amber-500">
                            <span>Pending</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentStatus === 'in_progress' ? 'border-blue-500 bg-blue-50 text-blue-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="status" value="in_progress" x-model="currentStatus" class="text-blue-600 focus:ring-blue-500">
                            <span>In Progress</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentStatus === 'completed' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="status" value="completed" x-model="currentStatus" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Completed</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentStatus === 'cancelled' ? 'border-rose-500 bg-rose-50 text-rose-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="status" value="cancelled" x-model="currentStatus" class="text-rose-600 focus:ring-rose-500">
                            <span>Cancelled</span>
                        </label>
                    </div>
                </div>

                <!-- 2. URGENCY / PRIORITY RADIO BUTTONS -->
                <div x-data="{ currentPriority: '{{ old('priority', $complaint->priority) }}' }">
                    <label class="block text-xs font-bold text-textMain mb-2 uppercase tracking-wider">
                        Urgency / Priority Level <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentPriority === 'urgent' ? 'border-rose-500 bg-rose-50 text-rose-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="priority" value="urgent" x-model="currentPriority" class="text-rose-600 focus:ring-rose-500">
                            <span>Urgent Line Halt</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentPriority === 'high' ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="priority" value="high" x-model="currentPriority" class="text-amber-600 focus:ring-amber-500">
                            <span>High Priority</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentPriority === 'medium' ? 'border-blue-500 bg-blue-50 text-blue-900 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="priority" value="medium" x-model="currentPriority" class="text-blue-600 focus:ring-blue-500">
                            <span>Medium</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-bold"
                               :class="currentPriority === 'low' ? 'border-slate-400 bg-slate-100 text-slate-800 shadow-sm' : 'border-borderMain bg-bg text-textMuted hover:bg-slate-100'">
                            <input type="radio" name="priority" value="low" x-model="currentPriority" class="text-slate-600 focus:ring-slate-500">
                            <span>Low / AMC</span>
                        </label>
                    </div>
                </div>

                <!-- 3. ENGINEER & SLA -->
                <div class="space-y-3 pt-3 border-t border-borderMain">
                    <div>
                        <label class="block text-xs font-bold text-textMain mb-1">
                            Assigned Field Engineer <span class="text-rose-600">*</span>
                        </label>
                        <select name="engineer_id" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                            @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ old('engineer_id', $complaint->engineer_id) == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }} ({{ $eng->department ?? 'Service' }})
                            </option>
                            @endforeach
                        </select>
                        @error('engineer_id')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-textMain mb-1">
                            Resolution Target Due Date <span class="text-rose-600">*</span>
                        </label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date', $complaint->due_date ? $complaint->due_date->format('Y-m-d\TH:i') : '') }}" required class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary focus:border-secondary py-2.5 px-3 font-medium text-textMain">
                        @error('due_date')<p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Fault Snapshot Upload (Optional Update) -->
            <div class="bg-surface rounded-2xl border border-borderMain shadow-sm p-5 space-y-3" x-data="{ preview: null }">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4 text-secondary"></i> Update Fault Photo
                </h3>

                @if($complaint->photo)
                    <div class="mb-2">
                        <span class="text-[11px] text-textMuted block mb-1">Current Attached Photo:</span>
                        <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Fault Snapshot" class="w-full h-32 object-cover rounded-xl border border-borderMain">
                    </div>
                @endif

                <input type="file" name="photo" accept="image/*" class="w-full text-xs text-textMuted file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
            </div>

            <!-- Action Buttons Card -->
            <div class="bg-surface rounded-2xl border border-borderMain shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-3 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Ticket Changes
                </button>
                <a href="{{ route('complaints.show', $complaint) }}" class="w-full py-2.5 rounded-xl border border-borderMain text-center block text-xs font-bold text-textMuted hover:bg-bg hover:text-textMain transition">
                    Discard & Return to Details
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
