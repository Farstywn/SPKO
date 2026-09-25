@extends('layouts.app')

@section('title', 'Daftar Surat Perintah Kerja Operator (SPKO)')
@section('breadcrumb', 'TRANSAKSI / SURAT PERINTAH KERJA OPERATOR')
@section('page_title', 'Daftar Transaksi SPKO')

@section('content')
<div class="space-y-5">

    <!-- Page Subheader -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Daftar Transaksi SPKO</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700">
                    {{ $allocations->total() }} Data
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola transaksi Surat Perintah Kerja Operator (workallocation) dan Nota Terima Kerja (workcompletion).
            </p>
        </div>
    </div>

    <!-- Main Table Card with Integrated Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Toolbar Bar: Filter on Left, Add Button on Far Right -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Left: Search & Filter Form -->
            <form action="{{ route('spko.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 flex-1 max-w-2xl">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari no. SPKO, operator..." 
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50/50 text-slate-800 placeholder:text-slate-400">
                </div>

                <!-- Process Filter Select -->
                <div class="w-40 shrink-0">
                    <select name="process" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">Semua Proses</option>
                        @foreach(['Cor', 'Brush', 'Bombing', 'Slep'] as $proc)
                            <option value="{{ $proc }}" {{ ($process ?? '') == $proc ? 'selected' : '' }}>
                                {{ $proc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cari Button -->
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition shrink-0">
                    Cari
                </button>

                @if(!empty($search) || !empty($process))
                    <a href="{{ route('spko.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition shrink-0" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Right: + Tambah Order SPKO Button -->
            <div class="shrink-0">
                <a href="{{ route('spko.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition w-full sm:w-auto">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>+ Tambah Order</span>
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. SPKO</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Operator</th>
                        <th class="px-6 py-3.5">Proses</th>
                        <th class="px-6 py-3.5">Item Produk</th>
                        <th class="px-6 py-3.5">Total Qty</th>
                        <th class="px-6 py-3.5">Total Berat (Gr)</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allocations as $row)
                        @php
                            $totalQty = $row->items->sum('Qty');
                            $totalWeight = $row->items->sum('Weight');
                            $processColors = [
                                'Cor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Brush' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'Bombing' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'Slep' => 'bg-purple-50 text-purple-700 border-purple-200',
                            ];
                            $badgeColor = $processColors[$row->Process] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition group">
                            <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-xs font-bold">
                                        {{ $row->SW }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">ID: {{ $row->ID }}</span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-700">
                                {{ \Carbon\Carbon::parse($row->TransDate)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="font-medium text-slate-900 text-xs">{{ $row->employee->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">ID: {{ $row->Employee }} &bull; {{ $row->employee->rank ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ $row->Process }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs font-medium text-slate-700">
                                {{ $row->items->count() }} Item
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalQty) }} Pcs
                            </td>
                            <td class="px-6 py-3.5 font-mono font-medium text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalWeight, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Print Button -->
                                    <a href="{{ route('spko.print', $row->ID) }}" target="_blank" title="Cetak SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                    <!-- Detail Button -->
                                    <a href="{{ route('spko.show', $row->ID) }}" title="Lihat Detail SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-indigo-700 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('spko.edit', $row->ID) }}" title="Edit Transaksi SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-amber-700 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('spko.destroy', $row->ID) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi SPKO {{ $row->SW }}? Seluruh nota terima kerja terkait akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Transaksi SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-rose-700 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 transition">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400 text-sm">
                                Belum ada data transaksi Surat Perintah Kerja Operator yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allocations->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $allocations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
