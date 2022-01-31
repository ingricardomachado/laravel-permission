<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\User;
use App\Models\Target;
use App\Models\Supplier;
use App\Models\SupplierDocument;
use App\Models\SupplierContact;
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
use App\Exports\SuppliersExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class SupplierController extends Controller
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
     * Display a listing of the supplier.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('suppliers.index');
    }

    public function datatable()
    {        
        $suppliers = $this->subscriber->suppliers();        
        
        return Datatables::of($suppliers)
            ->addColumn('action', function ($supplier) {
                if(session()->get('role')=='ADM'){
                    if($supplier->active){
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalSupplier('.$supplier->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" onclick="showModalContact(`'.$supplier->id.'`, 0)">Agregar contacto</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$supplier->id.')">Desactivar</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$supplier->id.'`, `'.$supplier->name.'`, `'.$supplier->credit_points.'`, `'.$supplier->debit_points.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$supplier->id.')"> Activar</a>
                                </div>
                            </div>';
                    }
                }else{
                    return "";
                }
                })           
            ->editColumn('number', function ($supplier) {                    
                    return '<b>'.$supplier->number.'</b>';
                })            
            ->editColumn('name', function ($supplier) {                    
                    return '<a href="#"  onclick="showModalSupplier('.$supplier->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$supplier->name.'<br><small><i>'.$supplier->target->name.'</i></small></a>';
                })
            ->addColumn('contacts', function ($supplier) {                    
                    return ($supplier->main_contact)?$supplier->main_contact->occupation.' '.$supplier->main_contact->name:'';
                    /*$all_contacts="";
                    foreach ($supplier->contacts()->orderBy('name')->get() as $contact) {
                        if($contact->main){
                            $all_contacts .= '<div class="text-left"><a href="#" style="color:inherit" onclick="showModalContact(`'.$contact->supplier_id.'`, `'.$contact->id.'`)" title="Click para editar contacto principal"><b>'.$contact->name.' ('.$contact->position.')</b></a></div>';

                        }else{
                            $all_contacts .= '<div class="text-left"><a href="#" style="color:inherit" onclick="showModalContact(`'.$contact->supplier_id.'`, `'.$contact->id.'`)" title="Click para editar">'.$contact->name.' ('.$contact->position.')</a></div>';
                        }
                    }
                    return $all_contacts;*/
                })
            ->addColumn('files', function ($supplier) {                    
                    $all_files="";
                    foreach ($supplier->documents()->get() as $document) {
                        $all_files .= '<div class="text-center"><a href="'.route('supplier_documents.download', $document->id).'" title="'.$document->file_name.'"><i class="fas fa-cloud-download-alt"></i></a> <a href="#" title="Eliminar" onclick="showModalDeleteDocument(`'.$document->id.'`, `'.$document->file_name.'`)"><i class="far fa-trash-alt"></i></a></div>';
                    }
                    return $all_files;
                })
            ->editColumn('status', function ($supplier) {                    
                    return $supplier->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'contacts', 'files', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified supplier.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries= Country::orderBy('name')->pluck('name','id');
        
        $targets= Target::where('active',true)
                        ->orderBy('name')->pluck('name','id');
        
        $default=Target::findOrfail(0);
        $targets->prepend($default->name, $default->id);

        
        if($id==0){
            $supplier = new Supplier();
            $states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');
            $parents=$this->subscriber->suppliers()
                            ->orderBy('name')
                            ->pluck('name','id');

        $array_names=[];
        $array_occupations=[];
        $array_positions=[];
        $array_phones=[];
        $array_emails=[];
        $array_mains=[];

        }else{
            $supplier = Supplier::findOrfail($id);
            $states= State::where('country_id', $supplier->country_id)->orderBy('name')->pluck('name','id');
            
            $array_names=$supplier->contacts()->pluck('name');
            $array_occupations=$supplier->contacts()->pluck('occupation');
            $array_positions=$supplier->contacts()->pluck('position');
            $array_phones=$supplier->contacts()->pluck('phone');
            $array_emails=$supplier->contacts()->pluck('email');
            $array_mains=$supplier->contacts()->pluck('main');
        }
        
        return view('suppliers.save')->with('subscriber', $this->subscriber)
                                ->with('supplier', $supplier)
                                ->with('targets', $targets)
                                ->with('countries', $countries)
                                ->with('states', $states)
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
                        
            return view('suppliers.contacts')
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
     * Store a newly created supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        try {
            $subscriber=Subscriber::findOrFail($request->subscriber_id);
            $supplier = new Supplier();
            $supplier->number=Supplier::max('number')+1;
            $supplier->subscriber_id=$subscriber->id;
            $supplier->name=$request->name;
            $supplier->target_id=$request->target;
            $supplier->country_id=$request->country;
            $supplier->state_id=$request->state;
            $supplier->city=$request->city;
            $supplier->address=$request->address;
            $supplier->location=$request->location;
            $supplier->zipcode=($request->zipcode)?$request->zipcode:null;
            $supplier->email=($request->email)?$request->email:null;
            $supplier->phone=($request->phone)?$request->phone:null;
            $supplier->rfc=($request->rfc)?$request->rfc:null;
            $supplier->bussines_name=($request->bussines_name)?$request->bussines_name:null;
            $supplier->urls=($request->urls)?$request->urls:null;
            $supplier->notes=($request->notes)?$request->nots:null;
            $supplier->bank_accounts=($request->bank_accounts)?$request->bank_accounts:null;
            $supplier->bussines_address=($request->bussines_address)?$request->bussines_address:null;
            $supplier->notes=$request->notes;
            $supplier->save();
            $this->insert_contacts($supplier, $request);
            if($request->hasfile('filenames')){
                foreach($request->file('filenames') as $file){
                    $document = new SupplierDocument();
                    $document->supplier_id=$supplier->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($supplier->subscriber_id.'/suppliers/', $file);
                    $document->save();
                }
            }

            return response()->json([
                    'success' => true,
                    'message' => 'Proveedor registrado exitosamente',
                    'supplier' => $supplier
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    function insert_contacts($supplier, $request){
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
            $contact=new SupplierContact();
            $contact->supplier_id=$supplier->id;
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
        $supplier = Supplier::findOrFail($id);
        
        if($supplier){
            return response()->json([
                    'success' => true,
                    'supplier' => $supplier
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);            
            $supplier->name=$request->name;
            $supplier->target_id=$request->target;
            $supplier->country_id=$request->country;
            $supplier->state_id=$request->state;
            $supplier->city=$request->city;
            $supplier->address=$request->address;
            $supplier->location=$request->location;
            $supplier->zipcode=($request->zipcode)?$request->zipcode:null;
            $supplier->email=($request->email)?$request->email:null;
            $supplier->phone=($request->phone)?$request->phone:null;
            $supplier->rfc=($request->rfc)?$request->rfc:null;
            $supplier->bussines_name=($request->bussines_name)?$request->bussines_name:null;
            $supplier->urls=($request->urls)?$request->urls:null;
            $supplier->notes=($request->notes)?$request->nots:null;
            $supplier->bank_accounts=($request->bank_accounts)?$request->bank_accounts:null;
            $supplier->bussines_address=($request->bussines_address)?$request->bussines_address:null;
            $supplier->notes=$request->notes;
            $supplier->save();
            $supplier->contacts()->delete();
            $this->insert_contacts($supplier, $request);

            if($request->hasfile('filenames'))
            {
                foreach($request->file('filenames') as $file)
                {
                    $document = new SupplierDocument();
                    $document->supplier_id=$supplier->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($supplier->subscriber_id.'/suppliers/', $file);
                    $document->save();
                }
            }            

            return response()->json([
                    'success' => true,
                    'message' => 'Proveedor actualizado exitosamente',
                    'supplier' => $supplier
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified supplier from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            foreach($supplier->documents()->get() as $document){
                Storage::delete($document->supplier->subscriber_id.'/suppliers/'.$document->file);
                Storage::delete($document->supplier->subscriber_id.'/suppliers/thumbs/'.$document->file);
            }
            $supplier->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
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
            $supplier = Supplier::findOrFail($id);
            $supplier->active=($supplier->active)?false:true;
            $supplier->save();

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
    
    public function rpt_suppliers()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $suppliers=$this->subscriber->suppliers()->orderBy('name')->get();

        $data=[
            'company' => $company,
            'suppliers' => $suppliers,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_suppliers', $data);
        
        return $pdf->stream('Proveedores.pdf');
    }

    public function xls_suppliers(Request $request)
    {        
        return Excel::download(new SuppliersExport($this->subscriber), 'Proveedores.xlsx');        
    }
    
    public function suppliers(){
        return Supplier::orderBy('name')->get();            
    }
}
