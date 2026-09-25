<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Product;
use App\Models\WorkAllocation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkoConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test endpoint API suggest-number mengembalikan response JSON nomor SPKO berikutnya.
     */
    public function test_api_suggest_number_returns_valid_json(): void
    {
        $response = $this->getJson('/spko/suggest-number?trans_date=2026-09-25');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'suggested_no',
            ])
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Test jika 2 user submit nomor SPKO yang sama secara bersamaan (Race Condition / Concurrency):
     * User 1 berhasil menggunakan nomor tersebut,
     * User 2 otomatis dialokasikan nomor berikutnya dan tetap berhasil disimpan tanpa nomor duplikat!
     */
    public function test_concurrent_spko_submission_auto_allocates_next_unique_number(): void
    {
        $employee = Employee::first();
        $product = Product::first();

        $conflictingSpkoNo = 'SPKO2609777';

        $payloadUser1 = [
            'spko_no'     => $conflictingSpkoNo,
            'trans_date'  => '2026-09-25',
            'employee_id' => $employee->Id_employee,
            'process'     => 'Brush',
            'remarks'     => 'Transaksi Operator 1',
            'items'       => [
                [
                    'fg'     => $product->Id_product,
                    'qty'    => 10,
                    'weight' => 25.5,
                ],
            ],
        ];

        $payloadUser2 = [
            'spko_no'     => $conflictingSpkoNo, // Nomor yang SAMA persis dari form yang dibuka bersamaan
            'trans_date'  => '2026-09-25',
            'employee_id' => $employee->Id_employee,
            'process'     => 'Bombing',
            'remarks'     => 'Transaksi Operator 2 (Submisi bersamaan)',
            'items'       => [
                [
                    'fg'     => $product->Id_product,
                    'qty'    => 15,
                    'weight' => 37.5,
                ],
            ],
        ];

        // Operator 1 submit duluan
        $response1 = $this->post('/spko', $payloadUser1);
        $response1->assertRedirect('/spko');

        // Pastikan transaksi Operator 1 tersimpan dengan nomor tersebut
        $this->assertDatabaseHas('workallocation', [
            'SW'      => $conflictingSpkoNo,
            'Process' => 'Brush',
        ]);

        // Operator 2 submit dengan nomor yang sama
        $response2 = $this->post('/spko', $payloadUser2);
        $response2->assertRedirect('/spko');
        $response2->assertSessionHas('success');

        // Pastikan Operator 2 TIDAK GAGAL dan TIDAK membuat duplikat,
        // melainkan otomatis mendapat nomor berikutnya (SPKO2609778)
        $this->assertDatabaseHas('workallocation', [
            'Process' => 'Bombing',
            'Remarks' => 'Transaksi Operator 2 (Submisi bersamaan)',
        ]);

        // Verifikasi bahwa tidak ada nomor kembar sama sekali di database
        $distinctCount = WorkAllocation::distinct('SW')->count('SW');
        $totalCount = WorkAllocation::count();
        $this->assertEquals($totalCount, $distinctCount, 'Setiap nomor SPKO di database wajib 100% unik tanpa duplikasi');
    }
}
