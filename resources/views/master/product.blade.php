@extends('layouts.app')

@section('title', 'Master Data Produk (FG)')
@section('breadcrumb', 'MASTER DATA / PRODUK')
@section('page_title', 'Data Finished Goods (FG)')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Toolbar Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Katalog Produk Finished Goods (FG)</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        {{ $products->total() }} Produk
                    </span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Master referensi perhiasan dan barang jadi untuk dialokasikan pada item transaksi SPKO.
                </p>
            </div>

            <!-- Filters Form -->
            <form action="{{ route('master.product') }}" method="GET" class="w-full lg:w-auto flex flex-wrap items-center gap-2">
                <!-- Category Select -->
                <div class="w-full sm:w-44">
                    <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:outline-none focus:border-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ ($category ?? '') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-60">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari SKU, deskripsi, serial..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50/50">
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition">
                    Filter
                </button>

                @if(!empty($search) || !empty($category))
                    <a href="{{ route('master.product') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition" title="Reset Filter">
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
                        <th class="px-6 py-3.5">ID Produk</th>
                        <th class="px-6 py-3.5">Kode SKU Produk</th>
                        <th class="px-6 py-3.5">Deskripsi Produk</th>
                        <th class="px-6 py-3.5">Sub Kategori</th>
                        <th class="px-6 py-3.5">Kadar (Carat)</th>
                        <th class="px-6 py-3.5">Serial No</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-800">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    {{ $prod->Id_product }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs font-semibold text-emerald-800">
                                <span class="px-2 py-1 rounded-md bg-emerald-50 border border-emerald-200">
                                    {{ $prod->sku }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="font-semibold text-slate-900 text-xs">{{ $prod->description }}</div>
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <span class="px-2.5 py-0.5 rounded-full font-medium text-[11px] bg-slate-100 text-slate-700">
                                    {{ $prod->sub_category }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-xs font-medium text-amber-700">
                                <span class="px-2 py-0.5 rounded bg-amber-50 border border-amber-200">
                                    {{ $prod->carat }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs text-slate-500">
                                {{ $prod->serial_no }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Siap Pakai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                Tidak ada data produk yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
