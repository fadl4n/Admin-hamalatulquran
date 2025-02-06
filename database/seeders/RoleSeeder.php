<?php

namespace Database\Seeders;

use App\Models\Priviledge;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Role::all()->count() == 0) {
            $roles = [
                [
                    'name' => 'Super Admin',
                    'description' => 'This is superadmin user',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Admin',
                    'description' => 'This is admin user',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            Role::insert($roles);
        }
    }
}
