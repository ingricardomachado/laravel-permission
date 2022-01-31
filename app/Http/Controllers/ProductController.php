<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use App\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductDocument;
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
use App\Exports\ProductsExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ProductController extends Controller
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
     * Display a listing of the product.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        $categories= $this->subscriber->categories()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');
        
        return view('products.index')->with('categories', $categories);
    }

    public function datatable(Request $request)
    {        
        $category_filter=$request->category_filter;

        if($category_filter!=''){
            $products = $this->subscriber->products()
                        ->leftjoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->join('units', 'products.unit_id', '=', 'units.id')
                        ->where('products.category_id', $category_filter)
                        ->select(['products.*', 'categories.name as category', 'units.name as unit', 'suppliers.name as supplier']);
        }else{
            $products = $this->subscriber->products()
                        ->leftjoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->join('units', 'products.unit_id', '=', 'units.id')
                        ->select(['products.*', 'categories.name as category', 'units.name as unit', 'suppliers.name as supplier']);
        }  
        return Datatables::of($products)
            ->addColumn('action', function ($product) {
                if($product->active){
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="showModalProduct('.$product->id.')">Editar</a>
                                <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$product->id.')">Desactivar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$product->id.'`, `'.$product->name.'`, `'.$product->credit_points.'`, `'.$product->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                }else{
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$product->id.')"> Activar</a>
                            </div>
                        </div>';
                }
            })           
            ->editColumn('number', function ($product) {                    
                    return '<b>'.$product->number_mask.'</b>';
                })
            ->editColumn('name', function ($product) {                    
                    return '<a href="#"  onclick="showModalProduct('.$product->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$product->name.'<br><small><i>'.$product->category.'</i></small></a>';
                })
            ->addColumn('files', function ($product) {                    
                    $all_files="";
                    foreach ($product->documents()->get() as $document) {
                        $all_files .= '<div class="text-center"><a href="'.route('product_documents.download', $document->id).'" title="'.$document->file_name.'"><i class="fas fa-cloud-download-alt"></i></a> <a href="#" title="Eliminar" onclick="showModalDeleteDocument(`'.$document->id.'`, `'.$document->file_name.'`)"><i class="far fa-trash-alt"></i></a></div>';
                    }
                    return $all_files;
                })
            ->editColumn('status', function ($product) {                    
                    return $product->status_label;
                })
            ->rawColumns(['action', 'number', 'name', 'files', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $units= Unit::where('active',true)->orderBy('unit')->pluck('name','id');

        $default=Unit::findOrfail(0);
        $units->prepend($default->name, $default->id);
        
        $categories= $this->subscriber->categories()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');
        $default=Category::findOrfail(0);
        $categories->prepend($default->name, $default->id);
        
        $suppliers= $this->subscriber->suppliers()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');
        $default=Supplier::findOrfail(0);
        $suppliers->prepend($default->name, $default->id);


        if($id==0){
            $product = new Product();
        }else{
            $product = Product::find($id);
        }
        
        return view('products.save')->with('product', $product)
                        ->with('categories', $categories)
                        ->with('suppliers', $suppliers)
                        ->with('units', $units);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = new Product();
            $product->number=Product::where('subscriber_id', $request->subscriber_id)->max('number')+1;
            $product->subscriber_id=$request->subscriber_id;
            $product->category_id=$request->category;
            $product->code=$request->code;
            $product->code_fe=$request->code_fe;
            $product->unit_id=$request->unit;
            $product->unit_fe=$request->unit_fe;
            $product->name=$request->name;
            $product->description=$request->description;
            $product->make=$request->make;
            $product->model=$request->model;
            $product->manufacturer_code=$request->manufacturer_code;
            $product->spare=$request->spare;
            $product->part_code=$request->part_code;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->supplier_id=$request->supplier;
            $product->initial_stock=$request->initial_stock;
            $product->reorder_point=$request->reorder_point;
            $product->safety_stock=$request->safety_stock;
            $product->cost=$request->cost;
            $product->price=$request->price;
            $file = $request->photo;        
            if(File::exists($file)){
                $product->photo_name = $file->getClientOriginalName();
                $product->photo_type = $file->getClientOriginalExtension();
                $product->photo_size = $file->getSize();                
                $product->photo=$this->upload_file($this->subscriber->id.'/products/', $file);
            }
            $product->save();
            $product->update_stock();
            //documentos
            if($request->hasfile('filenames')){
                foreach($request->file('filenames') as $file){
                    $document = new ProductDocument();
                    $document->product_id=$product->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($product->subscriber_id.'/products/', $file);
                    $document->save();
                }
            }

            return response()->json([
                    'success' => true,
                    'message' => 'Producto registrado exitosamente',
                    'product' => $product
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
        $product = Product::find($id);
        
        if($product){
            return response()->json([
                    'success' => true,
                    'data' => $product
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            $product->category_id=$request->category;
            $product->code=$request->code;
            $product->code_fe=$request->code_fe;
            $product->unit_id=$request->unit;
            $product->unit_fe=$request->unit_fe;
            $product->name=$request->name;
            $product->description=$request->description;
            $product->make=$request->make;
            $product->model=$request->model;
            $product->manufacturer_code=$request->manufacturer_code;
            $product->spare=$request->spare;
            $product->part_code=$request->part_code;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->supplier_id=$request->supplier;
            $product->initial_stock=$request->initial_stock;
            $product->reorder_point=$request->reorder_point;
            $product->safety_stock=$request->safety_stock;
            $product->cost=$request->cost;
            $product->price=$request->price;
            $product->field1=$request->field1;
            $file = $request->photo;        
            if(File::exists($file)){
                if($product->photo){
                    Storage::delete($this->subscriber->id.'/products/'.$product->photo);
                    Storage::delete($this->subscriber->id.'/products/thumbs/'.$product->photo);
                }
                $product->photo_name = $file->getClientOriginalName();
                $product->photo_type = $file->getClientOriginalExtension();
                $product->photo_size = $file->getSize();                
                $product->photo=$this->upload_file($this->subscriber->id.'/products/', $file);
            }
            $product->save();
            $product->update_stock();
            if($request->hasfile('filenames')){
                foreach($request->file('filenames') as $file){
                    $document = new ProductDocument();
                    $document->product_id=$product->id;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_type = $file->getClientOriginalExtension();
                    $document->file_size = $file->getSize();
                    $document->file=$this->upload_file($product->subscriber_id.'/products/', $file);
                    $document->save();
                }
            }            

            return response()->json([
                    'success' => true,
                    'message' => 'Producto actualizado exitosamente',
                    'product' => $product
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
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
            $product = Product::find($id);
            $product->active=($product->active)?false:true;
            $product->save();

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
    
    public function rpt_products()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;

        $products = $this->subscriber->products()->orderBy('name')->get();
        
        $data=[
            'company' => $company,
            'products' => $products,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_products', $data);
        
        return $pdf->stream('Productos.pdf');

    }

    public function xls_products(Request $request)
    {        
        return Excel::download(new ProductsExport($this->subscriber), 'Productos.xlsx');        
    }
    
    public function products(){
        return Product::orderBy('name')->get();            
    }

    /**
     * Display the specified location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gallery($id)
    {
        $product = Product::find($id);
        
        return view('products.gallery')->with('product', $product);
    }        

}
