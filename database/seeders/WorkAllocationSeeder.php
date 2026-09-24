<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allocations = [
            [
                'ID' => 250218000101,
                'SW' => '2502180001',
                'TransDate' => '2025-02-25',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250318000101,
                'SW' => '2503180001',
                'TransDate' => '2025-03-05',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250318000301,
                'SW' => '2503180003',
                'TransDate' => '2025-03-20',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250518002001,
                'SW' => '2505180020',
                'TransDate' => '2025-05-28',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
            [
                'ID' => 250618000401,
                'SW' => '2506180004',
                'TransDate' => '2025-06-09',
                'Employee' => 1942,
                'Remarks' => null,
                'Process' => 'Brush',
            ],
        ];

        foreach ($allocations as $row) {
            DB::table('workallocation')->updateOrInsert(
                ['ID' => $row['ID']],
                $row
            );
        }
    }
}
