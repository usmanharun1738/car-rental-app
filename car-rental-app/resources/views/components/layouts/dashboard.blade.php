<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - CARTAR</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        
        .sidebar-active-link {
            background-color: #EBF5EB;
            color: #111418;
            border-right: 3px solid #E3655B;
        }
        .sidebar-link:hover:not(.sidebar-active-link) {
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen flex antialiased">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full overflow-y-auto hidden md:flex">
        <!-- Logo Area -->
        <div class="p-6 flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                <div class="size-10 rounded-xl bg-[#E3655B] flex items-center justify-center text-white font-black text-xl shadow-md">
                    C
                </div>
                <h1 class="text-xl font-extrabold tracking-tight text-[#111418]">CARTAR</h1>
            </a>
        </div>
        
        <!-- User Snippet -->
        <div class="px-6 pb-6">
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50">
                <div class="bg-[#E3655B] rounded-full size-10 flex-shrink-0 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex flex-col overflow-hidden">
                    <p class="text-[#111418] text-sm font-bold truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-slate-500 text-xs truncate">Basic Member</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 flex flex-col gap-1">
            <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 mt-2">Menu</p>
            
            <a href="{{ route('dashboard') }}" 
               class="{{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'sidebar-active-link' : 'sidebar-link text-slate-600' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
               wire:navigate>
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'text-[#E3655B]' : '' }}" style="font-size: 20px;">dashboard</span>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('dashboard.profile') }}" 
               class="{{ request()->routeIs('dashboard.profile') ? 'sidebar-active-link' : 'sidebar-link text-slate-600' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
               wire:navigate>
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard.profile') ? 'text-[#E3655B]' : '' }}" style="font-size: 20px;">person</span>
                <span class="text-sm font-medium">User Profile</span>
            </a>
            
            <a href="{{ route('dashboard.bookings') }}" 
               class="{{ request()->routeIs('dashboard.bookings') ? 'sidebar-active-link' : 'sidebar-link text-slate-600' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
               wire:navigate>
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard.bookings') ? 'text-[#E3655B]' : '' }}" style="font-size: 20px;">calendar_month</span>
                <span class="text-sm font-medium">My Bookings</span>
            </a>
            
            <a href="{{ route('dashboard.security') }}" 
               class="{{ request()->routeIs('dashboard.security') ? 'sidebar-active-link' : 'sidebar-link text-slate-600' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
               wire:navigate>
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard.security') ? 'text-[#E3655B]' : '' }}" style="font-size: 20px;">security</span>
                <span class="text-sm font-medium">Security</span>
            </a>
            
            <a href="{{ route('dashboard.license') }}" 
               class="{{ request()->routeIs('dashboard.license') ? 'sidebar-active-link' : 'sidebar-link text-slate-600' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
               wire:navigate>
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard.license') ? 'text-[#E3655B]' : '' }}" style="font-size: 20px;">badge</span>
                <span class="text-sm font-medium">Driver's License</span>
            </a>
        </nav>
        
        <!-- Bottom Actions -->
        <div class="p-4 border-t border-slate-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full cursor-pointer items-center justify-start gap-3 rounded-lg h-10 px-4 text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                    <span class="text-sm font-medium">Log Out</span>
                </button>
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Mobile Header -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 md:hidden">
            <div class="flex items-center gap-2">
                <div class="size-8 rounded bg-[#E3655B] flex items-center justify-center text-white font-bold">C</div>
                <span class="font-bold text-lg">CARTAR</span>
            </div>
            <button class="text-slate-600">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </header>
        
        <!-- Desktop Header / Top Bar -->
        <header class="hidden md:flex h-20 items-center justify-between px-8 bg-white border-b border-slate-200 flex-shrink-0">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="text-slate-500 font-medium hover:text-slate-700" wire:navigate>Home</a>
                <span class="text-slate-300">/</span>
                @if(isset($breadcrumb))
                    <a href="{{ route('dashboard') }}" class="text-slate-500 font-medium hover:text-slate-700" wire:navigate>Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-[#111418] font-medium">{{ $breadcrumb }}</span>
                @else
                    <span class="text-[#111418] font-medium">Dashboard</span>
                @endif
            </div>
            
            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="absolute top-0 right-0 size-2 bg-red-500 rounded-full border border-white"></span>
                    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                </div>
                <a href="{{ route('vehicles.index') }}" 
                   class="bg-[#E3655B] hover:bg-[#d6554b] text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2"
                   wire:navigate>
                    <span class="material-symbols-outlined text-[18px]">directions_car</span>
                    Book a Car
                </a>
            </div>
        </header>
        
        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 bg-slate-50/50">
            {{ $slot }}
        </div>
    </main>
    
    @livewireScripts
</body>
</html>
