<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ $title ?? 'CARTAR - Car Rental' }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Material Symbols -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom CARTAR Colors -->
        <style>
            :root {
                --color-primary: #1E3A5F;
                --color-primary-dark: #152a45;
                --color-secondary: #FF6B35;
                --color-secondary-dark: #e55a2b;
            }
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="min-h-screen bg-gray-50 antialiased overflow-x-hidden">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 w-full border-b border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between whitespace-nowrap px-4 sm:px-6 lg:px-10 py-3 max-w-[1440px] mx-auto">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 text-[#1E3A5F]" wire:navigate>
                    <div class="size-8 text-[#FF6B35]">
                        <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_319)">
                                <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_319"><rect fill="white" height="48" width="48"></rect></clipPath>
                            </defs>
                        </svg>
                    </div>
                    <h2 class="text-[#1E3A5F] text-xl font-bold leading-tight tracking-[-0.015em]">CARTAR</h2>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex flex-1 justify-end gap-8">
                    <div class="flex items-center gap-9">
                        <a class="text-gray-700 text-sm font-medium leading-normal hover:text-[#1E3A5F] transition-colors" href="{{ route('vehicles.index') }}" wire:navigate>Fleet</a>
                        <a class="text-gray-700 text-sm font-medium leading-normal hover:text-[#1E3A5F] transition-colors" href="#">Locations</a>
                        <a class="text-gray-700 text-sm font-medium leading-normal hover:text-[#1E3A5F] transition-colors" href="#">Deals</a>
                    </div>
                    <div class="flex gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#1E3A5F] text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#152a45] transition-colors"
                               wire:navigate>
                                <span class="truncate">Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#FF6B35] text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#e55a2b] transition-colors"
                               wire:navigate>
                                <span class="truncate">Register</span>
                            </a>
                            <a href="{{ route('login') }}" 
                               class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-100 text-gray-900 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-gray-200 transition-colors"
                               wire:navigate>
                                <span class="truncate">Sign In</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button type="button" class="text-gray-700 hover:text-[#1E3A5F] transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
                <div class="px-4 py-4 space-y-3">
                    <a class="block text-gray-700 text-sm font-medium py-2 hover:text-[#1E3A5F] transition-colors" href="/#vehicles">Fleet</a>
                    <a class="block text-gray-700 text-sm font-medium py-2 hover:text-[#1E3A5F] transition-colors" href="#">Locations</a>
                    <a class="block text-gray-700 text-sm font-medium py-2 hover:text-[#1E3A5F] transition-colors" href="#">Deals</a>
                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="block w-full text-center py-2 px-4 bg-[#1E3A5F] text-white text-sm font-bold rounded-lg hover:bg-[#152a45] transition-colors"
                               wire:navigate>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="block w-full text-center py-2 px-4 bg-[#FF6B35] text-white text-sm font-bold rounded-lg hover:bg-[#e55a2b] transition-colors"
                               wire:navigate>
                                Register
                            </a>
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center py-2 px-4 bg-gray-100 text-gray-900 text-sm font-bold rounded-lg hover:bg-gray-200 transition-colors"
                               wire:navigate>
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-[#1E3A5F] border-t border-gray-800 pt-16 pb-8 px-4 md:px-10">
            <div class="max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-1 flex flex-col gap-4">
                        <div class="flex items-center gap-2 text-white">
                            <div class="size-6 text-[#FF6B35]">
                                <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_footer)">
                                        <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_footer"><rect fill="white" height="48" width="48"></rect></clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold">CARTAR</h2>
                        </div>
                        <p class="text-gray-400 text-sm">
                            Experience the freedom of the road with our premium car rental service in Nigeria.
                        </p>
                        <div class="flex gap-4 mt-2">
                            <a class="text-gray-400 hover:text-[#FF6B35] transition-colors" href="#">
                                <span class="material-symbols-outlined">public</span>
                            </a>
                            <a class="text-gray-400 hover:text-[#FF6B35] transition-colors" href="mailto:info@cartar.ng">
                                <span class="material-symbols-outlined">mail</span>
                            </a>
                            <a class="text-gray-400 hover:text-[#FF6B35] transition-colors" href="tel:+2348000000000">
                                <span class="material-symbols-outlined">call</span>
                            </a>
                        </div>
                    </div>

                    <!-- Our Company -->
                    <div>
                        <h3 class="text-white font-bold mb-4">Our Company</h3>
                        <ul class="flex flex-col gap-2">
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">About Us</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Careers</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Blog</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Press</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-white font-bold mb-4">Support</h3>
                        <ul class="flex flex-col gap-2">
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Help Center</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Terms of Service</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Privacy Policy</a></li>
                            <li><a class="text-gray-400 hover:text-[#FF6B35] text-sm transition-colors" href="#">Contact Us</a></li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div>
                        <h3 class="text-white font-bold mb-4">Newsletter</h3>
                        <p class="text-gray-400 text-sm mb-4">Subscribe to get the latest deals and updates.</p>
                        <div class="flex flex-col gap-2">
                            <input class="w-full rounded-lg border border-gray-600 bg-[#152a45] px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="Your email address" type="email">
                            <button class="w-full rounded-lg bg-[#FF6B35] text-white font-bold text-sm py-2 hover:bg-[#e55a2b] transition-colors">Subscribe</button>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm text-center md:text-left">© {{ date('Y') }} CARTAR. All rights reserved.</p>
                    <div class="flex gap-6">
                        <span class="text-gray-400 text-sm cursor-pointer hover:text-[#FF6B35]">English (US)</span>
                        <span class="text-gray-400 text-sm cursor-pointer hover:text-[#FF6B35]">₦ NGN</span>
                    </div>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
