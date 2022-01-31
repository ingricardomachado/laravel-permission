<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function showRegistrationForm()
    {
        $countries= Country::orderBy('name')->pluck('name','id');
        return view('auth.register')->with('countries', $countries);
    }    
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'cell' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {        
        //Paso1: Se crea un usuario SUB (Suscriptor con rol de Administrador)
        $user = new User();
        $user->PIN=rand(1000,9999);
        $user->name=$data['first_name'].' '.$data['last_name'];
        $user->email=$data['email'];
        $user->role_old='ADM';
        $user->active=1;
        $user->password=Hash::make($data['password']);
        $user->save();        
        //Paso2: Se crea el Subscriptor
        $subscriber = new Subscriber();
        $subscriber->number =Subscriber::max('number')+1;
        $subscriber->user_id = $user->id;
        $subscriber->first_name =$data['first_name'];
        $subscriber->last_name =$data['last_name'];
        $subscriber->full_name =$subscriber->first_name.' '.$subscriber->last_name;
        $subscriber->email =$data['email'];
        $subscriber->name =$data['name'];
        $subscriber->bussines_name =$data['name'];
        $subscriber->country_id =$data['country'];
        if(Timezone::where('country_id', $subscriber->country_id)->where('default',true)->exists()){
        $default_timezone=Timezone::where('country_id', $subscriber->country_id)->where('default',true)->first();
        $subscriber->timezone_id=$default_timezone->id;
        }else{
        $subscriber->timezone_id=20; //20 Mexico_City
        }
        $subscriber->cell =$data['cell'];
        $subscriber->demo=true;
        $subscriber->remaining_days=Setting::first()->demo_days;
        $subscriber->save();
        $this->create_roles($subscriber->id);
        $user->subscriber_id=$subscriber->id;        
        $user->save();
        $user->assignRole('ADM'.$subscriber->id);
        //Seteo de session
        $setting = Setting::first();
        Session::put('role', $user->role);
        Session::put('company_name', $setting->company);
        Session::put('subscriber', $subscriber);
        return $user;
    }

    public function create_roles($subscriber_id){
        
        /*
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
            CLI Cliente
        */
                    
            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'ADM'.$subscriber_id, 
                'aliase' => 'ADM',
                'description' => 'Administrador'
            ]);
            
            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'AOP'.$subscriber_id, 
                'aliase' => 'AOP',
                'description' => 'Administrador Operativo'
            ]);
            
            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'AXA'.$subscriber_id, 
                'aliase' => 'AXA',
                'description' => 'Auxiliar Administrativo'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'CON'.$subscriber_id, 
                'aliase' => 'CON',
                'description' => 'Contador'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'COM'.$subscriber_id, 
                'aliase' => 'COM',
                'description' => 'Comprador'
            ]);
            
            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'ALM'.$subscriber_id, 
                'aliase' => 'ALM',
                'description' => 'Almacenista'
            ]);
            
            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'VEN'.$subscriber_id, 
                'aliase' => 'VEN',
                'description' => 'Vendedor'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'VEF'.$subscriber_id, 
                'aliase' => 'VEF',
                'description' => 'Vendedor Full'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'MEN'.$subscriber_id, 
                'aliase' => 'MEN',
                'description' => 'Mensajero'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'TEC'.$subscriber_id, 
                'aliase' => 'TEC',
                'description' => 'Técnico'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'TEF'.$subscriber_id, 
                'aliase' => 'TEF',
                'description' => 'Técnico Full'
            ]);

            Role::create([
                'subscriber_id' => $subscriber_id, 
                'name' => 'CLI'.$subscriber_id, 
                'aliase' => 'CLI',
                'description' => 'Cliente'
            ]);

        }
}
