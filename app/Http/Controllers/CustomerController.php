<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use App\User;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\CustomerContact;
use App\Models\Target;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class CustomerController extends Controller
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
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('customers.index');
    }

    public function datatable()
    {        

        $customers = $this->subscriber->customers();        
        
        return Datatables::of($customers)
            ->addColumn('action', function ($customer) {
                if(session()->get('role')=='ADM'){
                    if($customer->active){
                        $opt_revoke=($customer->user_id)?
                        '<a class="dropdown-item" href="#" onclick="showModalRevoke(`'.$customer->id.'`, `'.$customer->name.'`)"> Revocar acceso al sistema</a>':'';
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalCustomer('.$customer->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" onclick="showModalContact(`'.$customer->id.'`, 0)">Agregar contacto</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$customer->id.')">Desactivar</a>
                                    '.$opt_revoke.'
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$customer->id.'`, `'.$customer->name.'`, `'.$customer->credit_points.'`, `'.$customer->debit_points.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$customer->id.')"> Activar</a>
                                </div>
                            </div>';
                    }
                }else{
                    return "";
                }
                })           
            ->editColumn('number', function ($customer) {                    
                    return '<b>'.$customer->number.'</b>';
                })
            ->editColumn('name', function ($customer) {                    
                    if($customer->type=='S'){
                        return '<a href="#"  onclick="showModalCustomer('.$customer->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$customer->name.'</a><br><small><i>Suc. '.$customer->parent->name.'</i></small>';
                    }else{
                        return '<a href="#"  onclick="showModalCustomer('.$customer->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$customer->name.'</a>';
                    }
                })
            ->addColumn('contacts', function ($customer) {                    
                    return ($customer->main_contact)?$customer->main_contact->occupation.' '.$customer->main_contact->name:'';
                    /*$all_contacts="";
                    foreach ($customer->contacts()->orderBy('name')->get() as $contact) {
                        if($contact->main){
                            $all_contacts .= '<div class="text-left"><a href="#" style="color:inherit" onclick="showModalContact(`'.$contact->customer_id.'`, `'.$contact->id.'`)" title="Click para editar contacto principal"><b>'.$contact->occupation.' '.$contact->name.'</b></a></div>';

                        }else{
                            $all_contacts .= '<div class="text-left"><a href="#" style="color:inherit" onclick="showModalContact(`'.$contact->customer_id.'`, `'.$contact->id.'`)" title="Click para editar">'.$contact->occupation.' '.$contact->name.'</a></div>';
                        }
                    }
                    return $all_contacts;*/
                })
            ->addColumn('files', function ($customer) {                    
                    $all_files="";
                    foreach ($customer->documents()->get() as $document) {
                        $all_files .= '<div class="text-center"><a href="'.route('customer_documents.download', $document->id).'" title="'.$document->file_name.'"><i class="fas fa-cloud-download-alt"></i></a> <a href="#" title="Eliminar" onclick="showModalDeleteDocument(`'.$document->id.'`, `'.$document->file_name.'`)"><i class="far fa-trash-alt"></i></a></div>';
                    }
                    return $all_files;
                })
            ->editColumn('status', function ($customer) {                    
                    return $customer->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'contacts', 'files', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries= Country::orderBy('name')->pluck('name','id');
        
        $targets= Target::where('active',true)
                        ->orderBy('name')->pluck('name','id');

        if($id==0){
            $customer = new Customer();
            $states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');
            $shipping_states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');
            $parents=$this->subscriber->customers()
                            ->where('type','P')->orderBy('name')
                            ->pluck('name','id');

            $array_names=[];
            $array_occupations=[];
            $array_positions=[];
            $array_phones=[];
            $array_emails=[];
            $array_mains=[];
        }else{
            $customer = Customer::find($id);
            $states= State::where('country_id', $customer->country_id)->orderBy('name')->pluck('name','id');
            $shipping_states= State::where('country_id', $customer->shipping_country_id)->orderBy('name')->pluck('name','id');
            $parents=$this->subscriber->customers()
                            ->where('type','P')->whereNotIn('id', [$customer->id])
                            ->orderBy('name')->pluck('name','id');

            $array_names=$customer->contacts()->pluck('name');
            $array_occupations=$customer->contacts()->pluck('occupation');
            $array_positions=$customer->contacts()->pluck('position');
            $array_phones=$customer->contacts()->pluck('phone');
            $array_emails=$customer->contacts()->pluck('email');
            $array_mains=$customer->contacts()->pluck('main');
        }
        
        return view('customers.save')->with('subscriber', $this->subscriber)
                                ->with('customer', $customer)
                                ->with('parents', $parents)
                                ->with('targets', $targets)
                                ->with('countries', $countries)
                                ->with('states', $states)
                                ->with('shipping_states', $shipping_states)
                                ->with('array_names', json_encode($array_names))
                                ->with('array_occupations', json_encode($array_occupations))
                                ->with('array_positions', json_encode($array_positions))
                                ->with('array_phones', json_encode($array_phones))
                                ->with('array_emails', json_encode($array_emails))
                                ->with('array_mains', json_encode($array_mains));

    }

    public function load_contacts(Request $request)
    {
        if($request->array_names){
                        
            return view('customers.contacts')
                        ->with('array_names', $request->array_names)
                        ->with('array_occupations', $request->array_occupations)
                        ->with('array_positions', $request->array_positions)
                        ->with('array_phones', $request->array_phones)
                        ->with('array_emails', $request->array_emails)
                        ->with('array_mains', $request->array_mains);
        }else{
            return "";
        }
    }
    
    /**
     * Store a newly created customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        try {
            /*if($request->create_user){
                $user= new User();
                $user->PIN=rand(1000,9999);
                $user->subscriber_id=$request->subscriber_id;
                $user->name= $request->name;
                $user->email= $request->email;
                $user->password= password_hash($request->password, PASSWORD_DEFAULT);
                $user->email_notification=0;
                $user->save();
                $user->assignRole('CLI'.$user->subscriber_id);
            }*/            
            $customer = new Customer();
            $customer->number=Customer::where('subscriber_id', $request->subscriber_id)->max('number')+1;
            $customer->type=($request->sucursal)?'S':'P'; //P=Principal S=Sucursal
            $customer->parent_id=($customer->type=='S')?$request->parent:null;
            $customer->subscriber_id=$request->subscriber_id;
            //$customer->user_id=($request->create_user)?$user->id:null;
            $customer->name=$request->name;
            $customer->target_id=$request->target;
            $customer->country_id=$request->country;
            $customer->state_id=$request->state;
            $customer->city=$request->city;
            $customer->address=$request->address;
            $customer->email=($request->email)?$request->email:null;
            $customer->phone=($request->phone)?$request->phone:null;
            $customer->rfc=($request->rfc)?$request->rfc:null;
            $customer->bussines_name=($request->bussines_name)?$request->bussines_name:null;
            $customer->street=($request->street)?$request->street:null;
            $customer->street_number=($request->street_number)?$request->street_number:null;
            $customer->neighborhood=($request->neighborhood)?$request->neighborhood:null;
            $customer->zipcode=($request->zipcode)?$request->zipcode:null;
            $customer->urls=($request->urls)?$request->urls:null;
            $customer->bussines_address=($request->bussines_address)?$request->bussines_address:null;
            $customer->shipping_street=($request->shipping_street)?$request->shipping_street:null;
            $customer->shipping_number=($request->shipping_number)?$request->shipping_number:null;
            $customer->shipping_neighborhood=($request->shipping_neighborhood)?$request->shipping_neighborhood:null;
            $customer->shipping_zipcode=($request->shipping_zipcode)?$request->shipping_zipcode:null;
            $customer->shipping_city=($request->shipping_city)?$request->shipping_city:null;
            $customer->shipping_country_id=$request->shipping_country;
            $customer->shipping_state_id=$request->shipping_state;            
            $customer->discount=($request->discount)?$request->discount:null;
            $customer->notes=($request->notes)?$request->notes:null;
            $customer->save();
            $this->insert_contacts($customer, $request);
            if($request->hasfile('filenames')){
                foreach($request->file('filenames') as $file){
                    $document = new CustomerDocument();
                    $document->customer_id=$customer->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($customer->subscriber_id.'/customers/', $file);
                    $document->save();
                }
            }

            return response()->json([
                    'success' => true,
                    'message' => 'Cliente registrado exitosamente',
                    'customer' => $customer
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    function insert_contacts($customer, $request){
        if($request->contacts){
            //mobile
            $array_contacts=(gettype($request->contacts)=="string")?json_decode($request->contacts, true):$request->contacts;
            for ($i=0; $i < count($array_contacts) ; $i++) { 
                $array_names[]=$array_contacts[$i]['name'];
                $array_occupations[]=$array_contacts[$i]['occupation'];
                $array_positions[]=$array_contacts[$i]['position'];
                $array_phones[]=$array_contacts[$i]['phone'];
                $array_emails[]=$array_contacts[$i]['email'];
                $array_mains[]=$array_contacts[$i]['main'];
            }
        }else{
            //web
            $array_names=$request->names;
            $array_occupations=$request->occupations;
            $array_positions=$request->positions;
            $array_phones=$request->phones;
            $array_emails=$request->emails;
            $array_mains=$request->mains;                
        }
        
        for ($i=0; $i < count($array_names) ; $i++) { 
            $contact=new CustomerContact();
            $contact->customer_id=$customer->id;
            $contact->name=$array_names[$i];
            $contact->occupation=$array_occupations[$i];
            $contact->position=$array_positions[$i];
            $contact->phone=$array_phones[$i];
            $contact->email=$array_emails[$i];
            $contact->main=$array_mains[$i];
            $contact->save();
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
        $customer = Customer::find($id);
        
        $array_emails=$customer->contacts()
                        ->whereNotNull('email')
                        ->orderBy('main','desc')->pluck('email');

        $customer['array_emails']=$array_emails;
        
        if($customer){
            return response()->json([
                    'success' => true,
                    'customer' => $customer
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        try {
            $customer = Customer::find($id);
            //Principal a Sucursal
            if($request->sucursal){
                $parent=Customer::find($request->parent);
                if($customer->user_id){
                  $user_old=User::find($customer->user_id);
                  $customer->user_id=null;
                  $customer->save();
                  $user_old->delete();
                }
                $customer->type='S';
                $customer->parent_id=$parent->id;
                $customer->user_id=$parent->user_id;
                $customer->save();
            }          
            //Sucursal a Principal
            if($request->change_parent){
                $customer->type='P';
                $customer->parent_id=null;
                $customer->user_id=null;
            }
            
            $customer->name=$request->name;
            $customer->target_id=$request->target;
            $customer->country_id=$request->country;
            $customer->state_id=$request->state;
            $customer->city=$request->city;
            $customer->address=$request->address;
            $customer->email=($request->email)?$request->email:null;
            $customer->phone=($request->phone)?$request->phone:null;
            $customer->rfc=($request->rfc)?$request->rfc:null;
            $customer->bussines_name=($request->bussines_name)?$request->bussines_name:null;
            $customer->street=($request->street)?$request->street:null;
            $customer->street_number=($request->street_number)?$request->street_number:null;
            $customer->zipcode=($request->zipcode)?$request->zipcode:null;
            $customer->neighborhood=($request->neighborhood)?$request->neighborhood:null;
            $customer->urls=($request->urls)?$request->urls:null;
            $customer->bussines_address=($request->bussines_address)?$request->bussines_address:null;
            $customer->shipping_street=($request->shipping_street)?$request->shipping_street:null;
            $customer->shipping_number=($request->shipping_number)?$request->shipping_number:null;
            $customer->shipping_neighborhood=($request->shipping_neighborhood)?$request->shipping_neighborhood:null;
            $customer->shipping_zipcode=($request->shipping_zipcode)?$request->shipping_zipcode:null;
            $customer->shipping_city=($request->shipping_city)?$request->shipping_city:null;
            $customer->shipping_country_id=$request->shipping_country;
            $customer->shipping_state_id=$request->shipping_state;
            $customer->discount=($request->discount)?$request->discount:null;
            $customer->notes=($request->notes)?$request->notes:null;
            if($customer->user_id){
                $customer->user->name=$customer->name;
                $customer->user->email=$customer->email;
                if($request->change_password){
                    $customer->user->password=password_hash($request->password, PASSWORD_DEFAULT);
                }
                $customer->user->save();
            }
            /*if($request->create_user){
                $user= new User();
                $user->PIN=rand(1000,9999);
                $user->subscriber_id=$customer->subscriber_id;
                $user->name= $request->name;
                $user->email= $request->email;
                $user->password= password_hash($request->password, PASSWORD_DEFAULT);
                $user->email_notification=0;
                $user->save();
                $user->assignRole('CLI'.$user->subscriber_id);
            }*/
            $customer->save();
            $customer->contacts()->delete();
            $this->insert_contacts($customer, $request);

            if($request->hasfile('filenames')){
                foreach($request->file('filenames') as $file){
                    $document = new CustomerDocument();
                    $document->customer_id=$customer->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($customer->subscriber_id.'/customers/', $file);
                    $document->save();
                }
            }            

            return response()->json([
                    'success' => true,
                    'message' => 'Cliente actualizado exitosamente',
                    'customer' => $customer
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);
            foreach($customer->documents()->get() as $document){
                Storage::delete($document->customer->subscriber_id.'/customers/'.$document->file);
                Storage::delete($document->customer->subscriber_id.'/customers/thumbs/'.$document->file);
            }
            if($customer->user_id){
                $user=User::find($customer->user_id);
                $user->roles()->detach();
                $user-> forgetCachedPermissions();
                $user->delete();                
            }
            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function revoke($id)
    {
        try {
            $customer = Customer::find($id);
            $user=User::find($customer->user_id);
            $customer->user_id=null;
            $customer->save();
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Acceso revocado exitosamente'
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
            $customer = Customer::find($id);
            if($customer->active){
                $customer->active=false;
                $customer->user->active=false;    
            }else{
                $customer->active=true;
                $customer->user->active=true;
            }
            $customer->user->save();
            $customer->save();

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
    
    public function rpt_customers()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $customers=$this->subscriber->customers()->orderBy('name')->get();

        $data=[
            'company' => $company,
            'customers' => $customers,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_customers', $data);
        
        return $pdf->stream('Clientes.pdf');
    }

    public function xls_customers(Request $request)
    {        
        return Excel::download(new CustomersExport($this->subscriber), 'Clientes.xlsx');        
    }
    
    public function customers(){
        return Customer::orderBy('name')->get();            
    }

    public function photos($id){
        try {
            $customer = Customer::findOrfail($id);
            $photos=$customer->photos()->get();
            foreach($photos as $photo){
                $photo['url']=url('photo/'.$photo->id);
                $photo['url_thumbnail']=url('photo_thumbnail/'.$photo->id);
            }

            return response()->json([
                    'success' => true,
                    'photos' => $photos,
                ], 200);                        

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }
    }
}
