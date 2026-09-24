<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    /**
     * Modul Informasi Harian SPKO dan NTHKO (Soal 3).
     * PERATURAN SOAL: Wajib menggunakan Raw Query murni, dilarang menggunakan Eloquent.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Parameter binding untuk keamanan SQL Injection pada raw query
        $summaryBindings = [];
        $summaryFilterSql = "";

        $detailBindings = [];
        $detailFilterSql = "";

        if (!empty($startDate) && !empty($endDate)) {
            $summaryFilterSql = "WHERE dates.report_date BETWEEN ? AND ?";
            $summaryBindings = [$startDate, $endDate];

            $detailFilterSql = "WHERE wa.TransDate BETWEEN ? AND ?";
            $detailBindings = [$startDate, $endDate];
        } elseif (!empty($startDate)) {
            $summaryFilterSql = "WHERE dates.report_date >= ?";
            $summaryBindings = [$startDate];

            $detailFilterSql = "WHERE wa.TransDate >= ?";
            $detailBindings = [$startDate];
        } elseif (!empty($endDate)) {
            $summaryFilterSql = "WHERE dates.report_date <= ?";
            $summaryBindings = [$endDate];

            $detailFilterSql = "WHERE wa.TransDate <= ?";
            $detailBindings = [$endDate];
        }

        // 1. Raw Query: Rekapitulasi Statistik SPKO & NTHKO per Hari
        $dailySummaryQuery = "
            SELECT 
                dates.report_date,
                COALESCE(spko.total_spko, 0) AS total_spko,
                COALESCE(spko.spko_qty, 0) AS total_spko_qty,
                COALESCE(spko.spko_weight, 0.00) AS total_spko_weight,
                COALESCE(nthko.total_nthko, 0) AS total_nthko,
                COALESCE(nthko.nthko_qty, 0) AS total_nthko_qty,
                COALESCE(nthko.nthko_weight, 0.00) AS total_nthko_weight,
                (COALESCE(spko.spko_weight, 0.00) - COALESCE(nthko.nthko_weight, 0.00)) AS weight_diff
            FROM (
                SELECT TransDate AS report_date FROM workallocation
                UNION
                SELECT TransDate AS report_date FROM workcompletion
            ) AS dates
            LEFT JOIN (
                SELECT 
                    wa.TransDate,
                    COUNT(DISTINCT wa.ID) AS total_spko,
                    SUM(COALESCE(wai.Qty, 0)) AS spko_qty,
                    SUM(COALESCE(wai.Weight, 0.00)) AS spko_weight
                FROM workallocation wa
                LEFT JOIN workallocationitem wai ON wa.ID = wai.IDM
                GROUP BY wa.TransDate
            ) AS spko ON dates.report_date = spko.TransDate
            LEFT JOIN (
                SELECT 
                    wc.TransDate,
                    COUNT(DISTINCT wc.ID) AS total_nthko,
                    SUM(COALESCE(wci.Qty, 0)) AS nthko_qty,
                    SUM(COALESCE(wci.Weight, 0.00)) AS nthko_weight
                FROM workcompletion wc
                LEFT JOIN workcompletionitem wci ON wc.ID = wci.IDM
                GROUP BY wc.TransDate
            ) AS nthko ON dates.report_date = nthko.TransDate
            {$summaryFilterSql}
            ORDER BY dates.report_date DESC
        ";

        $dailySummaries = DB::select($dailySummaryQuery, $summaryBindings);

        // 2. Raw Query: Rincian Transaksi SPKO dan NTHKO Harian
        $detailsQuery = "
            SELECT 
                wa.TransDate AS tgl_spko,
                wa.SW AS no_spko,
                wa.Process AS proses_spko,
                e.Id_employee AS id_operator,
                e.nama AS nama_operator,
                wc.ID AS id_nthko,
                wc.TransDate AS tgl_nthko,
                COALESCE(SUM(wai.Qty), 0) AS qty_spko,
                COALESCE(SUM(wai.Weight), 0.00) AS berat_spko,
                COALESCE(nthko_sub.total_qty_nthko, 0) AS qty_nthko,
                COALESCE(nthko_sub.total_berat_nthko, 0.00) AS berat_nthko,
                (COALESCE(SUM(wai.Weight), 0.00) - COALESCE(nthko_sub.total_berat_nthko, 0.00)) AS selisih_berat
            FROM workallocation wa
            JOIN employee e ON wa.Employee = e.Id_employee
            LEFT JOIN workallocationitem wai ON wa.ID = wai.IDM
            LEFT JOIN workcompletion wc ON wa.SW = wc.WorkAllocation
            LEFT JOIN (
                SELECT 
                    wci.IDM,
                    SUM(wci.Qty) AS total_qty_nthko,
                    SUM(wci.Weight) AS total_berat_nthko
                FROM workcompletionitem wci
                GROUP BY wci.IDM
            ) AS nthko_sub ON wc.ID = nthko_sub.IDM
            {$detailFilterSql}
            GROUP BY 
                wa.ID,
                wa.TransDate,
                wa.SW,
                wa.Process,
                e.Id_employee,
                e.nama,
                wc.ID,
                wc.TransDate,
                nthko_sub.total_qty_nthko,
                nthko_sub.total_berat_nthko
            ORDER BY wa.TransDate DESC, wa.SW DESC
        ";

        $detailedTransactions = DB::select($detailsQuery, $detailBindings);

        return view('reports.daily', compact('dailySummaries', 'detailedTransactions', 'startDate', 'endDate'));
    }
}
