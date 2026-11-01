<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Bhumi Technocare System</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: '#42919e',
                        primaryDark: '#2c6c77',
                        secondary: '#42919e',
                        accent: '#FFA500',
                        bg: '#F8FAFC',
                        surface: '#FFFFFF',
                        textMain: '#0F172A',
                        textMuted: '#64748B',
                        success: '#32CD32',
                        warning: '#FFA500',
                        error: '#DC2626',
                        borderMain: '#E2E8F0',
                        // Theme aliases for high compatibility & contrast:
                        'app-border': '#CBD5E1',
                        'primary-text': '#0F172A',
                        'secondary-text': '#64748B',
                        'app-bg': '#F8FAFC',
                        'app-surface': '#FFFFFF',
                        'app-success': '#32CD32',
                        'app-error': '#DC2626',
                        'app-warning': '#FFA500',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js Global Clock/Camera Modal Store -->
    <script>
        window.openClockModal = function(type = 'in') {
            if (window.Alpine && Alpine.store && Alpine.store('clockModal')) {
                Alpine.store('clockModal').show(type);
            } else {
                document.addEventListener('alpine:init', () => {
                    Alpine.store('clockModal').show(type);
                }, { once: true });
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.store('clockModal', {
                open: false,
                type: 'in',
                cameraActive: false,
                cameraLoading: false,
                photoSnapped: false,
                photoData: null,
                cameraError: null,
                stream: null,

                show(type = 'in') {
                    this.type = type;
                    this.open = true;
                    this.photoSnapped = false;
                    this.photoData = null;
                    this.cameraError = null;
                    this.cameraLoading = true;
                    this.cameraActive = false;
                    setTimeout(() => {
                        this.startCamera();
                        if (window.lucide) lucide.createIcons();
                    }, 100);
                },

                close() {
                    this.stopCamera();
                    this.open = false;
                    this.photoSnapped = false;
                    this.photoData = null;
                    this.cameraError = null;
                },

                async startCamera() {
                    this.cameraError = null;
                    this.photoSnapped = false;
                    this.photoData = null;
                    this.cameraLoading = true;
                    this.cameraActive = false;

                    this.stopCamera();

                    // Check for WebRTC mediaDevices support (requires Secure Context / HTTPS / localhost)
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraLoading = false;
                        this.cameraActive = false;
                        this.cameraError = 'Live webcam requires HTTPS or localhost. Tap below to use your Phone Camera.';
                        return;
                    }

                    try {
                        let stream = null;
                        try {
                            // Try user-facing front selfie camera first
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { 
                                    facingMode: 'user',
                                    width: { ideal: 1280 },
                                    height: { ideal: 720 }
                                },
                                audio: false
                            });
                        } catch(frontErr) {
                            // Fallback to any default video device
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: true,
                                audio: false
                            });
                        }

                        this.stream = stream;
                        this.cameraActive = true;
                        this.cameraLoading = false;

                        setTimeout(() => {
                            const video = document.getElementById('selfieVideo');
                            if (video && this.stream) {
                                video.srcObject = this.stream;
                                video.setAttribute('playsinline', 'true');
                                video.setAttribute('autoplay', 'true');
                                video.muted = true;
                                const playPromise = video.play();
                                if (playPromise !== undefined) {
                                    playPromise.catch(e => console.warn('Autoplay error', e));
                                }
                            }
                            if (window.lucide) lucide.createIcons();
                        }, 50);

                    } catch(err) {
                        console.warn('Live camera access error:', err);
                        this.cameraActive = false;
                        this.cameraLoading = false;
                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                            this.cameraError = 'Camera permission blocked. Tap "Open Device Camera" or allow access in browser.';
                        } else {
                            this.cameraError = 'Live camera stream unavailable. Tap "Open Device Camera" below.';
                        }
                        if (window.lucide) lucide.createIcons();
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        try {
                            this.stream.getTracks().forEach(t => t.stop());
                        } catch(e) {}
                        this.stream = null;
                    }
                    const video = document.getElementById('selfieVideo');
                    if (video) {
                        video.srcObject = null;
                    }
                    this.cameraActive = false;
                    this.cameraLoading = false;
                },

                snapLivePhoto() {
                    const video = document.getElementById('selfieVideo');
                    if (!video || !this.cameraActive) return;

                    const vWidth = video.videoWidth || 640;
                    const vHeight = video.videoHeight || 480;

                    const canvas = document.createElement('canvas');
                    // Resize to max 900px for instant upload & crisp preview
                    let w = vWidth;
                    let h = vHeight;
                    const maxDim = 900;
                    if (w > maxDim || h > maxDim) {
                        if (w > h) {
                            h = Math.round((h * maxDim) / w);
                            w = maxDim;
                        } else {
                            w = Math.round((w * maxDim) / h);
                            h = maxDim;
                        }
                    }

                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');

                    // Mirror horizontally for natural selfie view
                    ctx.translate(w, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, w, h);

                    this.photoData = canvas.toDataURL('image/jpeg', 0.85);
                    this.photoSnapped = true;
                    this.stopCamera();
                    if (window.lucide) lucide.createIcons();
                },

                retakePhoto() {
                    this.photoSnapped = false;
                    this.photoData = null;
                    const fileInput = document.getElementById('deviceCameraInput');
                    if (fileInput) fileInput.value = '';
                    this.startCamera();
                },

                handleFileSelect(event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let w = img.width;
                            let h = img.height;
                            const maxDim = 900;
                            if (w > maxDim || h > maxDim) {
                                if (w > h) {
                                    h = Math.round((h * maxDim) / w);
                                    w = maxDim;
                                } else {
                                    w = Math.round((w * maxDim) / h);
                                    h = maxDim;
                                }
                            }
                            canvas.width = w;
                            canvas.height = h;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, w, h);

                            this.photoData = canvas.toDataURL('image/jpeg', 0.85);
                            this.photoSnapped = true;
                            this.stopCamera();
                            if (window.lucide) lucide.createIcons();
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1E3A5F">
    <meta name="mobile-web-app-capable" content="yes">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F5F7FA; color: #0F172A; }

        /* Mobile App Feel & Touch Optimization */
        * {
            -webkit-tap-highlight-color: transparent;
        }
        @media (max-width: 1024px) {
            button, a, input, select, textarea {
                touch-action: manipulation;
            }
        }

        /* Form Inputs & Selects High-Visibility Border Styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        input[type="tel"],
        input[type="search"],
        input[type="url"],
        select,
        textarea {
            border: 1.5px solid #94A3B8 !important; /* Visible Slate-400 border */
            background-color: #FFFFFF !important;
            color: #0F172A !important;
            border-radius: 0.75rem !important; /* 12px rounded-xl */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-size: 0.8125rem; /* text-xs/sm */
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="datetime-local"]:focus,
        input[type="tel"]:focus,
        input[type="search"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            border-color: #2563EB !important; /* Vibrant primary blue on focus */
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
            background-color: #FFFFFF !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94A3B8 !important;
        }

        label {
            color: #1E293B;
            font-weight: 600;
        }

        /* Global Modern Sleek Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
            border: 1px solid #F1F5F9;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 #F1F5F9;
        }

        /* Dark Theme Sidebar Scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) rgba(0, 0, 0, 0.2);
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-bg text-textMain antialiased" x-data="{ mobileMenuOpen: false, sidebarCollapsed: false, threeDotsOpen: false }">

    @php
        $myToday = \App\Models\Attendance::where('user_id', auth()->id())
            ->whereDate('recorded_at', \Carbon\Carbon::today())
            ->latest('recorded_at')
            ->first();
        $isClocked = $myToday && $myToday->is_clock_in;
    @endphp

    <div class="min-h-screen flex overflow-x-hidden">
        <!-- DESKTOP SIDEBAR (Ultra-Premium Executive Dark Theme, Collapsible) -->
        <aside :class="sidebarCollapsed ? 'lg:-ml-64' : 'lg:ml-0'" 
               class="hidden lg:flex lg:flex-col lg:w-64 bg-[#0f172a] text-white flex-shrink-0 z-30 shadow-2xl lg:sticky lg:top-0 lg:h-screen lg:overflow-hidden border-r border-slate-800 transition-all duration-300 ease-in-out">
            <!-- Brand Logo Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 bg-black/20 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 flex items-center justify-center text-slate-950 font-black text-lg shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform shrink-0">
                        BT
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold text-sm tracking-tight text-white leading-tight truncate">Bhumi Technocare</div>
                        <div class="text-[10px] text-amber-300 font-bold tracking-wider uppercase">Machinery FSM</div>
                    </div>
                </a>
                <!-- Collapse Button -->
                <button type="button" @click="sidebarCollapsed = true" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Hide Sidebar">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto sidebar-scroll text-sm min-h-0">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white font-medium' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span class="truncate">Dashboard</span>
                </a>

                <!-- Service Complaints -->
                <a href="{{ route('complaints.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('complaints.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                        <span>{{ auth()->user()->isEngineer() ? 'My Tasks / Jobs' : 'Service Tickets' }}</span>
                    </div>
                    @php
                        $openCount = \App\Models\Complaint::whereNotIn('status', ['completed', 'cancelled'])
                            ->when(auth()->user()->isEngineer(), fn($q) => $q->where('engineer_id', auth()->id()))
                            ->count();
                    @endphp
                    @if($openCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-accent text-slate-950">{{ $openCount }}</span>
                    @endif
                </a>

                <!-- Billing & Invoicing -->
                <a href="{{ route('billing.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('billing.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                    <span>{{ auth()->user()->isEngineer() ? 'My Bills & Cash' : 'Billing & Cash' }}</span>
                </a>

                <!-- Expenses -->
                <a href="{{ route('expenses.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('expenses.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span>Field Expenses</span>
                </a>

                <!-- Attendance -->
                <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('attendance.index') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    <span>Daily Attendance</span>
                </a>

                @if(auth()->user()->isAdmin())
                <!-- Leaderboard (Admin Only) -->
                <a href="{{ route('attendance.leaderboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('attendance.leaderboard') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="trophy" class="w-5 h-5 text-amber-400"></i>
                    <span>Hours Leaderboard</span>
                </a>
                @endif

                <!-- Notice Board -->
                <a href="{{ route('notices.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('notices.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span>Notice Board</span>
                </a>

                <!-- Organizations -->
                <a href="{{ route('organizations.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('organizations.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                    <span>Firms / Clients</span>
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('customers.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Customer Directory</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Management</p>
                    </div>

                    <!-- Engineers -->
                    <a href="{{ route('engineers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('engineers.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="hard-hat" class="w-5 h-5"></i>
                        <span>Field Engineers</span>
                    </a>

                    <!-- Machinery Master -->
                    <a href="{{ route('masters.products') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('masters.products') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="cpu" class="w-5 h-5"></i>
                        <span>Machines & Equipment</span>
                    </a>

                    <!-- Service Types Master -->
                    <a href="{{ route('masters.services') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('masters.services') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="cog" class="w-5 h-5"></i>
                        <span>Service Types</span>
                    </a>

                    <!-- Areas & Cities -->
                    <a href="{{ route('masters.areas') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('masters.areas') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                        <span>Areas & Cities</span>
                    </a>

                    <!-- Audit Logs -->
                    <a href="{{ route('audit.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('audit.*') ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-900/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                        <span>Audit Security Logs</span>
                    </a>
                @endif
            </nav>

            @if(!auth()->user()->isAdmin())
            <!-- Engineer Quick Attendance Widget in Sidebar -->
            <div class="p-3 mx-3 my-2 rounded-2xl bg-white/5 border border-white/10 shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-300">Today's Shift</span>
                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full {{ $isClocked ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700/80 text-slate-300 border border-slate-600/40' }}">
                        {{ $isClocked ? 'Clocked In' : 'Clocked Out' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" @click="window.openClockModal('in')" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold flex items-center justify-center space-x-1.5 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed" {{ $isClocked ? 'disabled' : '' }}>
                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                        <span>Clock In</span>
                    </button>
                    <button type="button" @click="window.openClockModal('out')" class="px-2.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-[11px] font-bold flex items-center justify-center space-x-1.5 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed" {{ !$isClocked ? 'disabled' : '' }}>
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Clock Out</span>
                    </button>
                </div>
            </div>
            @endif

            <!-- User Profile Bottom Bar -->
            <div class="p-3 border-t border-slate-800 flex items-center justify-between bg-black/30 shrink-0">
                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-xl object-cover shrink-0 shadow-md ring-2 ring-white/10" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1E3A5F&color=FFFFFF&bold=true&size=128';">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-600 to-teal-400 flex items-center justify-center font-black text-white text-sm shrink-0 shadow-md">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400 font-medium truncate">{{ auth()->user()->phone_no }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition">
                        <i data-lucide="power" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- TOP APP BAR / NAVBAR -->
            <header class="flex h-14 sm:h-16 bg-surface border-b border-borderMain sticky top-0 z-20 items-center justify-between px-3 sm:px-4 lg:px-6 shadow-2xs gap-2">
                
                <!-- Left: Sidebar Toggle (Desktop only when collapsed), Admin Avatar & Info -->
                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                    <!-- Laptop / Tablet / Desktop Toggle Button (Visible ONLY when sidebar is non-visible/hidden, NEVER on phones) -->
                    <button type="button" 
                            x-show="sidebarCollapsed" 
                            x-cloak
                            @click="sidebarCollapsed = false" 
                            class="hidden lg:inline-flex p-1.5 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition border border-slate-200 shadow-2xs mr-1 active:scale-95" 
                            title="Open Sidebar">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <!-- Admin Photo & Profile Details (Left Corner) -->
                    <div class="flex items-center space-x-2.5 min-w-0">
                        @if(auth()->user()->photo)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover ring-2 ring-primary/20 shadow-2xs shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=42919e&color=FFFFFF&bold=true&size=128';">
                        @else
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-2xs shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 leading-tight truncate">
                                    {{ auth()->user()->name }}
                                </h2>
                                @if(auth()->user()->isAdmin())
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-teal-100 text-teal-800 border border-teal-200">
                                        ADMIN
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase {{ $isClocked ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $isClocked ? 'ON DUTY' : 'OFF DUTY' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-textMuted truncate hidden sm:block font-medium">
                                {{ auth()->user()->isEngineer() ? 'Field Service Operations' : 'Machinery Service & Breakdown ERP' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right: Notice Bell & ONLY 3-DOTS MENU (No New Ticket, No Admin Photo on Right) -->
                <div class="flex items-center space-x-1 sm:space-x-1.5 shrink-0">
                    
                    <!-- Quick Clock-In / Out Button (Engineers only) -->
                    @if(!auth()->user()->isAdmin())
                        <button type="button" @click="window.openClockModal('{{ $isClocked ? 'out' : 'in' }}')" class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition shrink-0 active:scale-95 {{ $isClocked ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-600 text-white shadow-2xs' }}">
                            <i data-lucide="{{ $isClocked ? 'log-out' : 'camera' }}" class="w-3.5 h-3.5"></i>
                            <span class="text-[11px] font-bold">{{ $isClocked ? 'Clock Out' : 'Clock In' }}</span>
                        </button>
                    @endif

                    <!-- Notice Bell with Unread Badge & Interactive Dropdown -->
                    @php
                        $userLastRead = auth()->user()->last_read_notices_at;
                        $noticesQuery = \App\Models\Notice::where('is_active', true)
                            ->where(function($q) {
                                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
                            });
                        $allNoticesList = (clone $noticesQuery)->latest()->take(10)->get();
                        $unreadCount = $userLastRead 
                            ? (clone $noticesQuery)->where('created_at', '>', $userLastRead)->count()
                            : $allNoticesList->count();
                    @endphp

                    <div class="relative shrink-0" x-data="{ 
                        notifOpen: false, 
                        unreadCount: {{ $unreadCount }},
                        markSeen() {
                            if (this.unreadCount > 0) {
                                this.unreadCount = 0;
                                fetch('{{ route('notices.markAsRead') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                }).catch(function(e) { console.error(e); });
                            }
                        },
                        toggleNotif() {
                            this.notifOpen = !this.notifOpen;
                            if (this.notifOpen) {
                                this.markSeen();
                            }
                        }
                    }">
                        <button type="button" 
                                @click="toggleNotif()" 
                                class="p-2 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition relative active:scale-95 border border-slate-200/60 shadow-2xs" 
                                title="Notifications & Notices">
                            <i data-lucide="bell" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center shadow-xs ring-2 ring-surface" x-text="unreadCount">
                                </span>
                            </template>
                        </button>

                        <!-- Notifications Dropdown Popup -->
                        <div x-show="notifOpen" 
                             @click.away="notifOpen = false" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-surface rounded-2xl shadow-2xl border border-borderMain z-50 overflow-hidden divide-y divide-slate-100">
                            
                            <!-- Header -->
                            <div class="px-4 py-3 bg-slate-50 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                        <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900">Company Notices</h4>
                                </div>
                                <span class="text-[10px] text-textMuted font-semibold">
                                    {{ $allNoticesList->count() }} Updates
                                </span>
                            </div>

                            <!-- List of Notifications -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 p-1">
                                @forelse($allNoticesList as $ntc)
                                    <div class="p-3 hover:bg-slate-50 transition rounded-xl space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] font-black uppercase text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-full">
                                                Notice
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $ntc->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        <h5 class="text-xs font-bold text-slate-900 leading-snug">
                                            {{ $ntc->title }}
                                        </h5>
                                        <p class="text-[11px] text-slate-600 leading-relaxed line-clamp-3">
                                            {{ $ntc->notice }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-textMuted text-xs">
                                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto text-slate-300 mb-1"></i>
                                        <p class="font-bold text-slate-700">No active notices</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">You're all caught up!</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer -->
                            <div class="p-2.5 bg-slate-50 text-center">
                                <a href="{{ route('notices.index') }}" class="text-xs font-bold text-primary hover:underline">
                                    View All Notices &rarr;
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- ONLY 3-DOTS MENU IN TOP RIGHT CORNER (Contains all Menu Hub options + Logout) -->
                    <div class="relative shrink-0" x-data="{ threeDotsMenuOpen: false }">
                        <button type="button" 
                                @click="threeDotsMenuOpen = !threeDotsMenuOpen" 
                                class="p-2 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition active:scale-95 border border-slate-200 shadow-2xs" 
                                title="Menu & Actions">
                            <i data-lucide="more-vertical" class="w-5 h-5"></i>
                        </button>

                        <!-- 3-Dots Dropdown with ALL Menu Options + Logout -->
                        <div x-show="threeDotsMenuOpen" 
                             @click.away="threeDotsMenuOpen = false" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-64 bg-surface rounded-2xl shadow-2xl border border-borderMain py-2 z-50 divide-y divide-slate-100">
                            
                            <!-- Header Info -->
                            <div class="px-4 py-2.5 bg-slate-50/90">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-textMuted truncate">{{ auth()->user()->phone_no }} • {{ ucfirst(auth()->user()->role) }}</p>
                            </div>

                            <!-- ALL Menu Hub Navigation Options -->
                            <div class="py-1.5 text-xs max-h-[60vh] overflow-y-auto">
                                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400"></i>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('complaints.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="wrench" class="w-4 h-4 text-slate-400"></i>
                                    <span>Service Tickets</span>
                                </a>
                                <a href="{{ route('billing.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="receipt" class="w-4 h-4 text-slate-400"></i>
                                    <span>Billing & Cash Ledger</span>
                                </a>
                                <a href="{{ route('customers.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="users" class="w-4 h-4 text-slate-400"></i>
                                    <span>Customer Directory</span>
                                </a>
                                <a href="{{ route('organizations.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                                    <span>Firms & Clients</span>
                                </a>
                                <a href="{{ route('expenses.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                                    <span>Field Expenses</span>
                                </a>
                                <a href="{{ route('attendance.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                    <span>Daily Attendance</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('attendance.leaderboard') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                        <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
                                        <span>Hours Leaderboard</span>
                                    </a>
                                @endif
                                <a href="{{ route('notices.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="bell" class="w-4 h-4 text-slate-400"></i>
                                    <span>Notice Board</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('engineers.index') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                        <i data-lucide="hard-hat" class="w-4 h-4 text-slate-400"></i>
                                        <span>Field Engineers</span>
                                    </a>
                                    <a href="{{ route('menu') }}" class="flex items-center space-x-2.5 px-4 py-2 text-primary font-bold hover:bg-primary/5">
                                        <i data-lucide="grid-2x2" class="w-4 h-4 text-primary"></i>
                                        <span>Master Hub / Settings</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Account & Sign Out -->
                            <div class="pt-1.5 text-xs">
                                <a href="{{ route('password.change') }}" class="flex items-center space-x-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="key" class="w-4 h-4 text-slate-400"></i>
                                    <span>Change Password</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 font-bold text-left transition">
                                        <i data-lucide="log-out" class="w-4 h-4 text-rose-600"></i>
                                        <span>Sign Out / Logout</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </header>

            <!-- TOAST NOTIFICATIONS (BOTTOM-CENTER WHITE) -->
            @include('partials.toast')

            <!-- BODY CONTENT (pb-24 on mobile guarantees content is never covered by bottom nav) -->
            <main class="flex-1 px-3 sm:px-4 lg:px-8 py-3 sm:py-4 pb-24 lg:pb-8">
                @yield('content')
            </main>

            <!-- FOOTER (Hidden on mobile for native app feel) -->
            <footer class="hidden lg:flex bg-surface border-t border-borderMain py-3 px-8 text-center text-xs text-textMuted items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} <strong>Bhumi Technocare</strong>. Machinery Maintenance & Field Service Operations ERP.</span>
                <span>PHP 8.4 • MySQL 9.7 • Responsive Blade UI</span>
            </footer>
        </div>
    </div>

    <!-- MOBILE BOTTOM APP NAVIGATION BAR (Native Mobile App Experience: 5 Fixed Options) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-surface/98 backdrop-blur-lg border-t border-slate-200/90 shadow-[0_-3px_20px_rgba(0,0,0,0.08)] px-1 py-1 flex items-center justify-around safe-area-bottom">
        <!-- 1. Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('dashboard') ? 'text-primary font-bold' : 'text-textMuted hover:text-textMain' }}">
            <div class="relative p-1 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'stroke-[2.5]' : 'stroke-[1.8]' }}"></i>
            </div>
            <span class="text-[10.5px] tracking-tight mt-0.5 {{ request()->routeIs('dashboard') ? 'font-bold text-primary' : 'font-medium' }}">Home</span>
        </a>

        <!-- 2. Services / Jobs (Scoped to assigned jobs for engineer) -->
        <a href="{{ route('complaints.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition group relative {{ (request()->routeIs('complaints.*') && !request()->routeIs('complaints.create')) ? 'text-primary font-bold' : 'text-textMuted hover:text-textMain' }}">
            <div class="relative p-1 rounded-xl transition {{ (request()->routeIs('complaints.*') && !request()->routeIs('complaints.create')) ? 'bg-primary/10 text-primary' : '' }}">
                <i data-lucide="wrench" class="w-5 h-5 {{ (request()->routeIs('complaints.*') && !request()->routeIs('complaints.create')) ? 'stroke-[2.5]' : 'stroke-[1.8]' }}"></i>
                @php
                    $openCount = \App\Models\Complaint::whereNotIn('status', ['completed', 'cancelled'])
                        ->when(auth()->user()->isEngineer(), fn($q) => $q->where('engineer_id', auth()->id()))
                        ->count();
                @endphp
                @if($openCount > 0)
                    <span class="absolute -top-1 -right-1 px-1.5 py-0.2 rounded-full bg-accent text-slate-950 text-[9px] font-black shadow-xs ring-2 ring-surface">
                        {{ $openCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10.5px] tracking-tight mt-0.5 {{ (request()->routeIs('complaints.*') && !request()->routeIs('complaints.create')) ? 'font-bold text-primary' : 'font-medium' }}">
                {{ auth()->user()->isEngineer() ? 'Jobs' : 'Services' }}
            </span>
        </a>

        <!-- 3. CENTER ACTION: LIVE ATTENDANCE CAMERA PUNCH FOR ENGINEER, + ADD FOR ADMIN -->
        @if(auth()->user()->isEngineer())
            <!-- Central Elevated Attendance Selfie Camera Button for Engineers -->
            <button type="button" 
                    @click="window.openClockModal('{{ $isClocked ? 'out' : 'in' }}')" 
                    class="flex flex-col items-center justify-center flex-1 -mt-4 transition group focus:outline-none" 
                    title="Live Selfie Attendance Punch">
                <div class="w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-600/40 ring-4 ring-surface transition transform active:scale-90 group-hover:scale-105">
                    <i data-lucide="camera" class="w-6 h-6 stroke-[2.4]"></i>
                </div>
                <span class="text-[10px] tracking-tight mt-1 font-bold text-emerald-700">Attendance</span>
            </button>
        @else
            <!-- Admin: + Add Service / Ticket -->
            <a href="{{ route('complaints.create') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 transition group" title="Create Service Complaint">
                <div class="w-10 h-10 -mt-2 rounded-full bg-primary text-white flex items-center justify-center shadow-md shadow-primary/30 transition transform active:scale-95 group-hover:scale-105 {{ request()->routeIs('complaints.create') ? 'ring-2 ring-accent ring-offset-2 ring-offset-surface' : '' }}">
                    <i data-lucide="plus" class="w-6 h-6 stroke-[2.8]"></i>
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('complaints.create') ? 'font-bold text-primary' : 'font-semibold text-textMuted' }}">+ Add</span>
            </a>
        @endif

        <!-- 4. Expenses (for Engineer) / Engineers (for Admin) -->
        @if(auth()->user()->isEngineer())
            <a href="{{ route('expenses.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('expenses.*') ? 'text-primary font-bold' : 'text-textMuted hover:text-textMain' }}">
                <div class="relative p-1 rounded-xl transition {{ request()->routeIs('expenses.*') ? 'bg-primary/10 text-primary' : '' }}">
                    <i data-lucide="credit-card" class="w-5 h-5 {{ request()->routeIs('expenses.*') ? 'stroke-[2.5]' : 'stroke-[1.8]' }}"></i>
                </div>
                <span class="text-[10.5px] tracking-tight mt-0.5 {{ request()->routeIs('expenses.*') ? 'font-bold text-primary' : 'font-medium' }}">Expenses</span>
            </a>
        @else
            <a href="{{ route('engineers.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('engineers.*') ? 'text-primary font-bold' : 'text-textMuted hover:text-textMain' }}">
                <div class="relative p-1 rounded-xl transition {{ request()->routeIs('engineers.*') ? 'bg-primary/10 text-primary' : '' }}">
                    <i data-lucide="hard-hat" class="w-5 h-5 {{ request()->routeIs('engineers.*') ? 'stroke-[2.5]' : 'stroke-[1.8]' }}"></i>
                </div>
                <span class="text-[10.5px] tracking-tight mt-0.5 {{ request()->routeIs('engineers.*') ? 'font-bold text-primary' : 'font-medium' }}">Engineers</span>
            </a>
        @endif

        <!-- 5. Bill (for Engineer: 'Bill', for Admin: 'Bills') -->
        <a href="{{ route('billing.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('billing.*') ? 'text-primary font-bold' : 'text-textMuted hover:text-textMain' }}">
            <div class="relative p-1 rounded-xl transition {{ request()->routeIs('billing.*') ? 'bg-primary/10 text-primary' : '' }}">
                <i data-lucide="receipt" class="w-5 h-5 {{ request()->routeIs('billing.*') ? 'stroke-[2.5]' : 'stroke-[1.8]' }}"></i>
            </div>
            <span class="text-[10.5px] tracking-tight mt-0.5 {{ request()->routeIs('billing.*') ? 'font-bold text-primary' : 'font-medium' }}">
                {{ auth()->user()->isEngineer() ? 'Bill' : 'Bills' }}
            </span>
        </a>
    </nav>

    <!-- MOBILE NAVIGATION DRAWER (Native App Drawer / Sheet) -->
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-50 flex lg:hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

        <!-- Drawer Content -->
        <div class="relative w-80 max-w-[85vw] bg-primary text-white flex flex-col z-50 shadow-2xl h-full">
            <!-- App Header -->
            <div class="p-4 sm:p-5 border-b border-white/10 bg-black/20 shrink-0 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 flex items-center justify-center text-slate-950 font-black text-sm shadow-md">
                        BT
                    </div>
                    <div>
                        <span class="font-extrabold text-sm text-white block leading-tight">Bhumi Technocare</span>
                        <span class="text-[10px] text-amber-300 font-bold uppercase tracking-wider">Machinery FSM</span>
                    </div>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="p-1.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Drawer Links -->
            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto sidebar-scroll text-sm min-h-0">
                <div class="px-3 pt-1 pb-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                    Daily Operations
                </div>

                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Home</span>
                </a>

                <a href="{{ route('complaints.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('complaints.*') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                        <span>Service Tickets</span>
                    </div>
                    @if($openCount > 0)
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-accent text-primary">{{ $openCount }}</span>
                    @endif
                </a>

                <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('attendance.index') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Daily Photo Attendance</span>
                </a>

                <a href="{{ route('attendance.leaderboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('attendance.leaderboard') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="trophy" class="w-4 h-4 text-accent"></i>
                    <span>Hours Leaderboard</span>
                </a>

                <a href="{{ route('expenses.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('expenses.*') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Field Expense Claims</span>
                </a>

                <a href="{{ route('billing.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('billing.*') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Billing & Cash</span>
                </a>

                <a href="{{ route('notices.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('notices.*') ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:bg-white/10' }}">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span>Notice Board</span>
                </a>

                <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                    Directories
                </div>

                <a href="{{ route('organizations.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                    <span>Firms / Clients</span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Customer Directory</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold text-amber-300 uppercase tracking-wider">
                        Admin Tools
                    </div>
                    <a href="{{ route('engineers.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                        <i data-lucide="hard-hat" class="w-4 h-4"></i>
                        <span>Engineers</span>
                    </a>
                    <a href="{{ route('masters.products') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                        <i data-lucide="cpu" class="w-4 h-4"></i>
                        <span>Machinery</span>
                    </a>
                    <a href="{{ route('masters.services') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                        <i data-lucide="cog" class="w-4 h-4"></i>
                        <span>Services</span>
                    </a>
                    <a href="{{ route('masters.areas') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>Areas & Cities</span>
                    </a>
                    <a href="{{ route('audit.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-white/10">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Security Logs</span>
                    </a>
                @endif
            </nav>

            <!-- Bottom Actions -->
            <div class="p-4 border-t border-white/10 bg-black/30 space-y-2 shrink-0">
                <a href="{{ route('password.change') }}" class="w-full py-2 px-3 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-xs flex items-center space-x-2 transition">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-slate-300"></i>
                    <span>Change Password</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-xs flex items-center justify-center space-x-2 transition shadow-md">
                        <i data-lucide="power" class="w-4 h-4"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(!auth()->user()->isAdmin())
    <!-- QUICK CLOCK-IN / CLOCK-OUT LIVE SELFIE MODAL (Engineers only) -->
    <div x-show="$store.clockModal && $store.clockModal.open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4 bg-primary/75 backdrop-blur-sm">
        
        <!-- Backdrop: click here closes modal -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="$store.clockModal.close()"></div>

        <!-- Modal Dialog -->
        <div x-show="$store.clockModal && $store.clockModal.open"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative w-full max-w-md max-h-[95vh] overflow-y-auto bg-surface rounded-3xl shadow-2xl border border-borderMain p-5 sm:p-6 z-10">
            
            <!-- Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-borderMain">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold" 
                         :class="$store.clockModal.type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                        <i data-lucide="camera" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-textMain" 
                            x-text="$store.clockModal.type === 'in' ? 'Clock In Attendance' : 'Clock Out Attendance'"></h3>
                        <p class="text-xs text-textMuted">Live selfie snapshot required for verification</p>
                    </div>
                </div>
                <button type="button" @click="$store.clockModal.close()" class="text-textMuted hover:text-textMain p-1.5 rounded-xl hover:bg-bg transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form -->
            <form :action="$store.clockModal.type === 'in' ? '{{ route('attendance.clockIn') }}' : '{{ route('attendance.clockOut') }}'" 
                  method="POST" 
                  class="mt-4 space-y-4"
                  x-data="{ submitting: false }"
                  @submit="submitting = true">
                @csrf
                <input type="hidden" name="photo_base64" :value="$store.clockModal.photoData">

                <!-- CAMERA SECTION -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-textMain uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="camera" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Selfie Camera</span> <span class="text-rose-600">*</span>
                        </label>
                        <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1" x-show="$store.clockModal.cameraActive && !$store.clockModal.photoSnapped">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Live Preview
                        </span>
                    </div>

                    <!-- Viewfinder Container (4:3 Aspect Ratio) -->
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-950 border-2 border-slate-700 shadow-2xl flex items-center justify-center">

                        <!-- 1. LIVE CAMERA ACTIVE VIEW -->
                        <div x-show="!$store.clockModal.photoSnapped && $store.clockModal.cameraActive" class="relative w-full h-full flex items-center justify-center">
                            <!-- Live Video Stream -->
                            <video id="selfieVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100 bg-slate-900"></video>

                            <!-- Viewfinder Overlays -->
                            <div class="absolute inset-3 pointer-events-none border border-white/20 rounded-xl flex flex-col justify-between p-2">
                                <div class="flex justify-between items-center text-[10px] font-mono text-emerald-400 bg-black/60 px-2 py-0.5 rounded backdrop-blur-sm self-start">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
                                    FRONT CAMERA
                                </div>
                                <div class="self-center w-14 h-14 border border-white/30 rounded-full flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white/70"></div>
                                </div>
                                <div class="text-[10px] text-white/80 self-center bg-black/50 px-2.5 py-0.5 rounded backdrop-blur-sm">
                                    Align face in center
                                </div>
                            </div>

                            <!-- Circular Camera Shutter Button (Like Native Phone Camera) -->
                            <div class="absolute bottom-3 inset-x-0 flex items-center justify-center z-10">
                                <button type="button" 
                                        @click="$store.clockModal.snapLivePhoto()" 
                                        class="group relative w-16 h-16 rounded-full border-4 border-white shadow-2xl bg-black/30 hover:scale-105 active:scale-90 transition transform cursor-pointer flex items-center justify-center"
                                        title="Click Instant Photo">
                                    <span class="block w-12 h-12 rounded-full bg-white group-hover:bg-emerald-400 transition shadow-md"></span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. CAMERA LOADING SPINNER -->
                        <div x-show="!$store.clockModal.photoSnapped && $store.clockModal.cameraLoading" class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm flex flex-col items-center justify-center text-white space-y-2.5">
                            <div class="w-9 h-9 border-3 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-semibold text-slate-200">Opening camera...</span>
                        </div>

                        <!-- 3. FALLBACK / DIRECT DEVICE CAMERA PROMPT -->
                        <div x-show="!$store.clockModal.photoSnapped && !$store.clockModal.cameraActive && !$store.clockModal.cameraLoading" 
                             class="absolute inset-0 bg-slate-900/95 backdrop-blur-sm p-4 flex flex-col items-center justify-center text-center text-white space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center shadow-lg">
                                <i data-lucide="camera" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Device Camera Mode</h4>
                                <p class="text-xs text-slate-300 mt-1 max-w-xs" x-text="$store.clockModal.cameraError || 'Tap button below to click selfie directly with your device camera.'"></p>
                            </div>
                            <button type="button" 
                                    @click="document.getElementById('deviceCameraInput').click()" 
                                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-black text-xs shadow-xl cursor-pointer flex items-center gap-2 active:scale-95 transition">
                                <i data-lucide="camera" class="w-4 h-4"></i>
                                <span>Click Photo with Camera</span>
                            </button>
                        </div>

                        <!-- 4. SNAPPED PHOTO PREVIEW -->
                        <div x-show="$store.clockModal.photoSnapped" class="relative w-full h-full flex items-center justify-center bg-slate-900">
                            <template x-if="$store.clockModal.photoData">
                                <img :src="$store.clockModal.photoData" alt="Captured Selfie" class="w-full h-full object-cover">
                            </template>
                            <div class="absolute top-3 right-3">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-lg flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Photo Clicked
                                </span>
                            </div>
                            <div class="absolute bottom-3 inset-x-0 flex justify-center">
                                <button type="button" 
                                        @click="$store.clockModal.retakePhoto()" 
                                        class="px-4 py-2 rounded-full bg-slate-900/90 hover:bg-black text-white font-bold text-xs shadow-xl flex items-center gap-2 border border-white/20 backdrop-blur-md transition active:scale-95">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                    <span>Retake Photo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Alternate Direct Device Camera Button (Always accessible) -->
                    <div class="mt-3 flex items-center justify-between gap-2" x-show="!$store.clockModal.photoSnapped">
                        <button type="button" 
                                @click="document.getElementById('deviceCameraInput').click()" 
                                class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 text-xs font-bold transition shadow-sm active:scale-95 flex items-center justify-center gap-2">
                            <i data-lucide="smartphone" class="w-4 h-4 text-secondary"></i>
                            <span>Open Device Camera (Full Screen)</span>
                        </button>
                        <button type="button" 
                                x-show="$store.clockModal.cameraError" 
                                @click="$store.clockModal.startCamera()" 
                                class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 text-xs font-bold transition shadow-sm active:scale-95"
                                title="Retry live stream">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Hidden Native Camera File Input -->
                    <input type="file" 
                           id="deviceCameraInput" 
                           accept="image/*" 
                           capture="user" 
                           @change="$store.clockModal.handleFileSelect($event)" 
                           class="hidden">
                </div>

                <!-- Server Time Indicator -->
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs flex items-center space-x-2">
                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-secondary"></i>
                    <span>Server Time: <strong>{{ now()->format('d M Y, h:i A') }}</strong></span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" 
                            @click="$store.clockModal.close()" 
                            class="px-4 py-2.5 rounded-xl border border-borderMain text-textMain text-xs font-bold hover:bg-bg transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="!$store.clockModal.photoSnapped || submitting" 
                            class="px-5 py-2.5 rounded-xl text-white text-xs font-bold shadow-md transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2" 
                            :class="$store.clockModal.type === 'in' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                        <i data-lucide="check" class="w-4 h-4" x-show="!submitting"></i>
                        <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" x-show="submitting"></span>
                        <span x-text="submitting ? 'Recording...' : ($store.clockModal.type === 'in' ? 'Confirm Clock In' : 'Confirm Clock Out')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Initialize Lucide icons on every page & mobile UX listeners -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // Mobile UX: auto hide/dismiss virtual keyboard when tapping outside input/textarea
        document.addEventListener('touchstart', function(e) {
            if (document.activeElement && 
                (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') &&
                !document.activeElement.contains(e.target) &&
                e.target !== document.activeElement) {
                document.activeElement.blur();
            }
        }, { passive: true });

        // WhatsApp vs WhatsApp Business Direct Intent Redirection
        window.openWhatsAppDirect = function(event, rawPhone, type) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const clean = String(rawPhone || '').replace(/[^0-9]/g, '');
            if (!clean) return;
            const num = clean.length === 10 ? '91' + clean : clean;
            const isAndroid = /Android/i.test(navigator.userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

            if (isAndroid) {
                if (type === 'business') {
                    // Force Android to launch WhatsApp Business app exclusively
                    window.location.href = `intent://send?phone=+${num}#Intent;scheme=smsto;package=com.whatsapp.w4b;action=android.intent.action.SENDTO;end`;
                } else {
                    // Force Android to launch Regular WhatsApp app exclusively
                    window.location.href = `intent://send?phone=+${num}#Intent;scheme=smsto;package=com.whatsapp;action=android.intent.action.SENDTO;end`;
                }
            } else if (isIOS) {
                if (type === 'business') {
                    window.location.href = `whatsapp-smb://send?phone=${num}`;
                    setTimeout(() => {
                        window.location.href = `https://wa.me/${num}`;
                    }, 800);
                } else {
                    window.location.href = `whatsapp://send?phone=${num}`;
                }
            } else {
                // Desktop Web
                if (type === 'business') {
                    window.open(`https://wa.me/${num}`, '_blank');
                } else {
                    window.open(`https://web.whatsapp.com/send?phone=${num}`, '_blank');
                }
            }
        };
    </script>
    @stack('scripts')
</body>
</html>
