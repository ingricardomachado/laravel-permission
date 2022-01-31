<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\User;
use App\Models\Category;
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

class CategoryController extends Controller
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
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('categories.index');
    }

    public function datatable(Request $request)
    {        
        
        $categories = $this->subscriber->categories()->orderBy('name');
        
        return Datatables::of($categories)
            ->addColumn('action', function ($category) {
                if($category->active){
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalCategory('.$category->id.')">Editar</a>
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$category->id.')">Desactivar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$category->id.'`, `'.$category->name.'`, `'.$category->credit_points.'`, `'.$category->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                }else{
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$category->id.')"> Activar</a>
                            </div>
                        </div>';
                }
            })           
            ->editColumn('name', function ($category) {                    
                    return '<a href="#"  onclick="showModalCategory('.$category->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$category->name.'</a>';
                })
            ->editColumn('status', function ($category) {                    
                    return $category->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $category = new Category();
        }else{
            $category = Category::find($id);
        }
        
        return view('categories.save')->with('category', $category);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        try {
            $category = new Category();
            $category->subscriber_id=$request->subscriber_id;
            $category->name=$request->name;
            $category->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Categoría registrada exitosamente',
                    'category' => $category
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
        $category = Category::find($id);
        
        if($category){
            return response()->json([
                    'success' => true,
                    'category' => $category
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = Category::find($id);
            $category->name=$request->name;
            $category->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente',
                    'category' => $category
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            $category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
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
            $category = Category::find($id);
            $category->active=($category->active)?false:true;
            $category->save();

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
    
    public function rpt_categories()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;

        $categories = $this->subscriber->categories()->orderBy('name')->get();
        
        $data=[
            'company' => $company,
            'categories' => $categories,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_categories', $data);
        
        return $pdf->stream('Categorías de Productos.pdf');

    }

    public function categories(){
        return Category::orderBy('name')->get();            
    }
}
