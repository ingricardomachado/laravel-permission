<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Project;
use App\Models\SupplierContact;
use App\Models\Setting;
use App\Models\Supplier;
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

class SupplierContactController extends Controller
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
     * Display the specified supplier.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($supplier_id, $id)
    {        
        $supplier=Supplier::findOrfail($supplier_id);
        if($id==0){
            $contact = new SupplierContact();
        }else{
            $contact = SupplierContact::findOrfail($id);
        }
        
        return view('suppliers.contact')->with('supplier', $supplier)
                        ->with('contact', $contact);
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
            $contact = new SupplierContact();        
            $contact->supplier_id=$request->supplier_id;
            $contact->name=$request->name;
            $contact->position=$request->position;
            $contact->phone=$request->phone;
            $contact->email=$request->email;
            if($request->main){
                $supplier=Supplier::findOrfail($request->supplier_id);
                $supplier->contacts()->update(['main' => false]);
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
            $contact = SupplierContact::findOrfail($id);        
            $contact->name=$request->name;
            $contact->position=$request->position;
            $contact->phone=$request->phone;
            $contact->email=$request->email;
            if($request->main){
                $supplier=Supplier::findOrfail($request->supplier_id);
                $supplier->contacts()->update(['main' => false]);
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
            $contact = SupplierContact::find($id);
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
