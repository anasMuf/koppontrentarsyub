<?php
namespace App\Helpers;

class Bulan
{
    public static function list(){
        return [
            [
                'bulan_angka' => '01',
                'bulan_nama' => 'Januari'
            ],
            [
                'bulan_angka' => '02',
                'bulan_nama' => 'Februari'
            ],
            [
                'bulan_angka' => '03',
                'bulan_nama' => 'Maret'
            ],
            [
                'bulan_angka' => '04',
                'bulan_nama' => 'April'
            ],
            [
                'bulan_angka' => '05',
                'bulan_nama' => 'Mei'
            ],
            [
                'bulan_angka' => '06',
                'bulan_nama' => 'Juni'
            ],
            [
                'bulan_angka' => '07',
                'bulan_nama' => 'Juli'
            ],
            [
                'bulan_angka' => '08',
                'bulan_nama' => 'Agustus'
            ],
            [
                'bulan_angka' => '09',
                'bulan_nama' => 'September'
            ],
            [
                'bulan_angka' => '10',
                'bulan_nama' => 'Oktober'
            ],
            [
                'bulan_angka' => '11',
                'bulan_nama' => 'November'
            ],
            [
                'bulan_angka' => '12',
                'bulan_nama' => 'Desember'
            ],
        ];
    }

    public static function filterByJoinDate($bulan_bergabung){
        return array_filter(self::list(), function($item) use ($bulan_bergabung){
            return $item['bulan_angka'] >= (int)$bulan_bergabung;
        });
    }

    public static function filtered($bulan){
        return array_filter(self::list(), function($item) use ($bulan){
            return $item['bulan_angka'] == (int)$bulan;
        });
    }
}
