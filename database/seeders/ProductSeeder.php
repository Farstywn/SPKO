<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'Id_product' => 128409,
                'sub_category' => 'CALP1',
                'serial_no' => 10043,
                'description' => 'Cincin Anak Laki Putus 1 gr-10043-20K',
                'carat' => '20K - 875',
            ],
            [
                'Id_product' => 767072,
                'sub_category' => 'LT1',
                'serial_no' => 10237,
                'description' => 'Liontin 1 gr-10237-17K',
                'carat' => '17K - 750',
            ],
            [
                'Id_product' => 772839,
                'sub_category' => 'CWTMT',
                'serial_no' => 10152,
                'description' => 'Cincin Wanita Mata-10152-16K',
                'carat' => '16K - 700',
            ],
            [
                'Id_product' => 772893,
                'sub_category' => 'GPMA2',
                'serial_no' => 10125,
                'description' => 'Gelang Pipa Mata Anak 2 gr-10125',
                'carat' => '08K - 375',
            ],
            [
                'Id_product' => 877501,
                'sub_category' => 'KCCBM2',
                'serial_no' => 10047,
                'description' => 'Kepala Cor Cincin Bangkok Metro 2 gr-10047',
                'carat' => '20K - 875',
            ],
        ];

        foreach ($products as $prod) {
            DB::table('product')->updateOrInsert(
                ['Id_product' => $prod['Id_product']],
                $prod
            );
        }
    }
}
