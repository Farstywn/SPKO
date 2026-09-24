<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkCompletionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['IDM' => 250218000101, 'Ordinal' => 1, 'Qty' => 52, 'Weight' => 140.15, 'LinkID' => 250218000101, 'LinkOrd' => 1, 'FG' => 128409],
            ['IDM' => 250318000101, 'Ordinal' => 1, 'Qty' => 43, 'Weight' => 125.28, 'LinkID' => 250318000101, 'LinkOrd' => 1, 'FG' => 128409],
            ['IDM' => 250318000301, 'Ordinal' => 1, 'Qty' => 3, 'Weight' => 2.32, 'LinkID' => 250318000301, 'LinkOrd' => 1, 'FG' => 767072],
            ['IDM' => 250318000301, 'Ordinal' => 2, 'Qty' => 5, 'Weight' => 3.91, 'LinkID' => 250318000301, 'LinkOrd' => 2, 'FG' => 772839],
            ['IDM' => 250318000301, 'Ordinal' => 3, 'Qty' => 5, 'Weight' => 4.11, 'LinkID' => 250318000301, 'LinkOrd' => 3, 'FG' => 772893],
            ['IDM' => 250518002001, 'Ordinal' => 1, 'Qty' => 50, 'Weight' => 19.89, 'LinkID' => 250518002001, 'LinkOrd' => 1, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 2, 'Qty' => 98, 'Weight' => 38.76, 'LinkID' => 250518002001, 'LinkOrd' => 2, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 3, 'Qty' => 100, 'Weight' => 38.99, 'LinkID' => 250518002001, 'LinkOrd' => 3, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 4, 'Qty' => 1, 'Weight' => 0.32, 'LinkID' => 250518002001, 'LinkOrd' => 4, 'FG' => 877501],
            ['IDM' => 250518002001, 'Ordinal' => 5, 'Qty' => 48, 'Weight' => 19.12, 'LinkID' => 250518002001, 'LinkOrd' => 5, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 1, 'Qty' => 100, 'Weight' => 38.12, 'LinkID' => 250618000401, 'LinkOrd' => 1, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 2, 'Qty' => 100, 'Weight' => 40.06, 'LinkID' => 250618000401, 'LinkOrd' => 2, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 3, 'Qty' => 24, 'Weight' => 9.18, 'LinkID' => 250618000401, 'LinkOrd' => 3, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 4, 'Qty' => 50, 'Weight' => 20.00, 'LinkID' => 250618000401, 'LinkOrd' => 4, 'FG' => 877501],
            ['IDM' => 250618000401, 'Ordinal' => 5, 'Qty' => 26, 'Weight' => 10.60, 'LinkID' => 250618000401, 'LinkOrd' => 4, 'FG' => 877501],
        ];

        foreach ($items as $item) {
            DB::table('workcompletionitem')->updateOrInsert(
                ['IDM' => $item['IDM'], 'Ordinal' => $item['Ordinal']],
                $item
            );
        }
    }
}
