@extends('layouts.app')

@section('title', 'Edit Staff: ' . $engineer->name . ' | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-secondary/15 text-secondary flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </span>
                Edit Field Engineer Profile: {{ $engineer->name }}
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Update personal records, assigned department, emergency phone or reset password</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('engineers.show', $engineer) }}" class="px-4 py-2 rounded-lg border border-app-border bg-surface hover:bg-app-bg text-xs font-semibold text-secondary-text transition-colors">
                Cancel
            </a>
            <button type="submit" form="engineerEditForm" class="px-6 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Update Profile
            </button>
        </div>
    </div>

    <!-- Main Full-Width Form Layout -->
    <form id="engineerEditForm" action="{{ route('engineers.update', $engineer) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- LEFT COLUMN: 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Account & Login Security -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-secondary"></i> Mobile Login & Credentials
                    </h3>
                    <span class="text-[11px] text-secondary-text">Staff ID: #ENG-{{ str_pad($engineer->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Full Name <span class="text-app-error">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $engineer->name) }}" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('name')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Mobile Number <span class="text-app-error">*</span></label>
                            <div class="flex items-center gap-1.5">
                                <input type="hidden" name="country_code" value="{{ old('country_code', $engineer->country_code ?? '+91') }}">
                                <span class="px-2.5 py-2.5 rounded-lg border border-app-border bg-app-bg text-xs font-bold text-primary-text">+91</span>
                                <input type="tel" name="phone_no" value="{{ old('phone_no', $engineer->phone_no) }}" required class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            </div>
                            @error('phone_no')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                            @error('country_code')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Change Password (Optional)</label>
                            <input type="password" name="new_password" placeholder="Leave blank to keep current" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
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
                            <input type="email" name="email" value="{{ old('email', $engineer->email) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Department</label>
                            <input type="text" name="department" value="{{ old('department', $engineer->department) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Designation / Post</label>
                            <input type="text" name="post" value="{{ old('post', $engineer->post) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
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
                            <input type="date" name="dob" value="{{ old('dob', $engineer->dob ? \Carbon\Carbon::parse($engineer->dob)->format('Y-m-d') : '') }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Blood Group</label>
                            <select name="blood_group" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                                <option value="">Select Blood Group...</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $engineer->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Emergency Family Contact</label>
                            <input type="tel" name="family_contact_no" value="{{ old('family_contact_no', $engineer->family_contact_no) }}" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">Residential Street Address</label>
                            <textarea name="address" rows="2" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary p-3">{{ old('address', $engineer->address) }}</textarea>
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
                    <i data-lucide="image" class="w-4 h-4 text-secondary"></i> Profile Photo
                </h3>
                @if($engineer->photo)
                <div class="flex items-center gap-3 p-3 bg-app-bg rounded-lg border border-app-border">
                    <img src="{{ $engineer->profile_photo_url }}" class="w-12 h-12 rounded-full object-cover border border-app-border" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($engineer->name) }}&background=1E3A5F&color=FFFFFF&bold=true&size=128';">
                    <span class="text-xs text-secondary-text">Current photo saved</span>
                </div>
                @endif
                <div class="border-2 border-dashed border-app-border hover:border-secondary/50 rounded-xl p-4 text-center bg-app-bg/50 transition-colors">
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
            </div>

            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-secondary"></i> Resume / Document
                </h3>
                @if($engineer->resume)
                <a href="{{ Storage::url($engineer->resume) }}" target="_blank" class="text-xs text-secondary hover:underline flex items-center gap-1.5 p-2 bg-app-bg rounded-lg">
                    <i data-lucide="download" class="w-4 h-4"></i> View Current Resume
                </a>
                @endif
                <div class="border-2 border-dashed border-app-border hover:border-secondary/50 rounded-xl p-4 text-center bg-app-bg/50 transition-colors">
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-xs text-secondary-text file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>
            </div>

            <!-- Submission Actions -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Update Profile
                </button>
                <a href="{{ route('engineers.show', $engineer) }}" class="w-full py-2 rounded-lg border border-app-border text-center block text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                    Discard Changes
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
