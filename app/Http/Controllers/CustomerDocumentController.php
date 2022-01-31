<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerDocumentRequest;
use App\Models\Project;
use App\Models\CustomerDocument;
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

class CustomerDocumentController extends Controller
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
            $document = new CustomerDocument();        
            $document->customer_id=$request->customer_id;
            $file = $request->file;        
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
            $document->file=$this->upload_file($this->subscriber->id.'/customers/', $file);
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
            $document = CustomerDocument::find($id);
            if($document->file){
                Storage::delete($document->customer->subscriber_id.'/customers/'.$document->file);
                Storage::delete($document->customer->subscriber_id.'/customers/thumbs/'.$document->file);
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
        $document = CustomerDocument::find($id);
        return Storage::download($document->customer->subscriber_id.'/customers/'.$document->file, $document->file_name);        
    }
}
