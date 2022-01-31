<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProductDocumentRequest;
use App\Models\Project;
use App\Models\ProductDocument;
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

class ProductDocumentController extends Controller
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
     * Display a listing of the document.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        //
    }

    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $document = new ProductDocument();        
            $document->product_id=$request->product_id;
            $file = $request->file;        
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
            $document->file=$this->upload_file($this->subscriber->id.'/products/', $file);
            $document->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Documento registrado exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified document from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $document = ProductDocument::find($id);
            if($document->file){
                Storage::delete($document->product->subscriber_id.'/products/'.$document->file);
                Storage::delete($document->product->subscriber_id.'/products/thumbs/'.$document->file);
            };
            $document->delete();

            return response()->json([
                    'success' => true,
                    'message' => 'Documento eliminado exitosamente'
                ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }


    /*
     * Download file from DB  
    */ 
    public function download($id)
    {
        $document = ProductDocument::find($id);
        return Storage::download($document->product->subscriber_id.'/products/'.$document->file, $document->file_name);        
    }
}
