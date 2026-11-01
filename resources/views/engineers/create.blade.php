@extends('layouts.app')

@section('title', 'Add Engineer | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </span>
                Add Engineer
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Register technician mobile authentication, credentials, department, and HR records</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('engineers.index') }}" class="px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="engineerForm" class="px-6 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Add Engineer
            </button>
        </div>
    </div>

    <!-- Main Full-Width Form Layout -->
    <form id="engineerForm" action="{{ route('engineers.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <!-- LEFT COLUMN: 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Account & Login Security -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-secondary"></i> Mobile Login & Credentials
                    </h3>
                    <span class="text-[11px] text-secondary-text">* Mandatory for app login</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Full Name <span class="text-app-error">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rahul Sharma" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('name')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Mobile Number <span class="text-app-error">*</span></label>
                            <div class="flex items-center gap-1.5">
                                <input type="hidden" name="country_code" value="+91">
                                <span class="px-2.5 py-2.5 rounded-lg border border-app-border bg-app-bg text-xs font-bold text-primary-text">+91</span>
                                <input type="tel" name="phone_no" value="{{ old('phone_no') }}" required placeholder="10-digit number" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            </div>
                            @error('phone_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                            @error('country_code')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Initial Password <span class="text-app-error">*</span></label>
                            <input type="password" name="password" required placeholder="Min 6 characters" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('password')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional & Department Details -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 text-accent"></i> Department & Designation
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="rahul@bhumitechnocare.com" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Department</label>
                            <input type="text" name="department" value="{{ old('department', 'CNC & Machinery Service') }}" placeholder="e.g. CNC & Industrial Automation" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Designation / Post</label>
                            <input type="text" name="post" value="{{ old('post', 'Senior Field Engineer') }}" placeholder="e.g. Field Service Specialist" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- HR & Personal Profile -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="heart" class="w-4 h-4 text-primary"></i> Personal Profile & Emergency Contacts
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob') }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Blood Group</label>
                            <select name="blood_group" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">Select Blood Group...</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Emergency Family Contact</label>
                            <input type="tel" name="family_contact_no" value="{{ old('family_contact_no') }}" placeholder="e.g. 9825001122" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Residential Street Address</label>
                            <textarea name="address" rows="2" placeholder="Residential house, apartment, street, city..." class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('address') }}</textarea>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Internal Remarks / HR Notes</label>
                            <textarea name="remarks" rows="2" placeholder="Additional notes or remarks regarding this staff member..." class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: 4 Columns -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Profile Photo & Resume -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-secondary"></i> Official Profile Photo
                </h3>
                <div class="border-2 border-dashed border-app-border hover:border-secondary/50 rounded-xl p-4 text-center bg-app-bg/50 transition-colors">
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
                @error('photo')<p class="text-xs text-app-error">{{ $message }}</p>@enderror
            </div>

            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-secondary"></i> Resume / ID Proof Document
                </h3>
                <div class="border-2 border-dashed border-app-border hover:border-secondary/50 rounded-xl p-4 text-center bg-app-bg/50 transition-colors">
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-xs text-secondary-text file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
            </div>

            <!-- Submission Actions -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Add Engineer
                </button>
                <a href="{{ route('engineers.index') }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Discard
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
