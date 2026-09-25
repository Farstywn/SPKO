<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Product;
use App\Models\WorkAllocation;
use App\Models\WorkCompletion;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test menampilkan halaman daftar SPKO.
     */
    public function test_can_view_spko_index(): void
    {
        $response = $this->get('/spko');
        $response->assertStatus(200);
        $response->assertSee('Daftar Transaksi SPKO');
    }

    /**
     * Test pembuatan SPKO dan sinkronisasi Nota Terima Kerja (Create).
     */
    public function test_can_create_spko_and_workcompletion(): void
    {
        $employee = Employee::first();
        $product = Product::first();

        $postData = [
            'spko_no'     => 'SPKO2609999',
            'trans_date'  => '2026-09-24',
            'employee_id' => $employee->Id_employee,
            'process'     => 'Cor',
            'remarks'     => 'Uji Otomatis SPKO',
            'items'       => [
                [
                    'fg'     => $product->Id_product,
                    'qty'    => 20,
                    'weight' => 50.00,
                ],
            ],
        ];

        $response = $this->post('/spko', $postData);
        $response->assertRedirect('/spko');

        // Pastikan workallocation terbentuk
        $this->assertDatabaseHas('workallocation', [
            'SW'       => 'SPKO2609999',
            'Employee' => $employee->Id_employee,
            'Process'  => 'Cor',
        ]);

        // Pastikan workcompletion terbentuk mengikuti SPKO
        $this->assertDatabaseHas('workcompletion', [
            'WorkAllocation' => 'SPKO2609999',
            'Employee'       => $employee->Id_employee,
            'Process'        => 'Cor',
        ]);
    }

    /**
     * Test update SPKO: ubah operator, tanggal, dan Qty item (Update).
     */
    public function test_can_update_spko(): void
    {
        $spko = WorkAllocation::first();
        $otherEmployee = Employee::where('Id_employee', '!=', $spko->Employee)->first() ?? $spko->employee;
        $product = Product::first();

        $updateData = [
            'trans_date'  => '2026-09-25',
            'employee_id' => $otherEmployee->Id_employee,
            'process'     => 'Bombing',
            'remarks'     => 'Catatan diperbarui',
            'items'       => [
                [
                    'fg'     => $product->Id_product,
                    'qty'    => 35,
                    'weight' => 87.50,
                ],
            ],
        ];

        $response = $this->put("/spko/{$spko->ID}", $updateData);
        $response->assertRedirect('/spko');

        $this->assertDatabaseHas('workallocation', [
            'ID'        => $spko->ID,
            'Employee'  => $otherEmployee->Id_employee,
            'Process'   => 'Bombing',
        ]);
        $this->assertEquals('2026-09-25', WorkAllocation::find($spko->ID)->TransDate->format('Y-m-d'));
    }

    /**
     * Test cetak SPKO (Print).
     */
    public function test_can_print_spko(): void
    {
        $spko = WorkAllocation::first();
        $response = $this->get("/spko/{$spko->ID}/print");
        $response->assertStatus(200);
        $response->assertSee('Surat Perintah Kerja Operator');
        $response->assertSee($spko->SW);
    }

    /**
     * Test hapus SPKO dan nota terima kerja terkait (Delete).
     */
    public function test_can_delete_spko(): void
    {
        $spko = WorkAllocation::first();
        $sw = $spko->SW;
        $id = $spko->ID;

        $response = $this->delete("/spko/{$id}");
        $response->assertRedirect('/spko');

        $this->assertDatabaseMissing('workallocation', [
            'ID' => $id,
        ]);

        $this->assertDatabaseMissing('workcompletion', [
            'WorkAllocation' => $sw,
        ]);
    }
}
