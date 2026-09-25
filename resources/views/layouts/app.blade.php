<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem SPKO Operator') - ERP Manufaktur</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                            800: '#14532d',
                            900: '#052e16',
                        },
                        darkpill: '#1e293b'
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Select2 Tailwind modern overrides */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border-color: #cbd5e1;
            border-radius: 0.75rem;
            background-color: #ffffff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            padding-left: 0;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 10px;
        }
        .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            font-size: 0.875rem;
            z-index: 9999;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 0.875rem;
            outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #10b981;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #059669;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: #ecfdf5;
            color: #065f46;
            font-weight: 600;
        }

        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100/70 text-slate-800 antialiased min-h-screen flex">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity"></div>

    <!-- Sidebar Container -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <!-- Brand Header -->
        <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center group">
                <div>
                    <span class="text-base font-bold text-slate-900 tracking-tight block leading-tight">SPKO</span>
                    <span class="text-[10px] font-semibold tracking-wider text-slate-400 uppercase">Surat Perintah Kerja Operator</span>
                </div>
            </a>
            <button id="sidebar-close-btn" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="flex-1 overflow-y-auto py-5 px-3 space-y-1.5 sidebar-scroll">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <i class="fa-solid fa-house w-5 text-center text-sm {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-semibold' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <!-- Master Data Accordion -->
            @php
                $isMasterActive = request()->routeIs('master.*');
            @endphp
            <div class="space-y-1 pt-1">
                <button type="button" 
                        class="menu-accordion-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ $isMasterActive ? 'text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                        data-target="menu-master">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-box-archive w-5 text-center text-sm {{ $isMasterActive ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        <span>Master Data</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 menu-icon {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-master" class="menu-subitems pl-8 pr-2 space-y-1 {{ $isMasterActive ? '' : 'hidden' }}">
                    <a href="{{ route('master.employee') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('master.employee') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Operator (Employee)
                    </a>
                    <a href="{{ route('master.product') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('master.product') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Produk (FG)
                    </a>
                </div>
            </div>

            <!-- Transaksi Accordion -->
            @php
                $isTransaksiActive = request()->routeIs('spko.*') || request()->routeIs('nthko.*');
            @endphp
            <div class="space-y-1 pt-1">
                <button type="button" 
                        class="menu-accordion-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ $isTransaksiActive ? 'text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                        data-target="menu-transaksi">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-credit-card w-5 text-center text-sm {{ $isTransaksiActive ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        <span>Transaksi</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 menu-icon {{ $isTransaksiActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-transaksi" class="menu-subitems pl-8 pr-2 space-y-1 {{ $isTransaksiActive ? '' : 'hidden' }}">
                    <a href="{{ route('spko.index') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('spko.index', 'spko.show', 'spko.edit') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Daftar SPKO
                    </a>
                    <a href="{{ route('spko.create') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('spko.create') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Buat SPKO Baru
                    </a>
                    <a href="{{ route('nthko.index') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('nthko.index') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Nota Terima Kerja (NTHKO)
                    </a>
                </div>
            </div>

            <!-- Laporan & Analitik Accordion -->
            @php
                $isReportActive = request()->routeIs('reports.*');
            @endphp
            <div class="space-y-1 pt-1">
                <button type="button" 
                        class="menu-accordion-btn w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ $isReportActive ? 'text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                        data-target="menu-laporan">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ $isReportActive ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        <span>Laporan & Analitik</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 menu-icon {{ $isReportActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-laporan" class="menu-subitems pl-8 pr-2 space-y-1 {{ $isReportActive ? '' : 'hidden' }}">
                    <a href="{{ route('reports.daily') }}" 
                       class="block px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('reports.daily') ? 'bg-indigo-50 text-indigo-800 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                        Laporan Harian (Raw SQL)
                    </a>
                    <a href="{{ env('FLASK_URL', 'http://127.0.0.1:5000') }}" target="_blank" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-amber-700 hover:bg-amber-50 transition">
                        <span>Modul Flask (Python)</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>
    </aside>

    <!-- Main Wrapper Area -->
    <div class="flex-1 md:pl-64 flex flex-col min-w-0 min-h-screen">

        <!-- Top Header Navigation Bar -->
        <header class="h-16 bg-white border-b border-slate-200/90 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-4">
                <!-- Hamburger Button (Mobile) -->
                <button id="sidebar-toggle-btn" class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumb / Header Title -->
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        @yield('breadcrumb', 'SISTEM ERP / MANUFAKTUR')
                    </div>
                    <div class="text-base font-bold text-slate-900 leading-tight">
                        @yield('page_title', 'Surat Perintah Kerja Operator')
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-8">
            <!-- Flash Notification Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-emerald-900">Operasi Berhasil</h4>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-rose-900">Perhatian</h4>
                        <p class="text-sm text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 shadow-xs">
                    <div class="mb-2 text-rose-900 font-semibold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Terjadi Kesalahan Validasi:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-rose-700 space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200/80 py-4 px-8 text-xs text-slate-500 mt-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>&copy; {{ date('Y') }} SPKO - Modul Surat Perintah Kerja Operator</span>
                <span class="text-slate-400">Database ERP Manufaktur</span>
            </div>
        </footer>

    </div>

    <!-- Sidebar & Accordion JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');

            function toggleSidebar() {
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                }
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', toggleSidebar);

            // Accordion menus
            document.querySelectorAll('.menu-accordion-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const target = document.getElementById(targetId);
                    const icon = this.querySelector('.menu-icon');
                    
                    if (target) {
                        const isHidden = target.classList.contains('hidden');
                        if (isHidden) {
                            target.classList.remove('hidden');
                            if (icon) icon.classList.add('rotate-180');
                        } else {
                            target.classList.add('hidden');
                            if (icon) icon.classList.remove('rotate-180');
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
