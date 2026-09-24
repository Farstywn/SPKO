@extends('layouts.app')

@section('title', 'Detail SPKO ' . $allocation->SW)

@section('content')
<div class="w-full space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('spko.index') }}" class="hover:text-emerald-700">SPKO</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Detail Transaksi</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                <span>{{ $allocation->SW }}</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                    Proses: {{ $allocation->Process }}
                </span>
            </h1>
            <p class="text-sm text-slate-500">ID Internal Transaksi: {{ $allocation->ID }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('spko.print', $allocation->ID) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm transition shadow-sm">
                Cetak SPKO
            </a>
            <a href="{{ route('spko.edit', $allocation->ID) }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm transition shadow-sm">
                Edit
            </a>
            <a href="{{ route('spko.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-sm transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- SPKO Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-100">
                Informasi SPKO (Work Allocation)
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Operator Penanggung Jawab</span>
                    <span class="font-semibold text-slate-900">{{ $allocation->employee->nama ?? '-' }}</span>
                    <span class="text-xs text-slate-500 block">ID: {{ $allocation->Employee }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tanggal Transaksi</span>
                    <span class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($allocation->TransDate)->translatedFormat('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Proses Pengerjaan</span>
                    <span class="font-semibold text-slate-900">{{ $allocation->Process }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Catatan / Remarks</span>
                    <span class="text-slate-700">{{ $allocation->Remarks ?: '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Work Completion Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-100">
                Nota Terima Kerja (Work Completion)
            </h3>
            @if($allocation->workCompletion)
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Nomor Referensi SPKO</span>
                        <span class="font-semibold text-slate-900 font-mono">{{ $allocation->workCompletion->WorkAllocation }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Tanggal Terima</span>
                        <span class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($allocation->workCompletion->TransDate)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Status Terima</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Tersinkronisasi
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Total Item Diterima</span>
                        <span class="font-semibold text-slate-900">{{ $allocation->workCompletion->items->count() }} Item ({{ $allocation->workCompletion->items->sum('Qty') }} Pcs)</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500 italic py-4">Belum ada nota terima kerja terkait.</p>
            @endif
        </div>
    </div>

    <!-- Product Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-semibold text-slate-900">
                Rincian Item Produk (SPKO)
            </h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                {{ $allocation->items->count() }} Item
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 w-16 text-center">No.</th>
                        <th class="px-6 py-3">Deskripsi Produk</th>
                        <th class="px-6 py-3">Carat</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3 text-right">Qty (Pcs)</th>
                        <th class="px-6 py-3 text-right">Berat SPKO (Gr)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allocation->items as $idx => $it)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 text-center font-mono text-xs font-semibold text-slate-400">
                                {{ $it->Ordinal }}
                            </td>
                            <td class="px-6 py-3.5 font-medium text-slate-900">
                                {{ $it->product->description ?? 'FG: ' . $it->FG }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-xs">
                                    {{ $it->product->carat ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs text-slate-600">
                                {{ $it->product->sku ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-semibold text-slate-900">
                                {{ number_format($it->Qty) }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono text-slate-900">
                                {{ number_format($it->Weight, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400">Tidak ada item produk.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50/70 border-t border-slate-200 font-semibold text-slate-900 text-sm">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right">Total Keseluruhan:</td>
                        <td class="px-6 py-3 text-right text-emerald-700">{{ number_format($allocation->items->sum('Qty')) }} Pcs</td>
                        <td class="px-6 py-3 text-right font-mono text-emerald-700">{{ number_format($allocation->items->sum('Weight'), 2) }} Gr</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
