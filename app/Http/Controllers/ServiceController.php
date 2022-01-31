<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ServiceRequest;
use App\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ServiceController extends Controller
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
     * Display a listing of the service.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        $categories= $this->subscriber->categories()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');
        
        return view('services.index')->with('categories', $categories);
    }

    public function datatable(Request $request)
    {        
        $category_filter=$request->category_filter;

        if($category_filter!=''){
            $services = $this->subscriber->services()
                        ->join('categories', 'services.category_id', '=', 'categories.id')
                        ->where('services.category_id', $category_filter)
                        ->select(['services.*', 'categories.name as category']);
        }else{
            $services = $this->subscriber->services()
                        ->join('categories', 'services.category_id', '=', 'categories.id')
                        ->select(['services.*', 'categories.name as category']);
        }

                            ;                
        return Datatables::of($services)
            ->addColumn('action', function ($service) {
                if($service->active){
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalService('.$service->id.')">Editar</a>
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$service->id.')">Desactivar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$service->id.'`, `'.$service->name.'`, `'.$service->credit_points.'`, `'.$service->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                }else{
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$service->id.')"> Activar</a>
                            </div>
                        </div>';
                }
            })           
            ->editColumn('number', function ($service) {                    
                    return '<b>'.$service->number_mask.'</b>';
                })
            ->editColumn('name', function ($service) {                    
                    return '<a href="#"  onclick="showModalService('.$service->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$service->name.'<br><small><i>'.$service->category.'</i></small></a>';
                })
            ->editColumn('status', function ($service) {                    
                    return $service->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $units= Unit::where('active',true)->orderBy('unit')->pluck('name','id');
        $categories= $this->subscriber->categories()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');
        $default=Category::findOrfail(0);
        $categories->prepend($default->name, $default->id);


        if($id==0){
            $service = new Service();
        }else{
            $service = Service::find($id);
        }
        
        return view('services.save')->with('service', $service)
                        ->with('categories', $categories)
                        ->with('units', $units);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request)
    {
        try {
            $service = new Service();
            $service->number=Service::where('subscriber_id', $request->subscriber_id)->max('number')+1;            
            $service->subscriber_id=$request->subscriber_id;
            $service->category_id=$request->category;
            $service->unit_fe=$request->unit_fe;
            $service->code=$request->code;
            $service->code_fe=$request->code_fe;
            $service->name=$request->name;
            $service->description=$request->description;
            $service->price=$request->price;
            $service->field1=$request->field1;
            $service->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Servicio registrado exitosamente',
                    'service' => $service
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
        $service = Service::find($id);
        
        if($service){
            return response()->json([
                    'success' => true,
                    'data' => $service
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequest $request, $id)
    {
        try {
            $service = Service::findOrfail($id);
            $category = Category::findOrfail($request->category);
            $service->category_id=$category->id;
            $service->unit_fe=$request->unit_fe;
            $service->code=$request->code;
            $service->code_fe=$request->code_fe;
            $service->name=$request->name;
            $service->description=$request->description;
            $service->price=$request->price;
            $service->field1=$request->field1;
            $service->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Servicio actualizado exitosamente',
                    'service' => $service
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Servicio eliminado exitosamente'
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
            $service = Service::find($id);
            $service->active=($service->active)?false:true;
            $service->save();

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
    
    public function rpt_services()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;

        $services = $this->subscriber->services()->orderBy('name')->get();
        
        $data=[
            'company' => $company,
            'services' => $services,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_services', $data);
        
        return $pdf->stream('Servicios.pdf');

    }

    public function xls_services(Request $request)
    {        
        return Excel::download(new ServicesExport($this->subscriber), 'Servicios.xlsx');        
    }

    public function services(){
        return Service::orderBy('name')->get();            
    }
}
