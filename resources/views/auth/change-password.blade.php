@extends('layouts.app')

@section('title', 'Account Settings & Profile | Bhumi Technocare')

@section('content')
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-app-border">
        <div>
            <h1 class="text-2xl font-bold text-primary-text flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                </span>
                Profile & Security Settings
            </h1>
            <p class="text-xs text-secondary-text mt-0.5">Manage your personal profile photo and login password</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Form (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Profile Photo Update Card -->
            <div class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden" x-data="{ photoPreview: null }">
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="camera" class="w-4 h-4 text-secondary"></i> Update Profile Picture
                    </h3>
                    <span class="text-[11px] text-secondary-text">JPG, PNG, WEBP (Max 5MB)</span>
                </div>

                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <!-- Current Photo / Live Preview -->
                        <div class="relative shrink-0">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary/20 shadow-md">
                            </template>
                            <template x-if="!photoPreview">
                                <img src="{{ auth()->user()->profile_photo_url }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary/20 shadow-md"
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1E3A5F&color=FFFFFF&bold=true&size=128';">
                            </template>
                            <span class="absolute -bottom-1.5 -right-1.5 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-md">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 w-full space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-primary-text mb-1">Select New Image</label>
                                <input type="file" 
                                       name="photo" 
                                       id="profilePhotoInput"
                                       accept="image/png, image/jpeg, image/jpg, image/webp" 
                                       required
                                       @change="
                                           const file = $event.target.files[0];
                                           if (file) {
                                               const reader = new FileReader();
                                               reader.onload = (e) => { photoPreview = e.target.result; };
                                               reader.readAsDataURL(file);
                                           }
                                       "
                                       class="w-full text-xs text-secondary-text file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                                @error('photo')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <p class="text-[11px] text-secondary-text">
                                This picture will be displayed on your mobile dashboard, leaderboards, service reports, and system top bar.
                            </p>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary hover:bg-secondary/90 text-white text-xs font-bold shadow-sm transition-colors flex items-center gap-2">
                                <i data-lucide="upload" class="w-4 h-4"></i> Save Profile Picture
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Password Form -->
            <form action="{{ route('password.update') }}" method="POST" class="bg-surface rounded-xl border border-app-border shadow-sm overflow-hidden">
                @csrf
                <div class="p-4 border-b border-app-border bg-app-bg/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-app-success"></i> Credential Verification
                    </h3>
                    <span class="text-[11px] text-secondary-text">User: {{ auth()->user()->name }} ({{ auth()->user()->phone_no }})</span>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-primary-text mb-1.5">
                            Current Password <span class="text-app-error">*</span>
                        </label>
                        <input type="password" name="current_password" required placeholder="Enter your existing password" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        @error('current_password')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-app-border">
                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                New Password <span class="text-app-error">*</span>
                            </label>
                            <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                            @error('password')<p class="text-[11px] text-app-error mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-primary-text mb-1.5">
                                Confirm New Password <span class="text-app-error">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required placeholder="Retype new password" class="w-full text-xs rounded-lg border-app-border bg-white focus:ring-secondary focus:border-secondary py-2.5 px-3">
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-app-bg/30 border-t border-app-border flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg border border-app-border text-xs font-medium text-secondary-text hover:bg-app-bg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <h4 class="text-xs font-bold text-primary-text flex items-center gap-2">
                    <i data-lucide="badge-check" class="w-4 h-4 text-secondary"></i> Active Account Info
                </h4>
                <div class="text-xs space-y-2 pt-2 border-t border-app-border">
                    <div class="flex justify-between text-secondary-text">
                        <span>Staff Name:</span>
                        <span class="font-bold text-primary-text">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between text-secondary-text">
                        <span>Role:</span>
                        <span class="font-bold text-primary-text capitalize">{{ auth()->user()->role }}</span>
                    </div>
                    <div class="flex justify-between text-secondary-text">
                        <span>Department:</span>
                        <span class="font-bold text-primary-text">{{ auth()->user()->department ?? 'Field Operations' }}</span>
                    </div>
                    <div class="flex justify-between text-secondary-text">
                        <span>Mobile No:</span>
                        <span class="font-bold text-primary-text">{{ auth()->user()->phone_no }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface rounded-xl border border-app-border shadow-sm p-5 space-y-3">
                <h4 class="text-xs font-bold text-primary-text flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-secondary"></i> Password Security Policy
                </h4>
                <ul class="text-[11px] text-secondary-text space-y-2 list-disc list-inside leading-relaxed">
                    <li>Never share your field technician login with external parties.</li>
                    <li>All attendance clock-ins, cash collections, and status transitions are cryptographically logged with your user ID.</li>
                    <li>If you suspect unauthorized session activity, change your password immediately.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
