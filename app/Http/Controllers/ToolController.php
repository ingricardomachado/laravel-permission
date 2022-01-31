<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\State;
use App\Models\Product;
use App\Models\Service;
use App\User;
use Session;
use DB;


class ToolController extends Controller
{    
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->subscriber = session()->get('subscriber');
            return $next($request);
        });    
    }

    public function format_ymd($date_dmy)
    {
        $date_ymd = substr($date_dmy, 6, 4).'-'.substr($date_dmy, 3, 2).'-'.substr($date_dmy, 0, 2);
        return $date_ymd;
    }
    
    public function verify_email(Request $request){
        $email = $request->email;
        if(User::where('email',$email)->exists()){
            return response()->json(array("exists" => true));
        }else{
            return response()->json(array("exists" => false));
        }
    }   
    
    /**
     * Simple way to generate a random password in PHP.
     *
     * @return \Illuminate\Http\Response
     */
    public function random_numbers($length)
    {        
        $chars = "0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
    
        return $password;
    }
    
    public function states($id)
    {
        $country=Country::find($id);
        $states=$country->states()->select('id', 'name')->get();
        return response()->json($states);
    }    

    public function products_services(Request $request)
    {
        
        $products=Product::where('active',true)
                            ->where('subscriber_id', $this->subscriber->id)
                            ->where('active',true)
                            ->where('name', 'LIKE', '%'.$request->q.'%')
                            ->orderBy('name')
                            ->select('id', DB::raw("'P' as type"), 'code', 'name', 'price', 'stock')->get();
                
        $services=Service::where('active',true)
                            ->where('subscriber_id', $this->subscriber->id)
                            ->where('active',true)
                            ->where('name', 'LIKE', '%'.$request->q.'%')
                            ->orderBy('name')
                            ->select('id', DB::raw("'S' as type"), 'code', 'name', 'price', DB::raw("'NA' as stock"))->get();

        $products_services=$products->merge($services);

        /*$products_services->prepend(['id'=> 0, 'type' => 'S', 'name' => '** Nuevo Servicio **', 'price'=> 0, 'stock'=>0]);*/      

        $products_services->prepend(['id'=> 0, 'type' => 'P', 'name' => '** Nuevo Producto o Servicio **', 'price'=> 0, 'stock'=>0]);



        return response()->json($products_services);
    }    

    public function test_request(Request $request)
    {
        try {
                return response()->json($request->all()
            );
        
        } catch (Exception $e) {
            
        }
    }

    public function test_var_dump(Request $request)
    {
        try {
            
            return var_dump($request->all());
        
        } catch (Exception $e) {
            
        }
    }

}
