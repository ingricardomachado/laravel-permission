<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Photo;
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

class PhotoController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->subscriber = session()->get('subscriber');
            return $next($request);
        });
    }    
    
    public function photos(){
        try {
            $photos=Photo::all();
            foreach($photos as $photo){
                $photo['url']=url('photo/'.$photo->id);
                $photo['url_thumbnail']=url('photo_thumbnail/'.$photo->id);
            }

            return response()->json([
                    'success' => true,
                    'photos' => $photos
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    /**
     * Display a listing of the photo.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        try {
            $photo=Photo::findOrFail($id);
            $photo['url']=url('photo/'.$photo->id);
            $photo['url_thumbnail']=url('photo_thumbnail/'.$photo->id);

            return response()->json([
                    'photo' => $photo
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    public function photo($id)
    {                
        $photo = Photo::findOrFail($id);
        $picture = Image::make(Storage::get($photo->customer->subscriber_id.'/photos/'.$photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function thumbnail($id)
    {                
        $photo = Photo::findOrFail($id);
        $picture = Image::make(Storage::get($photo->customer->subscriber_id.'/photos/thumbs/'.$photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /**
     * Store a newly created photo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $customer=Customer::findOrfail($request->customer_id);
            $photo = new Photo();        
            $photo->customer_id=$customer->id;
            $photo->note=$request->note;
            $file = $request->photo;        
            $photo->file_name = $file->getClientOriginalName();
            $photo->file_type = $file->getClientOriginalExtension();
            $photo->file_size = $file->getSize();
            $photo->file=$this->upload_file($customer->subscriber_id.'/photos/', $file);
            $photo->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Foto registrada exitosamente',
                    'photo' => $photo
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified photo in storage.
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
     * Remove the specified photo from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $photo = Photo::findOrFail($id);
            Storage::delete($photo->customer->subscriber_id.'/photos/thumbs/'.$photo->file);
            Storage::delete($photo->customer->subscriber_id.'/photos/'.$photo->file);        
            $photo->delete();
            
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
