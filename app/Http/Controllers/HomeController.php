<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        (!session()->has('role'))?session()->put('role', Auth::user()->role):'';        
                
        if(session()->get('role')=='SAM'){
            
            return redirect()->route('subscribers.index'); 
        
        }else if(session()->get('role')=='ADM' || session()->get('role')=='CLI'){
        
            (!session()->has('subscriber'))?session()->put('subscriber', Subscriber::find(Auth::user()->subscriber_id)):'';

            $subscriber = session()->get('subscriber');
            $states=$subscriber->country->states()->orderBy('name')->pluck('name','id');            

            return view('home')->with('subscriber', $subscriber)
                            ->with('states', $states);
        
        }else{
        
            return view('home');
        
        }
    }
}
