<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ContactRequest;
use App\User;
use App\Models\Contact;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactsExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ContactController extends Controller
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
     * Display a listing of the contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('contacts.index');
    }

    public function datatable()
    {        
        $contacts = $this->subscriber->contacts();        
        
        return Datatables::of($contacts)
            ->addColumn('action', function ($contact) {
                if(session()->get('role')=='ADM'){
                    if($contact->active){
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalContact('.$contact->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$contact->id.')">Desactivar</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$contact->id.'`, `'.$contact->name.'`, `'.$contact->credit_points.'`, `'.$contact->debit_points.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$contact->id.')"> Activar</a>
                                </div>
                            </div>';
                    }
                }else{
                    return "";
                }
                })           
            ->editColumn('number', function ($contact) {                    
                    return '<b>'.$contact->number.'</b>';
                })            
            ->editColumn('name', function ($contact) {                    
                    return '<a href="#"  onclick="showModalContact('.$contact->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$contact->full_name.'</b><br><small><i>'.$contact->email.'</i></small></a>';
                })
            ->editColumn('status', function ($contact) {                    
                    return $contact->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'contact', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries= Country::orderBy('name')->pluck('name','id');

        if($id==0){
            $contact = new Contact();
            $states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');
        }else{
            $contact = Contact::findOrfail($id);
            $states= State::where('country_id', $contact->country_id)->orderBy('name')->pluck('name','id');
        }
        
        return view('contacts.save')->with('subscriber', $this->subscriber)
                                ->with('contact', $contact)
                                ->with('countries', $countries)
                                ->with('states', $states);
    }

    /**
     * Store a newly created contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        try {
            $subscriber=Subscriber::findOrFail($request->subscriber_id);
            $contact = new Contact();
            $contact->number=Contact::max('number')+1;
            $contact->subscriber_id=$subscriber->id;
            $contact->first_name=$request->first_name;
            $contact->last_name=$request->last_name;
            $contact->full_name=$contact->first_name.' '.$contact->last_name;
            $contact->type=$request->type;
            $contact->position=$request->position;
            $contact->profession=$request->profession;
            $contact->country_id=$request->country;
            $contact->state_id=$request->state;
            $contact->city=$request->city;
            $contact->street=$request->street;
            $contact->street_number=$request->street_number;
            $contact->email=($request->email)?$request->email:null;
            $contact->cell=($request->cell)?$request->cell:null;
            $contact->phone=($request->phone)?$request->phone:null;
            $contact->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Contacto registrado exitosamente',
                    'contact' => $contact
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
        $contact = Contact::findOrFail($id);
        
        if($contact){
            return response()->json([
                    'success' => true,
                    'contact' => $contact
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);            
            $contact->first_name=$request->first_name;
            $contact->last_name=$request->last_name;
            $contact->full_name=$contact->first_name.' '.$contact->last_name;
            $contact->type=$request->type;
            $contact->position=$request->position;
            $contact->profession=$request->profession;
            $contact->country_id=$request->country;
            $contact->state_id=$request->state;
            $contact->city=$request->city;
            $contact->street=$request->street;
            $contact->street_number=$request->street_number;
            $contact->email=($request->email)?$request->email:null;
            $contact->cell=($request->cell)?$request->cell:null;
            $contact->phone=($request->phone)?$request->phone:null;
            $contact->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Contacto actualizado exitosamente',
                    'contact' => $contact
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
            $contact = Contact::findOrFail($id);
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
            $contact = Contact::findOrFail($id);
            $contact->active=($contact->active)?false:true;
            $contact->save();

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
    
    public function rpt_contacts()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $contacts=$this->subscriber->contacts()->orderBy('full_name')->get();

        $data=[
            'company' => $company,
            'contacts' => $contacts,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_contacts', $data);
        
        return $pdf->stream('Contactos.pdf');
    }

    public function xls_contacts(Request $request)
    {        
        return Excel::download(new ContactsExport($this->subscriber), 'Contactos.xlsx');        
    }
    
    public function contacts(){
        return Contact::orderBy('name')->get();            
    }
}
