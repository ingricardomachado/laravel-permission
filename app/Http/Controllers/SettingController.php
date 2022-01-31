<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Country;
use App\Models\State;
use App\User;
use Illuminate\Support\Facades\Crypt;
use Session;
//Image
use App\Http\Controllers\ImgController;
use Image;
use File;
use Storage;


class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->subscriber = session()->get('subscriber');
            return $next($request);
        });    
    }    
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $subscriber=Subscriber::find($this->subscriber->id);     
        $countries= Country::orderBy('name')->pluck('name','id');
        $states= State::where('country_id', $subscriber->country_id)->orderBy('name')->pluck('name','id');
        
        return view('settings.index')
                            ->with('countries', $countries)
                            ->with('states', $states)
                            ->with('subscriber', $subscriber);
    }

    public function update(Request $request, $id)
    {
        try {
            
            $subscriber = Subscriber::find($id);
            $user = User::where('id', $subscriber->user_id)->first();
            //se actualiza el usuario
            $user->name=$request->first_name.' '.$request->last_name;
            $user->email=$request->email;
            //se actualiza el suscriptor
            $file = $request->logo;
            if (File::exists($file))
            {        
                ($subscriber->logo)?Storage::delete(''.$subscriber->logo):'';
                $subscriber->logo_name = $file->getClientOriginalName();
                $subscriber->logo_type = $file->getClientOriginalExtension();
                $subscriber->logo=$this->upload_file($subscriber->id, $file);
            }
            $file = $request->stamp;
            if (File::exists($file))
            {        
                ($subscriber->stamp)?Storage::delete(''.$subscriber->stamp):'';
                $subscriber->stamp_name = $file->getClientOriginalName();
                $subscriber->stamp_type = $file->getClientOriginalExtension();
                $subscriber->stamp=$this->upload_file($subscriber->id, $file);
            }
            $subscriber->first_name= $request->first_name;
            $subscriber->last_name= $request->last_name;
            $subscriber->full_name= $request->first_name.' '.$request->last_name;
            $subscriber->email= $request->email;
            $subscriber->name= $request->name;
            $subscriber->bussines_name= $request->bussines_name;
            $subscriber->rfc= $request->rfc;
            $subscriber->country_id= $request->country;
            $subscriber->state_id= $request->state;
            $subscriber->city= $request->city;
            $subscriber->coin= $request->coin;
            $subscriber->coin_name= $request->coin_name;
            $subscriber->address= $request->address;
            ($request->phone)?$subscriber->phone=$request->phone:'';
            ($request->cell)?$subscriber->cell=$request->cell:'';
            $subscriber->full_registration=true;
            $subscriber->save();
            Session::put('subscriber', $subscriber);
            Session::put('coin', $subscriber->coin);
            Session::put('coin_name', $subscriber->coin_name);
            Session::put('money_format', $subscriber->money_format);
            
            return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones actualizadas exitosamente',
                ], 200);                    
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function app()
    {
        $setting = Setting::first();        
        return view('settings.app')->with('setting', $setting);
    }

    public function update_app(Request $request)
    {
        try {
            
            $setting = Setting::first();        
            // Codigo para el logo
            $file = $request->logo;
            if (File::exists($file))
            {        
                ($setting->logo)?Storage::delete(''.$setting->logo):'';
                $setting->logo_name = $file->getClientOriginalName();
                $setting->logo_type = $file->getClientOriginalExtension();
                $setting->logo=$this->upload_file('', $file);
            }        
            $setting->company= $request->input('company');
            $setting->NIT= $request->input('NIT');
            $setting->address= $request->input('address');
            $setting->phone= $request->input('phone');
            $setting->email= $request->input('email');
            $setting->coin= $request->input('coin');
            $setting->money_format= $request->input('money_format');
            $setting->save();        
            $this->set_session_app();


            return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones actualizadas exitosamente',
                ], 200);                    
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }

    }

    public function set_session_app(){
        $setting = Setting::first();
        Session::put('coin', $setting->coin);
        Session::put('money_format', $setting->money_format);
    }
}
