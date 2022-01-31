<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Subscriber;
use App\Models\Project;
use App\User;
use DB;
use Carbon\Carbon;


class FixController extends Controller
{

    public function create_roles(){
    	
    	/*
			SAM Super Administrador
			ADM Administrador
			AOP Administrador operativo
			AXA Auxiliar administrativo
			CON Contador
			COM Comprador
			ALM Almacenista
			VEN Vendedor
			VEF Vendedor full
			MEN Mensajero
			TEC Tecnico
			TEF Tecnico full
    	*/

    	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    	Role::truncate();
    	
    	//SAM    	
		Role::create(['name' => 'SAM', 'aliase' => 'SAM', 'description' => 'Super Administrador']);
    	
    	$subscribers=Subscriber::orderBy('id')->get();

    	foreach ($subscribers as $subscriber) {
    		
			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'ADM'.$subscriber->id, 
				'aliase' => 'ADM',
				'description' => 'Administrador'
			]);
			
			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'AOP'.$subscriber->id, 
				'aliase' => 'AOP',
				'description' => 'Administrador Operativo'
			]);
			
			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'AXA'.$subscriber->id, 
				'aliase' => 'AXA',
				'description' => 'Auxiliar Administrativo'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'CON'.$subscriber->id, 
				'aliase' => 'CON',
				'description' => 'Contador'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'COM'.$subscriber->id, 
				'aliase' => 'COM',
				'description' => 'Comprador'
			]);
			
			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'ALM'.$subscriber->id, 
				'aliase' => 'ALM',
				'description' => 'Almacenista'
			]);
			
			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'VEN'.$subscriber->id, 
				'aliase' => 'VEN',
				'description' => 'Vendedor'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'VEF'.$subscriber->id, 
				'aliase' => 'VEF',
				'description' => 'Vendedor Full'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'MEN'.$subscriber->id, 
				'aliase' => 'MEN',
				'description' => 'Mensajero'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'TEC'.$subscriber->id, 
				'aliase' => 'TEC',
				'description' => 'Técnico'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'TEF'.$subscriber->id, 
				'aliase' => 'TEF',
				'description' => 'Técnico Full'
			]);

			Role::create([
				'subscriber_id' => $subscriber->id, 
				'name' => 'CLI'.$subscriber->id, 
				'aliase' => 'CLI',
				'description' => 'Cliente'
			]);

    	}

    	DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    	return "Roles creados exitosamente";
    }


    public function assign_roles(){
    	$users=User::all();

    	foreach($users as $user){
    		if($user->role_old=='SAM'){
				$user->assignRole($user->role_old);
    		}else{
    			$user->assignRole($user->role_old.$user->subscriber_id);
    		}
    	}
    	return "Roles assignados exitosamente";
    }

    public function test_methods($id){
    	$user=User::find($id);

    	return $user->roles->first()->name;
    }

    public function read_projects(){
    	$projects=Project::where('_id','>=',651)->get();
    	foreach($projects as $project){
    		//echo $project->_id.' '.$project->date.' '.Carbon::createFromFormat('Y-m-d H:i:s', $project->date.' 00:00:00', 'America/Monterrey')->timestamp.'<br>';
    		$project->created=Carbon::createFromFormat('Y-m-d H:i:s', $project->date.' 00:00:00', 'America/Monterrey')->timestamp;
    		$project->save();
    	}

    	return "Actualizacion exitosa con todos los registros";
    }


}
