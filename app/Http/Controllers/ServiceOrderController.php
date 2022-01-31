<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Subscriber;
use App\Models\ServiceOrder;
use App\Models\Photo;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceOrdersExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Carbon\Carbon;
use Storage;

class ServiceOrderController extends Controller
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
        
        $customers=$this->subscriber->customers()->orderBy('name')->pluck('name','id');
        
        return view('service_orders.index')
                        ->with('customers', $customers);
    }

    public function datatable(Request $request)
    {        
        
        $customer_filter=$request->customer_filter;

        if($customer_filter!=''){
            $service_orders = $this->subscriber->service_orders()->where('customer_id', $customer_filter)->orderBy('date', 'desc');
        }else{
            $service_orders = $this->subscriber->service_orders()->orderBy('date', 'desc');
        }

        return Datatables::of($service_orders)
            ->addColumn('action', function ($service_order) {
                return '
                    <div class="input-group-prepend">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$service_order->id.'`, `'.$service_order->folio_mask.'`)">Eliminiar</a>                                
                        </div>
                    </div>';
            })           
            ->editColumn('folio', function ($service_order) {                    
                    return '<b>'.$service_order->folio_mask.'</b>';
                })
            ->editColumn('date', function ($service_order) {                    
                    return $service_order->date->format('d/m/Y H:i');
                })
            ->editColumn('customer', function ($service_order) {                    
                    return $service_order->customer->name.'<br><small>'.$service_order->service->name.'</small>';
                })
            ->addColumn('pdf', function ($service_order) {                    
                    return '<a href="'.route('service_orders.download_file', $service_order->id).'" target="_self" title="Descargar"><i class="fa fa-download"></i></a>';
                })
            ->rawColumns(['action', 'folio', 'customer', 'pdf'])
            ->make(true);
    }

    /**
     * Store a newly created service_order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        try {
            $customer=Customer::findOrfail($request->customer);
            $service_order=new ServiceOrder();
            $service_order->user_id=Auth::user()->id;
            $service_order->subscriber_id=$customer->subscriber_id;
            $service_order->customer_id=$customer->id;
            $service_order->service_id=$request->service;
            $service_order->custom_folio=$request->custom_folio;
            if($service_order->custom_folio){
                $service_order->folio=$request->folio;
            }else{
                $service_order->number=ServiceOrder::where('subscriber_id', $service_order->subscriber_id)->max('number')+1;
            }
            $service_order->date=Carbon::createFromFormat('d/m/Y H:i', $request->date);
            $service_order->activities=$request->activities;
            $service_order->recomendations=$request->recomendations;
            $service_order->notes=$request->notes;
            $service_order->contact=$request->contact;
            $service_order->contact_cell=$request->contact_cell;
            $service_order->contact_email=$request->contact_email;
            if($request->photos){
                $str_photos="";
                $array_photos=(gettype($request->photos)=="string")?json_decode($request->photos, true):$request->photos;
                for ($i=0; $i < count($array_photos) ; $i++) {  
                    $str_photos=($i==0)?$array_photos[$i]['id']:$str_photos.", ".$array_photos[$i]['id'];
                }
                $service_order->photos=$str_photos;            
            }
            $service_order->save();
            //detalle de la orden
            if($request->products){
                $array_products=(gettype($request->products)=="string")?json_decode($request->products, true):$request->products;
                for ($i=0; $i < count($array_products) ; $i++) { 
                    $service_order->products()->attach($array_products[$i]['id'], 
                        [
                            'quantity' => $array_products[$i]['quantity'],
                            'more_info' => $array_products[$i]['more_info']
                        ]);
                }
            }
            //el file_get_contents se usa para obtener la img y pasarla al pdf sin garbarla en storage
            $contact_signature=($request->contact_signature && $request->contact_signature!='')?file_get_contents($request->contact_signature):null;
            //se crea el pdf
            $pdf  = $this->pdf($service_order->id, $contact_signature);
            $file = $pdf->output();        
            $service_order->file=rand(10000000,9999999999).'.pdf';
            // se sube al storage
            Storage::put($service_order->subscriber_id.'/service_orders/'.$service_order->file, $file);
            $service_order->save();
            $service_order['url']=route('service_orders.download_file', $service_order->id);

            return response()->json([
                    'success' => true,
                    'message' => 'Order de Servicio registrada exitosamente',
                    'service_order' => $service_order,
                    'request' => $request->toArray()
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
        $service_order = ServiceOrder::find($id);
        $service_order['products']=$service_order->products()->get();
        $service_order['url']=route('service_orders.download_file', $service_order->id);

        
        if($service_order){
            return response()->json([
                    'success' => true,
                    'service_order' => $service_order
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified service_order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            //
            return response()->json([
                    'success' => true,
                    'message' => 'Order de Servicio actualizada exitosamente',
                    'service_order' => $service_order
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified service_order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $service_order = ServiceOrder::find($id);
            Storage::delete($service_order->subscriber_id.'/service_orders/'.$service_order->file);
            $service_order->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Order de Servicio eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pdf($id, $contact_signature)
    {        
        $service_order=ServiceOrder::find($id);

        $logo=($service_order->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($service_order->subscriber_id.'/'.$service_order->subscriber->logo)):'';
        $company=$service_order->subscriber->bussines_name;

        $signature=($contact_signature)?'data:image/png;base64, '.base64_encode($contact_signature):'';
        
        $user_signature=($service_order->user->signature)?'data:image/png;base64, '.base64_encode(Storage::get($service_order->subscriber_id.'/users/'.$service_order->user->signature)):'';
        
        if($service_order->photos){
            $photos_ids=explode(',', $service_order->photos);
            $photos=Photo::whereIn('id', $photos_ids)
                        ->take(15)->get();
        }else{
            $photos=collect([]);
        }

        $data=[
            'logo' => $logo,
            'company' => $company,
            'service_order' => $service_order,
            'photos' => $photos,
            'stamp' => '',
            'user_signature' => $user_signature,
            'contact_signature' => $signature,
        ];
                
        return PDF::loadView('reports/rpt_service_order', $data);
    }    

    public function base64($id, $contact_signature)
    {        
        $pdf=$this->pdf($id, $contact_signature);
        return base64_encode($pdf->output());        
    }

    /*
     * Download file from DB  
    */ 
    public function download_file($id)
    {
        try {
            $service_order = ServiceOrder::find($id);
        
            return Storage::download($service_order->subscriber_id.'/service_orders/'.$service_order->file, 'Orden de Servicio.pdf');
            
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rpt_service_orders()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $service_orders=$this->subscriber->service_orders()->orderBy('date', 'desc')->get();

        $data=[
            'company' => $company,
            'service_orders' => $service_orders,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_service_orders', $data);
        
        return $pdf->stream('Ordenes de Servicio.pdf');
    }

    public function xls_service_orders(Request $request)
    {        
        return Excel::download(new ServiceOrdersExport($this->subscriber), 'Ordenes de Servicio.xlsx');        
    }

}
