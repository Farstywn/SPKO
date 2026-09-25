@extends('layouts.app')

@section('title', 'Laporan Informasi Harian SPKO & NTHKO (Raw Query)')
@section('breadcrumb', 'LAPORAN & ANALITIK / INFORMASI HARIAN')
@section('page_title', 'Laporan Harian SPKO vs NTHKO')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Informasi Harian SPKO dan NTHKO</h1>
            <p class="text-sm text-slate-500 mt-1">
                Monitoring komparasi volume transaksi, kuantitas produk, dan bobot gramatur antara Surat Perintah Kerja Operator (SPKO) dan Nota Terima Hasil Kerja Operator (NTHKO) per hari.
            </p>
        </div>

        <!-- Filter Tanggal -->
        <div class="pt-4 border-t border-slate-100">
            <form method="GET" action="{{ route('reports.daily') }}" id="dateFilterForm" class="flex flex-wrap items-center gap-2.5">
                <span class="text-xs font-semibold text-slate-700">Tanggal:</span>
                <input type="date" name="start_date" id="filter_start_date" value="{{ $startDate }}"
                    class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm">

                <span class="text-xs text-slate-400 font-medium">s/d</span>
                <input type="date" name="end_date" id="filter_end_date" value="{{ $endDate }}"
                    class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm">

                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Filter
                </button>
                @if(!empty($startDate) || !empty($endDate))
                    <a href="{{ route('reports.daily') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-600 text-xs font-medium transition flex items-center gap-1">
                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Table (Daily Grouping) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-slate-900 text-base">
                Rekapitulasi Harian SPKO vs NTHKO
            </h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                {{ count($dailySummaries) }} Hari Terdata
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5 text-center">Jml SPKO</th>
                        <th class="px-6 py-3.5 text-right">Qty SPKO</th>
                        <th class="px-6 py-3.5 text-right">Berat SPKO (Gr)</th>
                        <th class="px-6 py-3.5 text-center">Jml NTHKO</th>
                        <th class="px-6 py-3.5 text-right">Qty NTHKO</th>
                        <th class="px-6 py-3.5 text-right">Berat NTHKO (Gr)</th>
                        <th class="px-6 py-3.5 text-right">Selisih Berat (Gr)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dailySummaries as $row)
                        @php
                            $diff = (float) $row->weight_diff;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->report_date)->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-6 py-3.5 text-center font-medium">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                                    {{ $row->total_spko }} SPKO
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-medium text-slate-800">
                                {{ number_format($row->total_spko_qty) }} Pcs
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono text-slate-800">
                                {{ number_format($row->total_spko_weight, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-center font-medium">
                                <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-xs font-semibold border border-sky-200">
                                    {{ $row->total_nthko }} NTHKO
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-medium text-slate-800">
                                {{ number_format($row->total_nthko_qty) }} Pcs
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono text-slate-800">
                                {{ number_format($row->total_nthko_weight, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-semibold {{ $diff > 0 ? 'text-amber-600' : ($diff < 0 ? 'text-rose-600' : 'text-emerald-600') }}">
                                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Tidak ada data aktivitas transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="font-bold text-slate-900 text-base">
                Rincian Perbandingan SPKO dan NTHKO
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemetaan langsung nomor SPKO dengan Nota Terima Kerja dan operator pelaksana.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal SPKO</th>
                        <th class="px-6 py-3.5">No. SPKO</th>
                        <th class="px-6 py-3.5">Operator</th>
                        <th class="px-6 py-3.5">Proses</th>
                        <th class="px-6 py-3.5">Tanggal NTHKO</th>
                        <th class="px-6 py-3.5 text-right">Qty (SPKO / NTHKO)</th>
                        <th class="px-6 py-3.5 text-right">Berat SPKO (Gr)</th>
                        <th class="px-6 py-3.5 text-right">Berat NTHKO (Gr)</th>
                        <th class="px-6 py-3.5 text-right">Selisih Berat (Gr)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($detailedTransactions as $detail)
                        @php
                            $diff = (float) $detail->selisih_berat;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-3.5 whitespace-nowrap text-slate-700">
                                {{ \Carbon\Carbon::parse($detail->tgl_spko)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900 font-mono">
                                {{ $detail->no_spko }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-medium text-slate-900">{{ $detail->nama_operator }}</span>
                                <span class="text-xs text-slate-400 block">ID: {{ $detail->id_operator }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $detail->proses_spko }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-slate-700">
                                {{ $detail->tgl_nthko ? \Carbon\Carbon::parse($detail->tgl_nthko)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-medium">
                                <span class="text-emerald-700">{{ number_format($detail->qty_spko) }}</span>
                                <span class="text-slate-400 mx-1">/</span>
                                <span class="text-sky-700">{{ number_format($detail->qty_nthko) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono text-slate-800">
                                {{ number_format($detail->berat_spko, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono text-slate-800">
                                {{ number_format($detail->berat_nthko, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-semibold {{ $diff > 0 ? 'text-amber-600' : ($diff < 0 ? 'text-rose-600' : 'text-emerald-600') }}">
                                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">Tidak ada rincian data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const form = $('#dateFilterForm');
        const startDate = $('#filter_start_date');
        const endDate = $('#filter_end_date');

        // Otomatis submit filter saat kedua tanggal sudah terisi dan salah satunya diubah
        function autoSubmit() {
            if (startDate.val() && endDate.val()) {
                form.submit();
            }
        }

        startDate.on('change', function() {
            autoSubmit();
        });

        endDate.on('change', function() {
            autoSubmit();
        });
    });
</script>
@endpush
