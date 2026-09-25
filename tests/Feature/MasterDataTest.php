<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test tampilan dashboard operasional ERP.
     */
    public function test_can_view_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Operasional');
        $response->assertSee('Total Transaksi SPKO');
        $response->assertSee('Operator Aktif');
    }

    /**
     * Test tampilan master data operator (employee).
     */
    public function test_can_view_master_employee(): void
    {
        $response = $this->get('/master/employee');
        $response->assertStatus(200);
        $response->assertSee('Master Operator (Karyawan)');
        $response->assertSee('ID Operator');
    }

    /**
     * Test tampilan master data produk (FG).
     */
    public function test_can_view_master_product(): void
    {
        $response = $this->get('/master/product');
        $response->assertStatus(200);
        $response->assertSee('Katalog Produk Finished Goods (FG)');
        $response->assertSee('Kode SKU Produk');
    }

    /**
     * Test tampilan daftar transaksi NTHKO.
     */
    public function test_can_view_nthko_index(): void
    {
        $response = $this->get('/nthko');
        $response->assertStatus(200);
        $response->assertSee('Nota Terima Hasil Kerja Operator (NTHKO)');
        $response->assertSee('Rujukan SPKO');
    }
}
