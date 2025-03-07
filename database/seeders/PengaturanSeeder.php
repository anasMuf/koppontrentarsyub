<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            [
                'setting' => 'logo',
                'value' => null,
            ],
            [
                'setting' => 'nama',
                'value' => 'Koperasi Tarsyub',
            ],
            [
                'setting' => 'alamat',
                'value' => 'Jl. Raya Kenongo No.1 01, Kenongo, Kec. Tulangan, Kabupaten Sidoarjo, Jawa Timur 61273',
            ],
            [
                'setting' => 'telepon',
                'value' => '081255557734',
            ],
            [
                'setting' => 'email',
                'value' => 'koppontrentarsyub.annafiiyah@gmail.com',
            ]
        ];

        foreach ($datas as $value) {
            Pengaturan::create($value);
        }
    }
}
