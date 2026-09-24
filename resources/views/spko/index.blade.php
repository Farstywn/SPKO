@extends('layouts.app')

@section('title', 'Daftar Surat Perintah Kerja Operator (SPKO)')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Daftar Transaksi SPKO</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $allocations->total() }} Data
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola transaksi Surat Perintah Kerja Operator (workallocation) dan Nota Terima Kerja (workcompletion).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('spko.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm shadow-sm transition">
                + Tambah Transaksi SPKO
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase font-semibold text-slate-700">
                    <tr>
                        <th class="px-6 py-4">No. SPKO</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Operator</th>
                        <th class="px-6 py-4">Proses</th>
                        <th class="px-6 py-4">Item Produk</th>
                        <th class="px-6 py-4">Total Qty</th>
                        <th class="px-6 py-4">Total Berat (Gr)</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
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
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-xs font-bold">
                                        {{ $row->SW }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">ID: {{ $row->ID }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                {{ \Carbon\Carbon::parse($row->TransDate)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $row->employee->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-400">ID: {{ $row->Employee }} &bull; {{ $row->employee->rank ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ $row->Process }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $row->items->count() }} Item
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ number_format($totalQty) }} Pcs
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-900">
                                {{ number_format($totalWeight, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Print Button -->
                                    <a href="{{ route('spko.print', $row->ID) }}" target="_blank" title="Cetak SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition">
                                        <i class="fa-solid fa-print text-sm"></i>
                                    </a>
                                    <!-- Detail Button -->
                                    <a href="{{ route('spko.show', $row->ID) }}" title="Lihat Detail SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-indigo-700 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 transition">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('spko.edit', $row->ID) }}" title="Edit Transaksi SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-amber-700 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 transition">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('spko.destroy', $row->ID) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi SPKO {{ $row->SW }}? Seluruh nota terima kerja terkait akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Transaksi SPKO" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-rose-700 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 transition">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                Belum ada data transaksi Surat Perintah Kerja Operator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allocations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $allocations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
