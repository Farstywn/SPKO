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
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
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
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">
    <!-- Header Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <div>
                        <a href="{{ route('spko.index') }}" class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                            ERP SPKO <span class="text-xs uppercase bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded-full">Operator</span>
                        </a>
                        <p class="text-xs text-slate-500 hidden sm:block">Sistem Surat Perintah & Nota Terima Kerja</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('spko.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('spko.index', 'spko.show') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Data SPKO
                    </a>
                    <a href="{{ route('spko.create') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('spko.create') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Buat SPKO Baru
                    </a>
                    <a href="{{ route('reports.daily') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.daily') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/50' }}">
                        Laporan Harian (Raw SQL)
                    </a>
                    <a href="{{ env('FLASK_URL', 'http://127.0.0.1:5000') }}" target="_blank" class="px-3.5 py-2 rounded-lg text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">
                        Modul Flask (Python)
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="grow py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 shadow-sm">
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-emerald-900">Operasi Berhasil</h4>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 shadow-sm">
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-rose-900">Perhatian / Gagal</h4>
                        <p class="text-sm text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 shadow-sm">
                    <div class="mb-2 text-rose-900 font-semibold text-sm">
                        Terjadi Kesalahan Validasi:
                    </div>
                    <ul class="list-disc list-inside text-sm text-rose-700 space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>&copy; {{ date('Y') }} Modul SPKO Operator - Sistem ERP Manufaktur Perhiasan</span>
            <span class="text-slate-400">Database ERP System</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
