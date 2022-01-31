<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProductPhotoRequest;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ProductPhotoController extends Controller
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
     * Display a listing of the product_photo.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        $product_photo = ProductPhoto::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$this->subscriber->id.'/products/'.$product_photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function thumbnail($id)
    {                
        $product_photo = ProductPhoto::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$this->subscriber->id.'/products/thumbs/'.$product_photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function load($id)
    {
        $product = Product::find($id);
        $photos=$product->photos()->get();

        return view('products.photos')->with('photos', $photos);
    }
    
    /**
     * Store a newly created product_photo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $product=Product::findOrfail($request->hdd_product_id);
            $product_photo = new ProductPhoto();        
            $product_photo->product_id=$product->id;
            $product_photo->title=$request->title;
            $file = $request->photo;        
            $product_photo->file_name = $file->getClientOriginalName();
            $product_photo->file_type = $file->getClientOriginalExtension();
            $product_photo->file=$this->upload_file($this->subscriber->id.'/products/', $file);
            $product_photo->main=($product->photos()->where('main',true)->count()>0)?false:true;
            $product_photo->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Foto registrada exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified product_photo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $product_photo = ProductPhoto::find($id);
            $product_photo->title=$request->title;
            if($request->main){
                $product=Product::findOrfail($product_photo->product_id);
                $product->photos()->update(['main' => false]);
                $product_photo->main=true;
            }
            $product_photo->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Foto actualizada exitosamente',
                    'product_photo' => $product_photo
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified product_photo from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product_photo = ProductPhoto::find($id);
            $product=Product::findOrfail($product_photo->product_id);
            Storage::delete($this->subscriber->id.'/products/thumbs/'.$product_photo->file);
            Storage::delete($this->subscriber->id.'/products/'.$product_photo->file);        
            $product_photo->delete();
            if($product->photos()->count()>0 && $product->photos()->where('main',true)->count()==0){
                $product_photo=$product->photos()->first();
                $product_photo->main=true;
                $product_photo->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
