<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_menus')->insert([
            [
                'name' => 'Master User',
                'icon' => 'fas fa-users',
                'sequence' => 1
            ],
            [
                'name' => 'Data Akademik',
                'icon' => 'fas fa-book',
                'sequence' => 2
            ],
            [
                'name' => 'Laporan Penilaian',
                'icon' => 'fas fa-chart-line',
                'sequence' => 3
            ],
            [
                'name' => 'Manajemen Surat',
                'icon' => 'fas fa-envelope',
                'sequence' => 4
            ],
            [
                'name' => 'Manajemen Artikel',
                'icon' => 'fas fa-newspaper',
                'sequence' => 5
            ]

        ]);

        DB::table('menus')->insert([
            [
                'group_menu_id' => 1,
                'name' => 'Manage Role',
                'url' => '/roles',
                'sequence' => 1
            ],
            [
                'group_menu_id' => 1,
                'name' => 'Manage User',
                'url' => '/users',
                'sequence' => 2
            ],
            [
                'group_menu_id' => 2,
                'name' => 'Data Kelas',
                'url' => '/kelas',
                'sequence' => 3
            ],
            [
                'group_menu_id' => 2,
                'name' => 'Data Santri',
                'url' => '/santri',
                'sequence' => 4
            ],
            [
                'group_menu_id' => 2,
                'name' => 'Data Pengajar',
                'url' => '/pengajar',
                'sequence' => 5
            ],
            [
                'group_menu_id' => 3,
                'name' => 'Target',
                'url' => '/target',
                'sequence' =>  6
            ],
            [
                'group_menu_id' => 3,
                'name' => 'Setoran hafalan',
                'url' => '/setoran',
                'sequence' => 7
            ],
            [
                'group_menu_id' => 3,
                'name' => 'Hisotri',
                'url' => '/histori',
                'sequence' =>  8
            ],
            [
                'group_menu_id' => 3,
                'name' => 'Nilai',
                'url' => '/nilai',
                'sequence' =>  9
            ],
            [
                'group_menu_id' => 4,
                'name' => 'Data Surat',
                'url' => '/surat',
                'sequence' => 11
            ],
            [
                'group_menu_id' => 5,
                'name' => 'Artikel',
                'url' => '/artikel',
                'sequence' => 10
            ],



        ]);
    }
}
