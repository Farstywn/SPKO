@extends('layouts.app')

@section('title', 'Master Data Operator')
@section('breadcrumb', 'MASTER DATA / OPERATOR')
@section('page_title', 'Data Operator (Employee)')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Toolbar Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Master Operator (Karyawan)</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        {{ $employees->total() }} Data
                    </span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Daftar operator dan teknisi produksi yang berhak menerima Surat Perintah Kerja Operator (SPKO).
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('master.employee') }}" method="GET" class="w-full md:w-auto flex items-center gap-2">
                <div class="relative w-full md:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari ID, nama, jabatan..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50/50">
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('master.employee') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition" title="Reset Pencarian">
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
                        <th class="px-6 py-3.5">ID Operator</th>
                        <th class="px-6 py-3.5">Nama Operator</th>
                        <th class="px-6 py-3.5">Jabatan / Rank</th>
                        <th class="px-6 py-3.5">Gender</th>
                        <th class="px-6 py-3.5">Tanggal Masuk</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-800">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    {{ $emp->Id_employee }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="font-semibold text-slate-900 text-xs">{{ $emp->nama }}</div>
                                <div class="text-[11px] text-slate-400">Teknisi Produksi</div>
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <span class="px-2.5 py-1 rounded-full font-semibold text-[11px] bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $emp->rank ?? 'Operator' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-600">
                                @if(strtoupper($emp->gender) === 'L' || strtoupper($emp->gender) === 'PRIA' || strtoupper($emp->gender) === 'M')
                                    <span class="inline-flex items-center gap-1 text-slate-700">
                                        <i class="fa-solid fa-mars text-sky-500"></i> Laki-laki
                                    </span>
                                @elseif(strtoupper($emp->gender) === 'P' || strtoupper($emp->gender) === 'WANITA' || strtoupper($emp->gender) === 'F')
                                    <span class="inline-flex items-center gap-1 text-slate-700">
                                        <i class="fa-solid fa-venus text-rose-500"></i> Perempuan
                                    </span>
                                @else
                                    <span class="text-slate-400">{{ $emp->gender ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ $emp->entry_date ? \Carbon\Carbon::parse($emp->entry_date)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">
                                Tidak ada data operator yang sesuai dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
