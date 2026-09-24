<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkAllocationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['IDM' => 250218000101, 'Ordinal' => 1, 'Qty' => 52, 'Weight' => 147.29, 'FG' => 128409],
            ['IDM' => 250318000101, 'Ordinal' => 1, 'Qty' => 46, 'Weight' => 129.17, 'FG' => 128409],
            ['IDM' => 250318000301, 'Ordinal' => 1, 'Qty' => 3, 'Weight' => 2.54, 'FG' => 767072],
            ['IDM' => 250318000301, 'Ordinal' => 2, 'Qty' => 5, 'Weight' => 4.21, 'FG' => 772839],
            ['IDM' => 250318000301, 'Ordinal' => 3, 'Qty' => 5, 'Weight' => 4.31, 'FG' => 772893],
            ['IDM' => 250518002001, 'Ordinal' => 1, 'Qty' => 50, 'Weight' => 20.57, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 2, 'Qty' => 100, 'Weight' => 41.27, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 3, 'Qty' => 100, 'Weight' => 39.25, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 4, 'Qty' => 1, 'Weight' => 0.39, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 5, 'Qty' => 48, 'Weight' => 19.82, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 1, 'Qty' => 100, 'Weight' => 39.12, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 2, 'Qty' => 100, 'Weight' => 41.16, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 3, 'Qty' => 24, 'Weight' => 9.78, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 4, 'Qty' => 76, 'Weight' => 31.30, 'FG' => 877501],
        ];

        foreach ($items as $item) {
            DB::table('workallocationitem')->updateOrInsert(
                ['IDM' => $item['IDM'], 'Ordinal' => $item['Ordinal']],
                $item
            );
        }
    }
}
