@extends('layouts.app')

@section('title', 'Edit Transaksi SPKO ' . $allocation->SW)
@section('breadcrumb', 'TRANSAKSI / SPKO / EDIT')
@section('page_title', 'Edit Transaksi SPKO')

@section('content')
<div class="w-full space-y-6">
    <!-- Breadcrumb & Title -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('spko.index') }}" class="hover:text-emerald-700">SPKO</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Edit Transaksi</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Transaksi SPKO: {{ $allocation->SW }}</h1>
            <p class="text-sm text-slate-500">Ubah data operator, tanggal transaksi, catatan, dan jumlah Qty per item produk.</p>
        </div>
        <a href="{{ route('spko.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <form action="{{ route('spko.update', $allocation->ID) }}" method="POST" id="spkoEditForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Main Information Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <h2 class="text-base font-semibold text-slate-900 pb-3 border-b border-slate-100">
                Informasi Transaksi
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- No SPKO (Readonly) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor SPKO (Terkunci)
                    </label>
                    <input type="text" value="{{ $allocation->SW }}" readonly
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold bg-slate-100 text-slate-600 cursor-not-allowed">
                    <span class="text-[11px] text-slate-400 mt-1 block">ID: {{ $allocation->ID }}</span>
                </div>

                <!-- Mengganti Tanggal Transaksi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="trans_date" id="trans_date" value="{{ old('trans_date', \Carbon\Carbon::parse($allocation->TransDate)->format('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <!-- Mengganti Operator -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Operator (Employee) <span class="text-rose-500">*</span>
                    </label>
                    <select name="employee_id" id="employee_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 bg-white">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->Id_employee }}" {{ old('employee_id', $allocation->Employee) == $emp->Id_employee ? 'selected' : '' }}>
                                {{ $emp->nama }} (ID: {{ $emp->Id_employee }}) - {{ $emp->rank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Proses -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Proses <span class="text-rose-500">*</span>
                    </label>
                    <select name="process" id="process" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 bg-white">
                        @foreach($processes as $p)
                            <option value="{{ $p }}" {{ old('process', $allocation->Process) == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Catatan / Remarks -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Remarks</label>
                <textarea name="remarks" id="remarks" rows="2" placeholder="Catatan tambahan..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">{{ old('remarks', $allocation->Remarks) }}</textarea>
            </div>
        </div>

        <!-- Product Items Card (Edit Qty per Item) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-3 border-b border-slate-100 gap-2">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Detail Item Produk & Ubah Qty
                    </h2>
                    <p class="text-xs text-slate-500">Ubah jumlah (Qty) atau bobot berat per item produk.</p>
                </div>
                <button type="button" id="btnAddRow" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition">
                    + Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="itemsTable">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase border-y border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No.</th>
                            <th class="px-4 py-3 min-w-[260px]">Pilih Produk</th>
                            <th class="px-4 py-3 w-36">Carat</th>
                            <th class="px-4 py-3 min-w-[180px]">SKU</th>
                            <th class="px-4 py-3 w-28">Qty (Pcs) <span class="text-rose-500">*</span></th>
                            <th class="px-4 py-3 w-32">Berat (Gr)</th>
                            <th class="px-4 py-3 w-16 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer" class="divide-y divide-slate-100">
                        <!-- Loaded dynamically via jQuery -->
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-slate-100 text-sm font-semibold text-slate-700">
                <span id="totalItemsSummary">Total: 0 Item</span>
                <span id="totalQtySummary">Total Qty: 0 Pcs</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('spko.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/30 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const productsData = @json($products);
    const existingItems = @json($allocation->items);

    $(document).ready(function() {
        // Initialize Select2 for Operator (Employee)
        $('#employee_id').select2({
            placeholder: '-- Cari & Pilih Operator --',
            allowClear: false,
            width: '100%'
        });

        let rowIndex = 0;

        function addRow(selectedFg = '', qtyVal = 1, weightVal = '') {
            rowIndex++;
            let optionsHtml = '<option value="">-- Cari & Pilih Produk --</option>';
            productsData.forEach(p => {
                const isSelected = (selectedFg == p.Id_product) ? 'selected' : '';
                optionsHtml += `<option value="${p.Id_product}" data-carat="${p.carat}" data-sku="${p.sku}" data-desc="${p.description}" ${isSelected}>${p.description} (${p.sub_category})</option>`;
            });

            const trHtml = `
                <tr class="item-row hover:bg-slate-50/50 transition" data-index="${rowIndex}">
                    <td class="px-4 py-3 text-center font-mono text-xs font-semibold text-slate-500 row-number">
                        ${rowIndex}
                    </td>
                    <td class="px-4 py-3">
                        <select name="items[${rowIndex}][fg]" class="fg-select w-full" required>
                            ${optionsHtml}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" class="carat-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-100 text-xs font-medium text-slate-700 cursor-not-allowed" readonly placeholder="-">
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" class="sku-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-100 font-mono text-xs text-slate-700 cursor-not-allowed" readonly placeholder="-">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowIndex}][qty]" value="${qtyVal}" min="1" class="qty-input w-full px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-amber-500" required>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.01" name="items[${rowIndex}][weight]" value="${weightVal}" placeholder="0.00" class="weight-input w-full px-3 py-2 rounded-lg border border-slate-300 text-sm font-mono text-slate-900 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="btn-remove-row w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 transition" title="Hapus Baris">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#itemsContainer').append(trHtml);
            const newTr = $(`tr[data-index="${rowIndex}"]`);
            const selectEl = newTr.find('.fg-select');

            // Initialize Select2 on the new product select
            selectEl.select2({
                placeholder: '-- Cari & Pilih Produk --',
                allowClear: false,
                width: '100%'
            });

            if (selectedFg) {
                selectEl.val(selectedFg).trigger('change');
            }
            updateRowNumbers();
            calculateTotals();
        }

        function updateRowNumbers() {
            $('#itemsContainer tr.item-row').each(function(idx) {
                $(this).find('.row-number').text(idx + 1);
            });
        }

        function calculateTotals() {
            let totalRows = $('#itemsContainer tr.item-row').length;
            let totalQty = 0;
            $('.qty-input').each(function() {
                totalQty += parseInt($(this).val()) || 0;
            });
            $('#totalItemsSummary').text(`Total: ${totalRows} Item`);
            $('#totalQtySummary').text(`Total Qty: ${totalQty} Pcs`);
        }

        $(document).on('change', '.fg-select', function() {
            const selectedOpt = $(this).find('option:selected');
            const row = $(this).closest('tr');
            const carat = selectedOpt.data('carat') || '';
            const sku = selectedOpt.data('sku') || '';
            row.find('.carat-input').val(carat);
            row.find('.sku-input').val(sku);
        });

        $(document).on('input', '.qty-input', function() {
            calculateTotals();
        });

        $('#btnAddRow').click(function() {
            addRow();
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#itemsContainer tr.item-row').length > 1) {
                const row = $(this).closest('tr');
                row.find('.fg-select').select2('destroy');
                row.remove();
                updateRowNumbers();
                calculateTotals();
            } else {
                alert('Transaksi minimal memiliki 1 baris item produk!');
            }
        });

        // Load existing items
        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addRow(item.FG, item.Qty, item.Weight);
            });
        } else {
            addRow();
        }
    });
</script>
@endpush
