@extends('layouts.app')

@section('title', 'Dashboard ERP SPKO')
@section('breadcrumb', 'DASHBOARD / RINGKASAN SISTEM')
@section('page_title', 'Ringkasan Operasional')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total SPKO Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Transaksi SPKO</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalSpko) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>

        <!-- Total NTHKO Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nota Terima Kerja (NTHKO)</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalNthko) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <!-- Master Operator Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Operator Aktif</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalEmployee) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-id-badge"></i>
            </div>
        </div>

        <!-- Master Produk Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Katalog Produk (FG)</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalProduct) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-box-archive"></i>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Transaksi SPKO Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">5 transaksi Surat Perintah Kerja Operator yang terakhir diterbitkan</p>
            </div>
            <a href="{{ route('spko.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition">
                <span>Lihat Semua SPKO</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. SPKO</th>
                        <th class="px-6 py-3.5">Operator</th>
                        <th class="px-6 py-3.5">Proses</th>
                        <th class="px-6 py-3.5">Item Produk</th>
                        <th class="px-6 py-3.5">Total Qty</th>
                        <th class="px-6 py-3.5">Total Berat (Gr)</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSpko as $row)
                        @php
                            $totalQty = $row->items->sum('Qty');
                            $totalWeight = $row->items->sum('Weight');
                            $firstItem = $row->items->first();
                            $itemSummary = $firstItem ? ($firstItem->product->description ?? $firstItem->FG) : '-';
                            if ($row->items->count() > 1) {
                                $itemSummary .= ' (+' . ($row->items->count() - 1) . ' item)';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-xs font-bold">
                                    {{ $row->SW }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="font-medium text-slate-900 text-xs">{{ $row->employee->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">ID: {{ $row->Employee }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                    {{ $row->Process }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-700 max-w-xs truncate" title="{{ $itemSummary }}">
                                {{ $itemSummary }}
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalQty) }} Pcs
                            </td>
                            <td class="px-6 py-3.5 font-mono font-medium text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($totalWeight, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->TransDate)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('spko.show', $row->ID) }}" title="Lihat Detail" class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('spko.print', $row->ID) }}" target="_blank" title="Cetak SPKO" class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-400">
                                Belum ada data transaksi SPKO.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
