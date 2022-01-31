<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Project;
use App\Models\CustomerContact;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class CustomerContactController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->subscriber = session()->get('subscriber');
            return $next($request);
        });
    }    
        
    /*public function load($customer_id, $id)
    {        
        $customer=Customer::findOrfail($customer_id);
        if($id==0){
            $contact = new CustomerContact();
        }else{
            $contact = CustomerContact::findOrfail($id);
        }
        
        return view('customers.contact')->with('customer', $customer)
                        ->with('contact', $contact);
    }*/
    
    public function load($customer_id)
    {        
        if($customer_id>0){
            $customer=Customer::findOrfail($customer_id);
            $contacts=$customer->contacts()->orderBy('name')->get();        
            
            return view('customers.contacts')->with('contacts', $contacts);
        }else{
            return "";
        }
    }

    /**
     * Display a listing of the contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        //
    }

    /**
     * Store a newly created contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $contact = new CustomerContact();        
            $contact->customer_id=$request->customer_id;
            $contact->name=$request->name;
            $contact->occupation=$request->occupation;
            $contact->position=$request->position;
            $contact->phone=$request->phone;
            $contact->email=$request->email;
            if($request->main){
                $customer=Customer::findOrfail($request->customer_id);
                $customer->contacts()->update(['main' => false]);
                $contact->main=true;
            }
            $contact->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Contacto registrado exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $contact = CustomerContact::findOrfail($id);        
            $contact->name=$request->name;
            $contact->occupation=$request->occupation;
            $contact->position=$request->position;
            $contact->phone=$request->phone;
            $contact->email=$request->email;
            if($request->main){
                $customer=Customer::findOrfail($request->customer_id);
                $customer->contacts()->update(['main' => false]);
                $contact->main=true;
            }
            $contact->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Contacto registrado exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $contact = CustomerContact::find($id);
            $contact->delete();

            return response()->json([
                    'success' => true,
                    'message' => 'Contacto eliminado exitosamente'
                ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
}
