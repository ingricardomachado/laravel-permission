<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function assign_role(){
        /*$users=User::where('role', 'SAM')->get();
        foreach ($users as $user) {
            $user->assignRole('SAM');
        }*/
        $user=User::find(2);
        $user->assignRole('SAM');
        
        return "Role asignado a los super administradores";
    }

    public function sync_permissions(){
        $admin = Role::where('name', 'ADM')->first();
        
        $permissions=[
            'categories.index',
            'categories.edit',
            'categories.show',
            'categories.create',
            'categories.destroy',

            'customers.index',
            'customers.edit',
            'customers.show',
            'customers.create',
            'customers.destroy',
            
            'employees.index',
            'employees.edit',
            'employees.show',
            'employees.create',
            'employees.destroy',

            'products.index',
            'products.edit',
            'products.show',
            'products.create',
            'products.destroy',

            'services.index',
            'services.edit',
            'services.show',
            'services.create',
            'services.destroy'
        ];
        
        $admin->syncPermissions($permissions);

        return "Permisos asignados";
    }
}
