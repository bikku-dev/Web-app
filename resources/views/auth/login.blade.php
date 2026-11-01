<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Bhumi Technocare ERP</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#1E3A5F',
                        secondary: '#3B82B8',
                        accent: '#F59E0B',
                        brandDark: '#0A1424',
                        brandNavy: '#0F1E36',
                        bg: '#F8FAFC',
                        surface: '#FFFFFF',
                        textMain: '#0F172A',
                        textMuted: '#64748B',
                        success: '#10B981',
                        borderMain: '#E2E8F0',
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="h-full bg-surface text-textMain font-sans antialiased" x-data="{ 
    showPassword: false, 
    forgotModalOpen: false,
    phoneInput: '{{ old('phone_no') }}',
    passwordInput: '',
    fillDemo(role) {
        if (role === 'admin') {
            this.phoneInput = '9999999999';
            this.passwordInput = 'password123';
        } else {
            this.phoneInput = '9876543210';
            this.passwordInput = 'password123';
        }
    }
}">

    <!-- Universal Toast Alerts -->
    @include('partials.toast')

    <!-- FULL PAGE SPLIT SCREEN LAYOUT -->
    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        <!-- ================= LEFT HERO / BRANDING COLUMN (Hidden on Mobile, 50% on Desktop) ================= -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-brandDark via-brandNavy to-primary p-12 xl:p-16 flex-col justify-between overflow-hidden text-white">
            
            <!-- Background Decorative Grid & Glow -->
            <div class="absolute inset-0 bg-grid-pattern opacity-60 pointer-events-none"></div>
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-accent/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header Logo & Name -->
            <div class="relative z-10 flex items-center space-x-3.5">
                <div class="w-13 h-13 rounded-2xl bg-white/10 p-1.5 backdrop-blur-md border border-white/20 shadow-2xl flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/app_logo.png') }}" 
                         alt="Bhumi Technocare Logo" 
                         class="w-10 h-10 object-contain rounded-xl"
                         onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white block">Bhumi Technocare</span>
                    <span class="text-xs text-secondary font-semibold uppercase tracking-wider block">Field Service & Operations ERP</span>
                </div>
            </div>

            <!-- Center Feature Showcase & Value Proposition -->
            <div class="relative z-10 my-auto py-10 max-w-lg">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-amber-300 backdrop-blur-sm mb-6">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    <span>Industrial Machinery ERP Platform</span>
                </div>

                <h1 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight tracking-tight mb-4">
                    Reliable Breakdown Service & Field Management
                </h1>
                
                <p class="text-sm xl:text-base text-slate-300 leading-relaxed mb-8">
                    Streamlining industrial machine repairs, live selfie attendance, and field billing collections across Gujarat.
                </p>

                <!-- Value Highlights -->
                <div class="space-y-4">
                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-xl bg-secondary/25 text-sky-300 flex items-center justify-center shrink-0">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Smart Ticket Dispatch</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Automated breakdown allocation with SLA & due date monitoring.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/25 text-emerald-300 flex items-center justify-center shrink-0">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Live Selfie Attendance</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Biometric photo snapshot verification for field engineers.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/25 text-amber-300 flex items-center justify-center shrink-0">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Spot Billing & Cash Handover</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Instant PDF invoices and zero-leakage cash collection tracking.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Metrics & Status -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span>Systems Operational</span>
                </div>
                <div>
                    <span>Surat, Gujarat • India</span>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT FORM COLUMN (Full Width on Mobile, 50% on Desktop) ================= -->
        <div class="w-full lg:w-1/2 min-h-screen bg-surface flex flex-col justify-between p-6 sm:p-10 lg:p-14 xl:p-18">
            
            <!-- Mobile Brand Bar (Visible only on mobile/tablet) -->
            <div class="lg:hidden flex items-center justify-between pb-6 mb-6 border-b border-borderMain">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/app_logo.png') }}" 
                         alt="Bhumi Technocare Logo" 
                         class="w-10 h-10 rounded-xl object-contain shadow-md"
                         onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                    <div>
                        <span class="font-extrabold text-base text-textMain block">Bhumi Technocare</span>
                        <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Field Operations ERP</span>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-bg border border-borderMain text-textMuted">
                    v2.5
                </span>
            </div>

            <!-- Center Form Content Container -->
            <div class="w-full max-w-md mx-auto my-auto py-4">
                
                <!-- Desktop Welcome Header -->
                <div class="mb-8">
                    <div class="hidden lg:flex items-center space-x-3 mb-6">
                        <img src="{{ asset('images/app_logo.png') }}" 
                             alt="Bhumi Technocare Logo" 
                             class="w-12 h-12 rounded-2xl object-contain shadow-md border border-borderMain p-0.5"
                             onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                        <div>
                            <span class="font-extrabold text-lg text-textMain block leading-tight">Bhumi Technocare System</span>
                            <span class="text-xs text-textMuted font-medium">Enterprise Field Service ERP</span>
                        </div>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-textMain tracking-tight">Sign in to your portal</h2>
                    <p class="text-sm text-textMuted mt-1.5">Enter your registered mobile number and password to continue</p>
                </div>

                <!-- One-Click Demo Credentials Pill (Helpful for quick access) -->
                <div class="mb-6 p-3 rounded-2xl bg-bg border border-borderMain">
                    <div class="text-[11px] font-bold text-textMuted mb-2 flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <i data-lucide="key" class="w-3.5 h-3.5 text-secondary"></i>
                            Quick Test Logins:
                        </span>
                        <span class="text-[10px] text-secondary font-semibold">Click to auto-fill</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="fillDemo('admin')" 
                                class="flex-1 py-2 px-2.5 rounded-xl bg-surface border border-borderMain hover:border-secondary hover:text-secondary text-xs font-bold transition flex flex-col items-center justify-center gap-0.5 shadow-2xs active:scale-95 text-center">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span>Admin Portal</span>
                            </div>
                            <span class="text-[10px] text-textMuted font-normal">9999999999 • password123</span>
                        </button>
                        <button type="button" 
                                @click="fillDemo('engineer')" 
                                class="flex-1 py-2 px-2.5 rounded-xl bg-surface border border-borderMain hover:border-emerald-600 hover:text-emerald-700 text-xs font-bold transition flex flex-col items-center justify-center gap-0.5 shadow-2xs active:scale-95 text-center">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Field Engineer</span>
                            </div>
                            <span class="text-[10px] text-textMuted font-normal">9876543210 • password123</span>
                        </button>
                    </div>
                </div>

                <!-- Main Login Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Mobile Number Field -->
                    <div>
                        <label for="phone_no" class="block text-xs font-bold text-textMain uppercase tracking-wider mb-1.5">
                            Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-textMuted font-bold text-xs">
                                <span>+91</span>
                            </div>
                            <input type="tel" 
                                   id="phone_no" 
                                   name="phone_no" 
                                   x-model="phoneInput"
                                   required 
                                   maxlength="10"
                                   placeholder="e.g. 9876543210" 
                                   class="w-full pl-12 pr-4 py-3 rounded-2xl border @error('phone_no') border-rose-500 @else border-borderMain @enderror focus:border-secondary focus:ring-4 focus:ring-secondary/15 outline-none text-sm text-textMain transition bg-bg/60 font-semibold tracking-wide">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-textMuted">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                        </div>
                        @error('phone_no')
                            <p class="text-xs font-bold text-rose-600 mt-1.5 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field with Visibility Toggle -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-textMain uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" 
                                    @click="forgotModalOpen = true" 
                                    class="text-xs font-bold text-secondary hover:text-primary transition">
                                Forgot password?
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-textMuted">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   x-model="passwordInput"
                                   required 
                                   placeholder="••••••••" 
                                   class="w-full pl-10 pr-12 py-3 rounded-2xl border @error('password') border-rose-500 @else border-borderMain @enderror focus:border-secondary focus:ring-4 focus:ring-secondary/15 outline-none text-sm text-textMain transition bg-bg/60 font-medium">
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-textMuted hover:text-textMain transition">
                                <i data-lucide="eye" class="w-4 h-4" x-show="!showPassword"></i>
                                <i data-lucide="eye-off" class="w-4 h-4" x-show="showPassword" x-cloak></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs font-bold text-rose-600 mt-1.5 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2.5 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-borderMain text-primary focus:ring-secondary">
                            <span class="text-xs font-medium text-textMuted">Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-primary to-secondary hover:from-slate-900 hover:to-primary text-white font-extrabold text-sm shadow-xl shadow-primary/25 hover:shadow-2xl active:scale-[0.98] transition transform flex items-center justify-center space-x-2">
                            <span>Sign In to Dashboard</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>

                <!-- Security & Provisioning Note -->
                <div class="mt-8 text-center pt-6 border-t border-borderMain">
                    <p class="text-xs text-textMuted flex items-center justify-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-secondary shrink-0"></i>
                        <span>Engineer accounts are provisioned & managed by system administrator</span>
                    </p>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="w-full text-center text-xs text-textMuted pt-6">
                <span>&copy; {{ date('Y') }} <strong>Bhumi Technocare</strong>. All rights reserved.</span>
            </div>
        </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div x-show="forgotModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary/75 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0" @click="forgotModalOpen = false"></div>

        <div class="relative w-full max-w-sm bg-surface rounded-3xl shadow-2xl border border-borderMain p-6 z-10 text-center"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="w-12 h-12 rounded-2xl bg-secondary/15 text-secondary mx-auto flex items-center justify-center mb-4">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-extrabold text-textMain">Password Reset Help</h3>
            <p class="text-xs text-textMuted mt-2 leading-relaxed">
                For security reasons, password resets are handled by system administrators. Please contact your company office or administrator to reset your credentials.
            </p>
            <div class="mt-5 space-y-2">
                <a href="tel:9999999999" class="w-full py-2.5 rounded-xl bg-primary text-white text-xs font-bold flex items-center justify-center space-x-1.5 transition">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5 text-accent"></i>
                    <span>Call Admin Helpdesk</span>
                </a>
                <button type="button" @click="forgotModalOpen = false" class="w-full py-2 rounded-xl text-xs font-bold text-textMuted hover:text-textMain transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
