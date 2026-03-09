<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | {{ config('app.name', 'Status Scheduler') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f5f3ff',
                                100: '#ede9fe',
                                200: '#ddd6fe',
                                300: '#c4b5fd',
                                400: '#a78bfa',
                                500: '#8b5cf6',
                                600: '#7c3aed',
                                700: '#6d28d9',
                                800: '#5b21b6',
                                900: '#4c1d95',
                                950: '#2e1065',
                            },
                        },
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                        },
                        animation: {
                            'pulse-slow': 'pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        }
                    }
                }
            }
        </script>

        <style>
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            .dark .glass {
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .bg-mesh {
                background-color: #ffffff;
                background-image: 
                    radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
                background-attachment: fixed;
            }
            .hero-gradient {
                background: linear-gradient(135deg, #6d28d9 0%, #4c1d95 100%);
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center overflow-hidden">
        
        <!-- Animated Background Elements -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary-500/20 blur-[120px] rounded-full animate-pulse-slow"></div>
            <div class="absolute top-[20%] -right-[5%] w-[35%] h-[35%] bg-indigo-500/20 blur-[120px] rounded-full animate-pulse-slow" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[45%] h-[45%] bg-blue-500/10 blur-[120px] rounded-full animate-pulse-slow" style="animation-delay: 4s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto">
                <div class="glass p-8 md:p-12 rounded-[2rem] shadow-2xl flex flex-col md:flex-row items-center gap-12">
                    
                    <!-- Left Side: Branding/Visual -->
                    <div class="flex-1 text-center md:text-left">
                        <div class="inline-flex items-center justify-center p-3 bg-primary-600 rounded-2xl shadow-lg mb-6 group transition-transform hover:scale-110">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                            Status <span class="text-primary-600 dark:text-primary-400">Scheduler</span>
                        </h1>
                        <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                            Efficiently manage and schedule your social media posts across WhatsApp, Instagram, and more. One platform, total control.
                        </p>
                        
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            @auth
                                <a href="{{ url('/admin') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-600/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                                    <span>Go to Dashboard</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            @else
                                <a href="{{ url('/admin/login') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-600/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                                    <span>Sign In to Portal</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-all transform hover:-translate-y-1">
                                        Create Account
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Right Side: Decorative Image/Feature -->
                    <div class="flex-1 w-full max-w-sm hidden md:block">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-primary-600 to-indigo-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-xl aspect-square">
                                <!-- Premium Generated Logo -->
                                <img src="/status_scheduler_logo_1772632592421.png" alt="Status Scheduler" class="w-full h-full object-cover">

                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-8">
                                    <div>
                                        <p class="text-white font-medium text-lg">Next-gen Scheduling</p>
                                        <p class="text-slate-300 text-sm">Automate your social presence with ease.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Simple -->
                <div class="mt-12 text-center text-sm text-slate-500 dark:text-slate-500">
                    &copy; {{ date('Y') }} Status Scheduler. Built for professionals.
                </div>
            </div>
        </div>

    </body>
</html>
