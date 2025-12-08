<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ $title ?? 'CARTAR - Car Rental' }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom CARTAR Colors -->
        <style>
            :root {
                --color-primary: #1E3A5F;
                --color-primary-dark: #152a45;
                --color-secondary: #FF6B35;
                --color-secondary-dark: #e55a2b;
            }
        </style>
    </head>
    <body class="min-h-screen bg-gray-50 font-sans antialiased" style="font-family: 'Inter', sans-serif;">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <a href="/" class="flex items-center" wire:navigate>
                        <img src="/images/cartar-logo.png" alt="CARTAR" class="h-10 w-auto">
                    </a>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="/" class="text-gray-700 hover:text-[#1E3A5F] font-medium transition">Home</a>
                        <a href="#vehicles" class="text-gray-700 hover:text-[#1E3A5F] font-medium transition">Vehicles</a>
                        <a href="#how-it-works" class="text-gray-700 hover:text-[#1E3A5F] font-medium transition">How It Works</a>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#152a45] transition"
                               wire:navigate>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="text-gray-700 hover:text-[#1E3A5F] font-medium transition"
                               wire:navigate>
                                Log in
                            </a>
                            <a href="{{ route('register') }}" 
                               class="px-4 py-2 text-sm font-medium text-white bg-[#FF6B35] rounded-lg hover:bg-[#e55a2b] transition"
                               wire:navigate>
                                Register
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
        <footer class="bg-[#1E3A5F] text-white py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Logo & Description -->
                    <div>
                        <img src="/images/cartar-logo.png" alt="CARTAR" class="h-12 w-auto mb-4 brightness-0 invert">
                        <p class="text-gray-300 text-sm">
                            Premium car rentals in Nigeria. Book your dream car in minutes.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li><a href="/" class="hover:text-white transition">Home</a></li>
                            <li><a href="#vehicles" class="hover:text-white transition">Browse Vehicles</a></li>
                            <li><a href="#how-it-works" class="hover:text-white transition">How It Works</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="font-semibold mb-4">Contact Us</h3>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li>Lagos, Nigeria</li>
                            <li>info@cartar.ng</li>
                            <li>+234 800 000 0000</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-600 mt-8 pt-8 text-center text-sm text-gray-400">
                    &copy; {{ date('Y') }} CARTAR. All rights reserved.
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
