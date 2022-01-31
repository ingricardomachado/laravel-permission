<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\UnitRequest;
use App\User;
use App\Models\Unit;
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

class UnitController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the unit.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('units.index');
    }

    public function datatable()
    {        

        $units = Unit::orderBy('unit');        
        
        return Datatables::of($units)
            ->addColumn('action', function ($unit) {
                if($unit->id==0){
                  return "";   
                }
                if($unit->active){
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalUnit('.$unit->id.')">Editar</a>
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$unit->id.')">Desactivar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$unit->id.'`, `'.$unit->name.'`, `'.$unit->credit_points.'`, `'.$unit->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                }else{
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$unit->id.')"> Activar</a>
                            </div>
                        </div>';
                }
            })           
            ->editColumn('unit', function ($unit) {                    
                    return '<a href="#"  onclick="showModalUnit('.$unit->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$unit->unit.'</a>';
                })
            ->editColumn('status', function ($unit) {                    
                    return $unit->status_label;
                })
            ->rawColumns(['action', 'unit', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified unit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $unit = new Unit();
        }else{
            $unit = Unit::find($id);
        }
        
        return view('units.save')->with('unit', $unit);
    }

    /**
     * Store a newly created unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitRequest $request)
    {
        try {
            $unit = new Unit();
            $unit->unit=$request->unit;
            $unit->name=$request->name;
            $unit->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Unidad registrada exitosamente',
                    'unit' => $unit
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
        $unit = Unit::find($id);
        
        if($unit){
            return response()->json([
                    'success' => true,
                    'unit' => $unit
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UnitRequest $request, $id)
    {
        try {
            $unit = Unit::find($id);           
            $unit->unit=$request->unit;
            $unit->name=$request->name;
            $unit->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Unidad actualizada exitosamente',
                    'unit' => $unit
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            $unit->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Unidad eliminada exitosamente'
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
            $unit = Unit::find($id);
            $unit->active=($unit->active)?false:true;
            $unit->save();

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
    
    public function rpt_units()
    {        
        $setting = Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get($setting->logo)):'';
        $company=$setting->company;

        $units = Unit::orderBy('unit')->get();
        
        $data=[
            'company' => $company,
            'units' => $units,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_units', $data);
        
        return $pdf->stream('Unidades.pdf');

    }

    public function units(){
        return Unit::orderBy('name')->get();            
    }
}
