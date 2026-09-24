<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'Id_employee' => 1942,
                'entry_date' => '2025-06-20 14:33:37',
                'nama' => 'Ahmad Husaini',
                'rank' => 'Operator',
                'gender' => 'L',
            ],
            [
                'Id_employee' => 1943,
                'entry_date' => '2025-06-20 14:33:40',
                'nama' => 'Maya Sofa Nata',
                'rank' => 'Operator',
                'gender' => 'P',
            ],
            [
                'Id_employee' => 1944,
                'entry_date' => '2025-06-20 14:33:43',
                'nama' => 'Rizal Ardiyansah Bintoro',
                'rank' => 'Operator',
                'gender' => 'L',
            ],
        ];

        foreach ($employees as $emp) {
            DB::table('employee')->updateOrInsert(
                ['Id_employee' => $emp['Id_employee']],
                $emp
            );
        }
    }
}
