<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\Priviledge;
use Illuminate\Database\Seeder;

class PriviledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(Priviledge::all()->count() == 0) {
            $roles = Role::all();
            foreach ($roles as $role) {
                $rows = Menu::all();
                $menus = [];
                foreach ($rows as $menu) {
                    if ($role->id == 1) {
                        $menus[] = [
                            'role_id' => $role->id,
                            'menu_id' => $menu->id,
                            'view' => 1,
                            'add' => 1,
                            'edit' => 1,
                            'delete' => 1,
                            'other' => 1,
                        ];
                    } else {
                        $menus[] = [
                            'role_id' => $role->id,
                            'menu_id' => $menu->id,
                            'view' => 0,
                            'add' => 0,
                            'edit' => 0,
                            'delete' => 0,
                            'other' => 0,
                        ];
                    }
                }
                Priviledge::insert($menus);
            }
        }
    }
}
