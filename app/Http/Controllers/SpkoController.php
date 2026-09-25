<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\WorkAllocation;
use App\Models\WorkAllocationItem;
use App\Models\WorkCompletion;
use App\Models\WorkCompletionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpkoController extends Controller
{
    /**
     * Tampilkan daftar transaksi SPKO (Work Allocation).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $process = $request->query('process');

        $query = WorkAllocation::with(['employee', 'items.product', 'workCompletion']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('SW', 'like', "%{$search}%")
                  ->orWhere('ID', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($process)) {
            $query->where('Process', $process);
        }

        $allocations = $query->orderBy('TransDate', 'desc')
            ->orderBy('ID', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('spko.index', compact('allocations', 'search', 'process'));
    }


    /**
     * Endpoint API untuk mengecek dan mengambil nomor SPKO berikutnya secara real-time.
     */
    public function getSuggestNumber(Request $request)
    {
        $date = $request->query('trans_date', now()->toDateString());
        $suggested = $this->generateSpkoNumber($date);

        return response()->json([
            'status' => 'success',
            'suggested_no' => $suggested,
        ]);
    }

    /**
     * Form pembuatan SPKO dan Nota Terima Kerja baru.
     */
    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $products = Product::orderBy('description')->get();
        $processes = ['Cor', 'Brush', 'Bombing', 'Slep'];
        $suggestedSpkoNo = $this->generateSpkoNumber(now()->toDateString());

        return view('spko.create', compact('employees', 'products', 'processes', 'suggestedSpkoNo'));
    }

    /**
     * Simpan SPKO baru beserta Nota Terima Kerja (mengikuti data SPKO).
     * Dilengkapi atomic concurrency lock untuk mencegah nomor SPKO dan ID duplikat saat multi-user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,Id_employee',
            'trans_date'  => 'required|date',
            'process'     => 'required|in:Cor,Brush,Bombing,Slep',
            'remarks'     => 'nullable|string|max:500',
            'spko_no'     => 'nullable|string|max:25',
            'items'       => 'required|array|min:1',
            'items.*.fg'  => 'required|exists:product,Id_product',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $transDate = Carbon::parse($validated['trans_date']);
            $newId = $this->generateNumericId($transDate, true);

            $requestedSpkoNo = !empty($validated['spko_no']) ? trim($validated['spko_no']) : null;
            $wasAutoAllocated = false;
            $originalRequested = $requestedSpkoNo;

            // Kunci dan periksa apakah nomor SPKO yang diminta sudah digunakan oleh transaksi lain
            if (!empty($requestedSpkoNo)) {
                $isTaken = WorkAllocation::where('SW', $requestedSpkoNo)->lockForUpdate()->exists();
                if ($isTaken) {
                    // Jika sudah terpakai saat form dibuka bersamaan, alokasikan nomor unik berikutnya secara otomatis
                    $spkoNo = $this->generateSpkoNumber($validated['trans_date'], true);
                    $wasAutoAllocated = true;
                } else {
                    $spkoNo = $requestedSpkoNo;
                }
            } else {
                $spkoNo = $this->generateSpkoNumber($validated['trans_date'], true);
            }

            // 1. Simpan Surat Perintah Kerja (workallocation)
            $allocation = WorkAllocation::create([
                'ID'        => $newId,
                'Remarks'   => $validated['remarks'] ?? null,
                'Employee'  => $validated['employee_id'],
                'TransDate' => $transDate->toDateString(),
                'Process'   => $validated['process'],
                'SW'        => $spkoNo,
            ]);

            // 2. Simpan Item SPKO (workallocationitem)
            $ordinal = 1;
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];
                $weight = isset($item['weight']) && $item['weight'] !== ''
                    ? (float) $item['weight']
                    : round($qty * 2.5, 2);

                WorkAllocationItem::create([
                    'IDM'     => $allocation->ID,
                    'Ordinal' => $ordinal,
                    'Qty'     => $qty,
                    'Weight'  => $weight,
                    'FG'      => (int) $item['fg'],
                ]);
                $ordinal++;
            }

            // 3. Simpan Nota Terima Kerja (workcompletion) - mengikuti data SPKO
            $completion = WorkCompletion::create([
                'ID'             => $newId,
                'Remarks'        => $validated['remarks'] ?? null,
                'Employee'       => $validated['employee_id'],
                'TransDate'      => $transDate->toDateString(),
                'Process'        => $validated['process'],
                'WorkAllocation' => $spkoNo,
            ]);

            // 4. Simpan Item Nota Terima Kerja (workcompletionitem) - sinkron dengan item SPKO
            $ordinal = 1;
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];
                $weight = isset($item['weight']) && $item['weight'] !== ''
                    ? (float) $item['weight']
                    : round($qty * 2.5, 2);

                WorkCompletionItem::create([
                    'IDM'     => $completion->ID,
                    'Ordinal' => $ordinal,
                    'Qty'     => $qty,
                    'Weight'  => $weight,
                    'LinkID'  => $allocation->ID,
                    'LinkOrd' => $ordinal,
                    'FG'      => (int) $item['fg'],
                ]);
                $ordinal++;
            }

            DB::commit();

            $successMsg = $wasAutoAllocated
                ? "Nomor SPKO '{$originalRequested}' telah digunakan oleh transaksi lain. Sistem secara otomatis menerbitkan nomor baru: {$spkoNo}."
                : "Surat Perintah Kerja {$spkoNo} dan Nota Terima Kerja berhasil dibuat!";

            return redirect()->route('spko.index')->with('success', $successMsg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat SPKO: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail transaksi SPKO dan Nota Terima Kerja.
     */
    public function show($id)
    {
        $allocation = WorkAllocation::with(['employee', 'items.product', 'workCompletion.items.product'])
            ->findOrFail($id);

        return view('spko.show', compact('allocation'));
    }

    /**
     * Form edit SPKO (Ubah Operator, Tanggal Transaksi, dan Qty per Item).
     */
    public function edit($id)
    {
        $allocation = WorkAllocation::with(['employee', 'items.product', 'workCompletion.items'])
            ->findOrFail($id);
        $employees = Employee::orderBy('nama')->get();
        $products = Product::orderBy('description')->get();
        $processes = ['Cor', 'Brush', 'Bombing', 'Slep'];

        return view('spko.edit', compact('allocation', 'employees', 'products', 'processes'));
    }

    /**
     * Update transaksi SPKO dan sinkronisasi ke Nota Terima Kerja.
     */
    public function update(Request $request, $id)
    {
        $allocation = WorkAllocation::with('items')->findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,Id_employee',
            'trans_date'  => 'required|date',
            'process'     => 'required|in:Cor,Brush,Bombing,Slep',
            'remarks'     => 'nullable|string|max:500',
            'items'       => 'required|array|min:1',
            'items.*.fg'  => 'required|exists:product,Id_product',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $transDate = Carbon::parse($validated['trans_date'])->toDateString();

            // 1. Update Surat Perintah Kerja
            $allocation->update([
                'Employee'  => $validated['employee_id'],
                'TransDate' => $transDate,
                'Process'   => $validated['process'],
                'Remarks'   => $validated['remarks'] ?? null,
            ]);

            // 2. Refresh Items SPKO
            WorkAllocationItem::where('IDM', $allocation->ID)->delete();
            $ordinal = 1;
            $itemsData = [];
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];
                $weight = isset($item['weight']) && $item['weight'] !== ''
                    ? (float) $item['weight']
                    : round($qty * 2.5, 2);

                WorkAllocationItem::create([
                    'IDM'     => $allocation->ID,
                    'Ordinal' => $ordinal,
                    'Qty'     => $qty,
                    'Weight'  => $weight,
                    'FG'      => (int) $item['fg'],
                ]);

                $itemsData[] = [
                    'ordinal' => $ordinal,
                    'qty'     => $qty,
                    'weight'  => $weight,
                    'fg'      => (int) $item['fg'],
                ];
                $ordinal++;
            }

            // 3. Update Nota Terima Kerja (WorkCompletion)
            $completion = WorkCompletion::where('WorkAllocation', $allocation->SW)
                ->orWhere('ID', $allocation->ID)
                ->first();

            if ($completion) {
                $completion->update([
                    'Employee'  => $validated['employee_id'],
                    'TransDate' => $transDate,
                    'Process'   => $validated['process'],
                    'Remarks'   => $validated['remarks'] ?? null,
                ]);

                WorkCompletionItem::where('IDM', $completion->ID)->delete();
                foreach ($itemsData as $row) {
                    WorkCompletionItem::create([
                        'IDM'     => $completion->ID,
                        'Ordinal' => $row['ordinal'],
                        'Qty'     => $row['qty'],
                        'Weight'  => $row['weight'],
                        'LinkID'  => $allocation->ID,
                        'LinkOrd' => $row['ordinal'],
                        'FG'      => $row['fg'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('spko.index')
                ->with('success', "Transaksi SPKO {$allocation->SW} berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui SPKO: ' . $e->getMessage());
        }
    }

    /**
     * Hapus transaksi SPKO beserta Nota Terima Kerja terkait.
     */
    public function destroy($id)
    {
        $allocation = WorkAllocation::findOrFail($id);

        DB::beginTransaction();
        try {
            $sw = $allocation->SW;

            // Hapus Nota Terima Kerja terkait
            $completions = WorkCompletion::where('WorkAllocation', $sw)
                ->orWhere('ID', $allocation->ID)
                ->get();

            foreach ($completions as $comp) {
                WorkCompletionItem::where('IDM', $comp->ID)->delete();
                $comp->delete();
            }

            // Hapus items SPKO dan header SPKO
            WorkAllocationItem::where('IDM', $allocation->ID)->delete();
            $allocation->delete();

            DB::commit();

            return redirect()->route('spko.index')
                ->with('success', "Transaksi SPKO {$sw} berhasil dihapus!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus SPKO: ' . $e->getMessage());
        }
    }

    /**
     * Cetak dokumen resmi SPKO sesuai format Soal 2 (Halaman 4 PDF).
     */
    public function print($id)
    {
        $allocation = WorkAllocation::with(['employee', 'items.product'])->findOrFail($id);

        return view('spko.print', compact('allocation'));
    }

    /**
     * Helper membuat nomor urut SPKO unik berformat 'SPKO{yy}{mm}{001}'.
     * Menghitung nomor urut tertinggi secara akurat dan mendukung pessimistic lock untuk mencegah race condition.
     */
    protected function generateSpkoNumber(string $dateString, bool $lock = false): string
    {
        $date = Carbon::parse($dateString);
        $prefix = 'SPKO' . $date->format('y') . $date->format('m');

        $query = WorkAllocation::where('SW', 'LIKE', "{$prefix}%");
        if ($lock) {
            $query->lockForUpdate();
        }

        $existingNumbers = $query->pluck('SW');

        $maxSequence = 0;
        foreach ($existingNumbers as $sw) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $sw, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSequence) {
                    $maxSequence = $seq;
                }
            }
        }

        $nextSequence = $maxSequence + 1;
        return $prefix . str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Helper membuat Numeric ID unik 12 digit untuk tabel workallocation/workcompletion.
     * Mengikuti pola referensi data: {YY}{MM}{DD}{SEQ:4}01 dengan pessimistic lock.
     */
    protected function generateNumericId(Carbon $date, bool $lock = false): int
    {
        $datePrefix = $date->format('ymd');

        do {
            $randomSeq = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $candidate = (int) ($datePrefix . $randomSeq . '01');

            $waQuery = WorkAllocation::where('ID', $candidate);
            $wcQuery = WorkCompletion::where('ID', $candidate);

            if ($lock) {
                $waQuery->lockForUpdate();
                $wcQuery->lockForUpdate();
            }

            $exists = $waQuery->exists() || $wcQuery->exists();
        } while ($exists);

        return $candidate;
    }
}
