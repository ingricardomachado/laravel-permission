<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\TargetRequest;
use App\User;
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
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class TargetController extends Controller
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
     * Display a listing of the target.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('targets.index');
    }

    public function datatable(Request $request)
    {        
        
        $targets = Target::orderBy('name');
        
        return Datatables::of($targets)
            ->addColumn('action', function ($target) {
                if($target->id==0){
                  return "";   
                }
                if($target->active){
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalTarget('.$target->id.')">Editar</a>
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$target->id.')">Desactivar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$target->id.'`, `'.$target->name.'`, `'.$target->credit_points.'`, `'.$target->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                }else{
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$target->id.')"> Activar</a>
                            </div>
                        </div>';
                }
            })           
            ->editColumn('name', function ($target) {                    
                    return '<a href="#"  onclick="showModalTarget('.$target->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$target->name.'</a>';
                })
            ->editColumn('status', function ($target) {                    
                    return $target->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified target.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $target = new Target();
        }else{
            $target = Target::find($id);
        }
        
        return view('targets.save')->with('target', $target);
    }

    /**
     * Store a newly created target in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TargetRequest $request)
    {
        try {
            $target = new Target();
            $target->name=$request->name;
            $target->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Giro registrado exitosamente',
                    'target' => $target
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
        $target = Target::find($id);
        
        if($target){
            return response()->json([
                    'success' => true,
                    'target' => $target
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified target in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TargetRequest $request, $id)
    {
        try {
            $target = Target::find($id);
            $target->name=$request->name;
            $target->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Giro actualizado exitosamente',
                    'target' => $target
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified target from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $target = Target::find($id);
            $target->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Giro eliminado exitosamente'
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
            $target = Target::find($id);
            $target->active=($target->active)?false:true;
            $target->save();

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
    
    public function rpt_targets()
    {        
        $setting = Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get($setting->logo)):'';
        $company=$setting->company;

        $targets = Target::orderBy('name')->get();
        
        $data=[
            'company' => $company,
            'targets' => $targets,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_targets', $data);
        
        return $pdf->stream('Giros de Productos.pdf');

    }

    public function targets(){
        return Target::orderBy('name')->get();            
    }
}
