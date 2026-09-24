<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkCompletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completions = [
            [
                'ID' => 250218000101,
                'WorkAllocation' => '2502180001',
                'TransDate' => '2025-02-25',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250318000101,
                'WorkAllocation' => '2503180001',
                'TransDate' => '2025-03-08',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250318000301,
                'WorkAllocation' => '2503180003',
                'TransDate' => '2025-03-22',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250518002001,
                'WorkAllocation' => '2505180020',
                'TransDate' => '2025-05-31',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250618000401,
                'WorkAllocation' => '2506180004',
                'TransDate' => '2025-06-12',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
        ];

        foreach ($completions as $row) {
            DB::table('workcompletion')->updateOrInsert(
                ['ID' => $row['ID']],
                $row
            );
        }
    }
}
