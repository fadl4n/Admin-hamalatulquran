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
                'icon' => 'fas fa-user',
                'sequence' => 1
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
                'group_menu_id' => 1,
                'name' => 'Data Santri',
                'url' => '/santri',
                'sequence' => 3
            ],
            [
                'group_menu_id' => 1,
                'name' => 'Data Kelas',
                'url' => '/kelas',
                'sequence' => 4
            ],
            [
                'group_menu_id' => 1,
                'name' => 'Data Pengajar',
                'url' => '/pengajar',
                'sequence' => 5
            ],
            [
                'group_menu_id' => 1,
                'name' => 'Data Keluarga',
                'url' => '/keluarga',
                'sequence' => 6
            ],
            [
                'group_menu_id' => 1,
                'name' => 'Data Surat',
                'url' => '/surat',
                'sequence' => 7
            ],
           

        ]);
    }
}
