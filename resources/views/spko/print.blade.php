<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPKO - {{ $allocation->SW }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #111;
        }

        body {
            background-color: #f1f5f9;
            padding: 24px;
        }

        .no-print-toolbar {
            max-width: 800px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #0f172a;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1e293b;
        }

        .btn-outline {
            background-color: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-outline:hover {
            background-color: #f8fafc;
        }

        /* Container Print Sheet */
        .print-sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 1.5px solid #22c55e; /* Green border as in PDF reference */
            padding: 24px 28px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sheet-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 16px;
            color: #0f172a;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 32px;
            row-gap: 6px;
            margin-bottom: 20px;
        }

        .meta-row {
            display: flex;
            align-items: baseline;
            line-height: 1.6;
        }

        .meta-label {
            width: 110px;
            font-weight: normal;
        }

        .meta-colon {
            width: 14px;
        }

        .meta-val {
            flex: 1;
            font-weight: normal;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #64748b;
            padding: 7px 10px;
            font-size: 12px;
        }

        .items-table th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }

        .items-table td.col-no {
            text-align: center;
            width: 45px;
        }

        .items-table td.col-desc {
            text-align: left;
        }

        .items-table td.col-carat {
            text-align: center;
            width: 90px;
        }

        .items-table td.col-sku {
            text-align: center;
            font-family: monospace;
            font-size: 11.5px;
            width: 240px;
        }

        .items-table td.col-qty {
            text-align: center;
            width: 60px;
            font-weight: normal;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .print-sheet {
                border: 1.5px solid #22c55e;
                box-shadow: none;
                padding: 20px 24px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Top Toolbar (Hidden on Print) -->
    <div class="no-print-toolbar">
        <div>
            <strong>Preview Dokumen SPKO</strong>
            <span style="color: #64748b; font-size: 12px; margin-left: 8px;">(Sesuai format resmi halaman 4)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak / Print
            </button>
            <button onclick="window.close()" class="btn btn-outline">
                ✕ Tutup
            </button>
        </div>
    </div>

    <!-- Printable Area (Formatted exactly as PDF page 4) -->
    <div class="print-sheet">
        <div class="sheet-title">
            Surat Perintah Kerja Operator
        </div>

        <div class="meta-grid">
            <!-- Left Column -->
            <div>
                <div class="meta-row">
                    <span class="meta-label">ID Operator</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $allocation->Employee }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Nama Operator</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $allocation->employee->nama ?? '-' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Tanggal</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">
                        {{ \Carbon\Carbon::parse($allocation->TransDate)->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div class="meta-row">
                    <span class="meta-label">No SPKO</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val" style="font-weight: bold;">{{ $allocation->SW }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Proses</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $allocation->Process }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Catatan</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $allocation->Remarks ?: '' }}</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No.</th>
                    <th>Deskripsi</th>
                    <th style="width: 90px;">Carat</th>
                    <th style="width: 240px;">SKU</th>
                    <th style="width: 60px;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocation->items as $index => $item)
                    <tr>
                        <td class="col-no">{{ $item->Ordinal }}</td>
                        <td class="col-desc">{{ $item->product->description ?? ('FG ' . $item->FG) }}</td>
                        <td class="col-carat">{{ $item->product->carat ?? '-' }}</td>
                        <td class="col-sku">{{ $item->product->sku ?? '-' }}</td>
                        <td class="col-qty">{{ $item->Qty }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">
                            Tidak ada item produk terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
