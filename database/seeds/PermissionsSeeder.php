<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Categories
        Permission::create(['name' => 'categories.index']);
        Permission::create(['name' => 'categories.create']);
        Permission::create(['name' => 'categories.show']);
        Permission::create(['name' => 'categories.edit']);
        Permission::create(['name' => 'categories.destroy']);

        //Customers
        Permission::create(['name' => 'customers.index']);
        Permission::create(['name' => 'customers.create']);
        Permission::create(['name' => 'customers.show']);
        Permission::create(['name' => 'customers.edit']);
        Permission::create(['name' => 'customers.destroy']);

        //Employees
        Permission::create(['name' => 'employees.index']);
        Permission::create(['name' => 'employees.create']);
        Permission::create(['name' => 'employees.show']);
        Permission::create(['name' => 'employees.edit']);
        Permission::create(['name' => 'employees.destroy']);

        //Products
        Permission::create(['name' => 'products.index']);
        Permission::create(['name' => 'products.create']);
        Permission::create(['name' => 'products.show']);
        Permission::create(['name' => 'products.edit']);
        Permission::create(['name' => 'products.destroy']);

        //Servicios
        Permission::create(['name' => 'services.index']);
        Permission::create(['name' => 'services.create']);
        Permission::create(['name' => 'services.show']);
        Permission::create(['name' => 'services.edit']);
        Permission::create(['name' => 'services.destroy']);

        //Subscribers
        Permission::create(['name' => 'subscribers.index']);
        Permission::create(['name' => 'subscribers.create']);
        Permission::create(['name' => 'subscribers.show']);
        Permission::create(['name' => 'subscribers.edit']);
        Permission::create(['name' => 'subscribers.destroy']);
    }
}
