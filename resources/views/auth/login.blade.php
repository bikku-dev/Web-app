<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0F1E36">
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
                        primaryDark: '#12253E',
                        secondary: '#2563EB',
                        secondaryDark: '#1D4ED8',
                        accent: '#F59E0B',
                        brandDark: '#070E1A',
                        brandNavy: '#0F1E36',
                        brandDeep: '#162B45',
                        bg: '#F8FAFC',
                        surface: '#FFFFFF',
                        textMain: '#0F172A',
                        textMuted: '#64748B',
                        success: '#10B981',
                        borderMain: '#E2E8F0',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                        'glow': '0 0 35px -5px rgba(37, 99, 235, 0.3)',
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
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 22px 22px;
        }
        /* Mobile touch tap optimization */
        button, a, input {
            touch-action: manipulation;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-textMain font-sans antialiased selection:bg-secondary/20 selection:text-secondary" 
      x-data="{ 
          showPassword: false, 
          forgotModalOpen: false,
          loading: false
      }">

    <!-- Universal Toast Alerts -->
    @include('partials.toast')

    <!-- FULL PAGE SPLIT SCREEN LAYOUT -->
    <div class="min-h-screen w-full flex flex-col lg:flex-row overflow-x-hidden">

        <!-- ================= LEFT HERO / BRAND SHOWCASE (Visible on Desktop / Laptop, 50% split) ================= -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-[52%] relative bg-gradient-to-br from-brandDark via-brandNavy to-brandDeep p-10 xl:p-14 2xl:p-16 flex-col justify-between overflow-hidden text-white select-none">
            
            <!-- Ambient Glowing Lighting Meshes -->
            <div class="absolute inset-0 bg-grid-pattern opacity-60 pointer-events-none"></div>
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-secondary/20 rounded-full blur-[110px] pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-accent/15 rounded-full blur-[110px] pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-80 h-80 bg-sky-500/10 rounded-full blur-[120px] pointer-events-none"></div>

            <!-- Top Header: Official Logo & Brand Identity -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center space-x-3.5 p-2 pr-5 rounded-2xl bg-white/[0.06] border border-white/10 backdrop-blur-md shadow-glass">
                    <div class="w-11 h-11 rounded-xl bg-white/10 p-1 flex items-center justify-center shrink-0 border border-white/15 shadow-sm">
                        <img src="{{ asset('images/app_logo.png') }}" 
                             alt="Bhumi Technocare Logo" 
                             class="w-9 h-9 object-contain rounded-lg"
                             onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                    </div>
                    <div>
                        <span class="text-base font-extrabold tracking-tight text-white block leading-tight">Bhumi Technocare</span>
                        <span class="text-[10px] text-sky-300 font-bold uppercase tracking-wider block">Field Operations & FSM ERP</span>
                    </div>
                </div>

                <div class="hidden xl:flex items-center space-x-2 px-3 py-1.5 rounded-full bg-white/[0.06] border border-white/10 text-[11px] text-slate-300 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-semibold">v2.6 Enterprise Cloud</span>
                </div>
            </div>

            <!-- Center Feature Showcase & Value Proposition -->
            <div class="relative z-10 my-auto py-8 max-w-xl">
                <!-- Pill Badge -->
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/[0.08] border border-white/[0.14] text-xs font-bold text-amber-300 backdrop-blur-md mb-6 shadow-sm">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-accent"></i>
                    <span>Machinery Breakdown & Operations Suite</span>
                </div>

                <h1 class="text-3xl xl:text-4xl 2xl:text-5xl font-extrabold text-white leading-[1.18] tracking-tight mb-4">
                    Intelligent Field Service & Machinery Operations
                </h1>
                
                <p class="text-sm xl:text-base text-slate-300/90 leading-relaxed mb-8 max-w-lg font-normal">
                    Empowering breakdown maintenance, SLA-based technician dispatch, live selfie attendance, and field billing collections across Gujarat.
                </p>

                <!-- 3 Premium Feature Glass Cards -->
                <div class="space-y-3.5">
                    
                    <!-- Feature 1: Ticket Dispatch -->
                    <div class="group flex items-start space-x-3.5 p-3.5 xl:p-4 rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.08] hover:border-white/[0.16] backdrop-blur-md transition-all duration-300 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500/20 to-blue-500/30 text-sky-300 border border-sky-400/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                            <i data-lucide="wrench" class="w-5 h-5 text-sky-300"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-white tracking-tight">Smart Breakdown Ticket Dispatch</h4>
                            <p class="text-xs text-slate-300/80 mt-0.5 leading-relaxed">
                                Sequential ticketing (Computer, Printer, CCTV), real-time SLA urgency tracking, and technician allocation.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2: Selfie Attendance -->
                    <div class="group flex items-start space-x-3.5 p-3.5 xl:p-4 rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.08] hover:border-white/[0.16] backdrop-blur-md transition-all duration-300 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/30 text-emerald-300 border border-emerald-400/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                            <i data-lucide="camera" class="w-5 h-5 text-emerald-300"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-white tracking-tight">Geo-Verified Selfie Attendance</h4>
                            <p class="text-xs text-slate-300/80 mt-0.5 leading-relaxed">
                                Biometric camera snapshots with timestamps for on-site field staff clock-in and clock-out.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3: Spot Billing -->
                    <div class="group flex items-start space-x-3.5 p-3.5 xl:p-4 rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.08] hover:border-white/[0.16] backdrop-blur-md transition-all duration-300 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500/20 to-orange-500/30 text-amber-300 border border-amber-400/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                            <i data-lucide="receipt" class="w-5 h-5 text-amber-300"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-white tracking-tight">On-Spot Invoicing & Cash Handover</h4>
                            <p class="text-xs text-slate-300/80 mt-0.5 leading-relaxed">
                                Instant customer job sheets, GST tax invoices, and verified cash handover reconciliation.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Trust & Reliability Bar -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center space-x-2.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-300 font-medium">All Cloud Services Operational</span>
                </div>
                <div class="flex items-center space-x-1.5 text-slate-400 font-medium">
                    <i data-lucide="shield-check" class="w-4 h-4 text-sky-400"></i>
                    <span>Surat, Gujarat • 256-Bit SSL Secured</span>
                </div>
            </div>

        </div>

        <!-- ================= RIGHT COLUMN: LOGIN FORM (Full Width on Mobile, 50% on Desktop) ================= -->
        <div class="w-full lg:w-1/2 xl:w-[48%] min-h-screen bg-white flex flex-col justify-between p-5 sm:p-8 md:p-12 lg:p-12 xl:p-16 relative">
            
            <!-- Mobile Brand Bar (Visible strictly on mobile & tablet < 1024px) -->
            <div class="lg:hidden flex items-center justify-between pb-4 mb-4 border-b border-slate-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 p-1 flex items-center justify-center shrink-0 shadow-sm border border-slate-800">
                        <img src="{{ asset('images/app_logo.png') }}" 
                             alt="Bhumi Technocare Logo" 
                             class="w-8 h-8 object-contain rounded-lg"
                             onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                    </div>
                    <div>
                        <span class="font-extrabold text-base text-slate-900 block leading-tight">Bhumi Technocare</span>
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Field Operations ERP</span>
                    </div>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700">
                    Enterprise Portal
                </span>
            </div>

            <!-- Center Form Content Container -->
            <div class="w-full max-w-md mx-auto my-auto py-2 sm:py-4">
                
                <!-- Desktop Welcome Header -->
                <div class="mb-7">
                    <div class="hidden lg:flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 p-1.5 flex items-center justify-center shrink-0 shadow-md border border-slate-800">
                            <img src="{{ asset('images/app_logo.png') }}" 
                                 alt="Bhumi Technocare Logo" 
                                 class="w-9 h-9 object-contain rounded-xl"
                                 onerror="this.onerror=null; this.src='{{ asset('images/bhumi_logo.jpg') }}';">
                        </div>
                        <div>
                            <span class="font-extrabold text-lg text-slate-900 block leading-tight">Bhumi Technocare</span>
                            <span class="text-xs text-slate-500 font-semibold tracking-wide">Enterprise Machinery ERP</span>
                        </div>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Sign in to your portal</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 font-medium leading-relaxed">
                        Enter your registered mobile number and password to access your dashboard
                    </p>
                </div>

                <!-- Validation Error Summary Banner (If any) -->
                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5 shadow-2xs animate-shake">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                        <div class="min-w-0">
                            <span class="font-bold block">Authentication Notice</span>
                            <span class="text-rose-700 leading-normal">{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                <!-- Main Login Form -->
                <form action="{{ route('login') }}" 
                      method="POST" 
                      @submit="loading = true" 
                      class="space-y-4 sm:space-y-4.5">
                    @csrf

                    <!-- Mobile Number Input Field -->
                    <div>
                        <label for="phone_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <!-- Country Code Prefix Pill -->
                            <div class="absolute left-3.5 flex items-center pointer-events-none space-x-1.5 text-xs font-bold text-slate-700 pr-2.5 border-r border-slate-300">
                                <span>🇮🇳</span>
                                <span>+91</span>
                            </div>
                            
                            <input type="tel" 
                                   id="phone_no" 
                                   name="phone_no" 
                                   value="{{ old('phone_no') }}"
                                   required 
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   autofocus
                                   placeholder="10-digit mobile number" 
                                   class="w-full text-base sm:text-sm pl-20 pr-10 py-3 sm:py-3.5 rounded-2xl border @error('phone_no') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 @enderror focus:border-secondary focus:ring-4 focus:ring-secondary/15 outline-none font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal bg-slate-50/50 focus:bg-white transition duration-200">
                            
                            <div class="absolute right-3.5 pointer-events-none text-slate-400">
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

                    <!-- Password Input Field with Visibility Toggle -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" 
                                    @click="forgotModalOpen = true" 
                                    class="text-xs font-bold text-secondary hover:text-secondaryDark transition cursor-pointer">
                                Forgot password?
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 pointer-events-none text-slate-400">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>

                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter account password" 
                                   class="w-full text-base sm:text-sm pl-11 pr-12 py-3 sm:py-3.5 rounded-2xl border @error('password') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 @enderror focus:border-secondary focus:ring-4 focus:ring-secondary/15 outline-none font-medium text-slate-900 placeholder:text-slate-400 bg-slate-50/50 focus:bg-white transition duration-200">

                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    aria-label="Toggle password visibility"
                                    class="absolute right-3.5 p-1 text-slate-400 hover:text-slate-700 transition cursor-pointer rounded-lg hover:bg-slate-100">
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

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center space-x-2.5 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded-md border-slate-300 text-primary focus:ring-secondary cursor-pointer">
                            <span class="text-xs font-semibold text-slate-600">Keep me signed in on this device</span>
                        </label>
                    </div>

                    <!-- Sign In Action Button with Loading Spinner -->
                    <div class="pt-2">
                        <button type="submit" 
                                :disabled="loading" 
                                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-primary via-[#1e3a5f] to-secondary hover:from-primaryDark hover:to-secondaryDark text-white font-extrabold text-sm sm:text-base shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/35 active:scale-[0.99] transition duration-200 flex items-center justify-center space-x-2 disabled:opacity-70 disabled:cursor-not-allowed cursor-pointer">
                            
                            <!-- Default State -->
                            <span x-show="!loading" class="inline-flex items-center space-x-2">
                                <span>Sign In to Dashboard</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>

                            <!-- Processing Authentication State -->
                            <span x-show="loading" x-cloak class="inline-flex items-center space-x-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Authenticating...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Security & Provisioning Note -->
                <div class="mt-8 pt-5 border-t border-slate-200/80 text-center">
                    <p class="text-xs text-slate-500 font-medium flex items-center justify-center gap-1.5 leading-relaxed">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span>Role-based access for Administrators and Field Engineers</span>
                    </p>
                </div>
            </div>

            <!-- Page Bottom Footer -->
            <div class="w-full text-center text-xs text-slate-500 pt-6">
                <span>&copy; {{ date('Y') }} <strong class="text-slate-700">Bhumi Technocare</strong>. All rights reserved.</span>
            </div>

        </div>
    </div>

    <!-- FORGOT PASSWORD MODAL (Enterprise Helpdesk) -->
    <div x-show="forgotModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0" @click="forgotModalOpen = false"></div>

        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 z-10 text-center"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 mx-auto flex items-center justify-center mb-4 shadow-sm p-3">
                <i data-lucide="shield-alert" class="w-7 h-7"></i>
            </div>
            
            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Password Reset Assistance</h3>
            
            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                For security compliance, employee password resets are managed directly by the system administrator. Please reach out to your operations head or admin office.
            </p>

            <div class="mt-6 space-y-2">
                <a href="tel:9999999999" 
                   class="w-full py-2.5 rounded-xl bg-primary hover:bg-primaryDark text-white text-xs font-bold flex items-center justify-center space-x-1.5 transition shadow-sm">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>Contact Operations Admin</span>
                </a>
                <button type="button" 
                        @click="forgotModalOpen = false" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                    Close Window
                </button>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
