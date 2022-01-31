<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\SubscriberRequest;
use Auth;
use Illuminate\Support\Facades\Crypt;
use Session;
use App\Models\Subscriber;
use App\Models\Setting;
use App\Models\Center;
use App\Models\Timezone;
use App\Models\Country;
use App\Models\State;
use App\User;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ImgController;
use Storage;
use Image;
use File;
use DB;
use PDF;
use Mail;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('subscribers.index');
    }

    public function index_demo()
    {        
        return view('subscribers.demo');
    }

    public function datatable(Request $request)
    {        
        $demo=$request->demo;

        $subscribers = Subscriber::where('demo', $demo)->get();

        return Datatables::of($subscribers)
            ->addColumn('action', function ($subscriber) {
                    if($subscriber->active){
                        return 
                            '<div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalDemo('.$subscriber->id.')">Demo</a>
                                    <a class="dropdown-item" href="'.route('subscribers.manage', $subscriber->id).'">Administrar</a>
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalSubscriber('.$subscriber->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$subscriber->id.')">Desactivar</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$subscriber->id.'`, `'.$subscriber->name.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return 
                            '<div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$subscriber->id.')">Activar</a>
                                </div>
                            </div>';
                    }
                })           
            ->editColumn('number', function ($subscriber) {                    
                    return '<b>'.$subscriber->number.'</b>';
                })
            ->editColumn('name', function ($subscriber) {                    
                    return '<a href="#"  onclick="showModalSubscriber('.$subscriber->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$subscriber->name.'</b></a>'.'<br><small>PIN '.$subscriber->user->PIN.'<br>'.$subscriber->created_at->format('d/m/Y').'</small>';
                })
            ->editColumn('contact', function ($subscriber) {                    
                    return $subscriber->full_name.'<br><small><i>'.$subscriber->email.'<br>'.$subscriber->cell.'</i></small>';
                })
            ->addColumn('customers', function ($subscriber) {                    
                    return $subscriber->customers()->count();
                })
            ->addColumn('assets', function ($subscriber) {                    
                    return "";
                    //return $subscriber->assets()->count();
                })           
            ->editColumn('status', function ($subscriber) {                    
                    return $subscriber->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'contact', 'status'])
            ->make(true);
    }
    
    public function datatable_demo(Request $request)
    {        
        $subscribers = Subscriber::get();

        return Datatables::of($subscribers)
            ->addColumn('action', function ($subscriber) {
                    if($subscriber->active){
                        return 
                            '<div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.route('subscribers.manage', $subscriber->id).'">Administrar</a>
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalSubscriber('.$subscriber->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$subscriber->id.')">Desactivar</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$subscriber->id.'`, `'.$subscriber->name.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return '<div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$subscriber->id.')">Activar</a>
                            </div>
                        </div>';
                    }
                })           
            ->editColumn('number', function ($subscriber) {                    
                    return '<b>'.$subscriber->number.'</b>';
                })
            ->editColumn('name', function ($subscriber) {                    
                    return $subscriber->name.'<br><small><i>'.$subscriber->email.'<br>'.$subscriber->cell.'</i></small>';
                })
            ->addColumn('customers', function ($subscriber) {                    
                    return $subscriber->customers()->count();
                })
            ->addColumn('assets', function ($subscriber) {                    
                    return "";
                    //return $subscriber->assets()->count();
                })           
            ->editColumn('status', function ($subscriber) {                    
                    return $subscriber->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'address', 'name', 'status'])
            ->make(true);
    }

    /**
     * Display the specified subscriber.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries= Country::orderBy('name')->pluck('name','id');

        if($id==0){
            $subscriber = new Subscriber();
            $states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');

        }else{
            $subscriber = Subscriber::find($id);
            $states= State::where('country_id', $subscriber->country_id)->orderBy('name')->pluck('name','id');
        }
        
        return view('subscribers.save')->with('subscriber', $subscriber)
                                    ->with('countries', $countries)
                                    ->with('states', $states);
    }

    /**
     * Store a newly created subscriber in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriberRequest $request)
    {
        try {
            //se crea el usuario
            $user = new User();
            $user->PIN=rand(1000,9999);
            $user->name=$request->first_name.' '.$request->last_name;
            $user->email=$request->email;
            $user->role='ADM';
            $user->password= password_hash($request->password, PASSWORD_DEFAULT);
            $user->save();
            //se crea el suscriptor
            $subscriber = new Subscriber();
            $subscriber->number=Subscriber::max('number')+1;        
            $subscriber->user_id=$user->id;
            $subscriber->first_name= $request->first_name;
            $subscriber->last_name= $request->last_name;
            $subscriber->full_name= $subscriber->first_name.' '.$subscriber->last_name;
            $subscriber->email= $request->email;
            $subscriber->name= $request->name;
            $subscriber->bussines_name= $request->bussines_name;
            $subscriber->rfc= $request->rfc;
            $subscriber->country_id= $request->country;
            $subscriber->state_id= $request->state;
            $subscriber->city= $request->city;
            $subscriber->address= $request->address;
            ($request->phone)?$subscriber->phone=$request->phone:'';
            ($request->cell)?$subscriber->cell=$request->cell:'';
            if(Timezone::where('country_id', $subscriber->country_id)->where('default',true)->exists()){
                $default_timezone=Timezone::where('country_id', $subscriber->country_id)->where('default',true)->first();
                $subscriber->timezone_id=$default_timezone->id;
            }else{
                $subscriber->timezone_id=20; //20 Mexico_City
            }
            $subscriber->save();
            $user->subscriber_id=$subscriber->id;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Suscriptor registrado exitosamente',
                'subscriber' => $subscriber
            ], 200);                        
        
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {            
        $subscriber = Subscriber::findOrFail($id);
        
        if($subscriber){
            return response()->json([
                    'success' => true,
                    'subscriber' => $subscriber
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
    
    /**
     * Update the specified subscriber in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriberRequest $request, $id)
    {
            
        $subscriber = Subscriber::find($id);
        $user = User::where('id', $subscriber->user_id)->first();
        //se actualiza el usuario
        $user->name=$request->first_name.' '.$request->last_name;
        $user->email=$request->email;
        ($request->change_password)?$user->password= password_hash($request->password, PASSWORD_DEFAULT):'';
        $user->save();
        //se actualiza el suscriptor
        $subscriber->first_name= $request->first_name;
        $subscriber->last_name= $request->last_name;
        $subscriber->full_name= $subscriber->first_name.' '.$subscriber->last_name;        
        $subscriber->email= $request->email;
        $subscriber->name= $request->name;
        $subscriber->bussines_name= $request->bussines_name;
        $subscriber->rfc= $request->rfc;
        $subscriber->country_id= $request->country;
        $subscriber->state_id= $request->state;
        $subscriber->city= $request->city;
        $subscriber->address= $request->address;
        ($request->phone)?$subscriber->phone=$request->phone:'';
        ($request->cell)?$subscriber->cell=$request->cell:'';
        $subscriber->save();

        return response()->json([
                'success' => true,
                'message' => 'Suscriptor actualizado exitosamente',
                'subscriber' => $subscriber
            ], 200);
    }

    /**
     * Remove the specified owner from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $subscriber = Subscriber::find($id);
            Storage::deleteDirectory($subscriber->id);
            $user=User::find($subscriber->user_id);
            $user->delete();
            $subscriber->delete();

            return response()->json([
                'success' => true,
                'message' => 'Suscriptor eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status to specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        try {
            $subscriber = Subscriber::find($id);
            ($subscriber->active)?$subscriber->active=false:$subscriber->active=true;
            $subscriber->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Estado cambiado exitosamente',
                ], 200);                        

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }
    }

    public function demo(Request $request, $id)
    {
        try {
            $subscriber=Subscriber::find($id);
            $subscriber->demo=$request->demo;
            if($subscriber->demo){
                $subscriber->remaining_days=$request->remaining_days;
                if($request->remaining_days>0){
                    $subscriber->active=true;
                    $subscriber->user->active=true;
                }else{
                    $subscriber->active=false;
                    $subscriber->user->active=false;                
                }
            }else{
                $subscriber->remaining_days=0;
                $subscriber->active=true;
                $subscriber->user->active=true;
            }
            $subscriber->user->save();
            $subscriber->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Proceso realizado exitosamente',
                ], 200);                        

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }
    }
    
    public function full_register(Request $request, $id)
    {
        try {
            $subscriber=Subscriber::find($id);
            $file = $request->logo;        
            if (File::exists($file)){
                $subscriber->logo_name = $file->getClientOriginalName();
                $subscriber->logo_type = $file->getClientOriginalExtension();
                $subscriber->logo=$this->upload_file($subscriber->id, $file);
            }
            $subscriber->state_id=$request->state;
            $subscriber->city=$request->city;
            $subscriber->address = $request->address;
            $subscriber->full_registration=true;
            $subscriber->save();        
            
            return response()->json([
                'success' => true,
                'message' => 'Gracias por completar el registro'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function rpt_subscribers($demo)
    {        
        $setting = Setting::first();
        $subscribers=Subscriber::where('demo', $demo)->orderBy('number')->get();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get($setting->logo)):'';
        
        $company=$setting->company;
        
        $data=[
            'company' => $company,
            'subscribers' => $subscribers,
            'demo' => $demo,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_subscribers', $data);
        
        return $pdf->stream('Suscriptores.pdf');

    }

    public function subscribers(){
        return Subscriber::orderBy('name')->get();
    }


    public function manage($id)
    {
        $subscriber = Subscriber::find($id);
        Session::put('role', 'ADM');
        Session::put('subscriber', $subscriber);

        return redirect()->route('home');
    }

    public function return_sam()
    {
        Session::forget('subscriber');
        Session::put('role', 'SAM');
                
        return redirect()->route('subscribers.index');    
    }

    public function customers($id){
        try {
            $subscriber=Subscriber::findOrfail($id);            
            return response()->json([
                'success' => true,
                'data' => $subscriber->customers()->orderBy('name')->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function products($id){
        try {
            $subscriber=Subscriber::findOrfail($id);            
            return response()->json([
                'success' => true,
                'data' => $subscriber->products()->orderBy('name')->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function services($id){
        try {
            $subscriber=Subscriber::findOrfail($id);            
            return response()->json([
                'success' => true,
                'data' => $subscriber->services()->orderBy('name')->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function suppliers($id){
        try {
            $subscriber=Subscriber::findOrfail($id);            
            return response()->json([
                'success' => true,
                'data' => $subscriber->suppliers()->orderBy('name')->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sales($id){
        try {
            $subscriber=Subscriber::findOrfail($id);            
            return response()->json([
                'success' => true,
                'data' => $subscriber->sales()->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
