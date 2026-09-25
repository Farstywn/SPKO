@extends('layouts.app')

@section('title', 'Nota Terima Kerja (NTHKO)')
@section('breadcrumb', 'TRANSAKSI / NOTA TERIMA KERJA')
@section('page_title', 'Daftar Transaksi NTHKO')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Toolbar Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Nota Terima Hasil Kerja Operator (NTHKO)</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        {{ $completions->total() }} Data
                    </span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Dokumen penerimaan hasil kerja operator (workcompletion) yang terbit otomatis dari SPKO.
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('nthko.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari SPKO, operator, proses..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50/50">
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('nthko.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition" title="Reset Pencarian">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">ID NTHKO</th>
                        <th class="px-6 py-3.5">Rujukan SPKO</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Operator</th>
                        <th class="px-6 py-3.5">Proses</th>
                        <th class="px-6 py-3.5">Item Produk</th>
                        <th class="px-6 py-3.5">Total Qty</th>
                        <th class="px-6 py-3.5">Total Berat (Gr)</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($completions as $row)
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
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-800 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    #{{ $row->ID }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($row->workAllocation)
                                    <a href="{{ route('spko.show', $row->workAllocation->ID) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-xs font-bold hover:bg-emerald-100 transition">
                                        {{ $row->WorkAllocation }}
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    </a>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold">
                                        {{ $row->WorkAllocation }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($row->TransDate)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="font-semibold text-slate-900 text-xs">{{ $row->employee->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">ID: {{ $row->Employee }} &bull; {{ $row->employee->rank ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ $row->Process }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-700">
                                {{ $row->items->count() }} Item
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalQty) }} Pcs
                            </td>
                            <td class="px-6 py-3.5 font-mono font-medium text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalWeight, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                    Diterima
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-sm text-slate-400">
                                Tidak ada data Nota Terima Kerja yang sesuai dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($completions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $completions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
