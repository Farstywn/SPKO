<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportRawQueryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test modul laporan harian dengan Raw Query (Soal 3).
     */
    public function test_can_view_daily_report_with_raw_query(): void
    {
        $response = $this->get('/reports/daily-spko-nthko');
        $response->assertStatus(200);
        $response->assertSee('Informasi Harian SPKO dan NTHKO');
        $response->assertSee('Rekapitulasi Harian SPKO vs NTHKO');
        $response->assertSee('Rincian Perbandingan SPKO dan NTHKO');
    }

    /**
     * Test filter tanggal memfilter kedua tabel (rekapitulasi dan rincian).
     */
    public function test_can_filter_daily_report_by_date(): void
    {
        $response = $this->get('/reports/daily-spko-nthko?start_date=2024-01-01&end_date=2024-01-02');
        $response->assertStatus(200);
        $response->assertSee('Informasi Harian SPKO dan NTHKO');
    }
}
