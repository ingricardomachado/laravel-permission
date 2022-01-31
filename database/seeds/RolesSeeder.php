<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'SAM']);
        Role::create(['name' => 'ADM']);
        Role::create(['name' => 'TEC']);
    }
}
