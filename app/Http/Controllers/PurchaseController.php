<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\User;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseSetting;
use App\Models\Product;
use App\Models\Service;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\InventoryMovement;
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
use Carbon\Carbon;
use Mail;
use App\Mail\PurchaseNotification;
use Storage;


class PurchaseController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->subscriber = session()->get('subscriber');
            return $next($request);
        });    
    }    
    
    public function index()
    {                        
        return view('purchases.index')->with('type', 'C');
    }

    public function orders()
    {                        
        return view('purchases.index')->with('type', 'O');
    }

    public function datatable(Request $request)
    {        
        $type_filter=$request->type_filter;
        $supplier_filter=$request->supplier_filter;

        if($supplier_filter!=''){
            $purchases = Purchase::
                join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->where('purchases.type', $type_filter)
                ->where('purchases.subscriber_id', $this->subscriber->id)
                ->where('purchases.supplier_id', $supplier_filter)
                ->orderBy('purchases.date', 'desc')
                ->orderBy('purchases.id', 'desc')
                ->select(['purchases.*', 'suppliers.name as supplier', 'suppliers.email as supplier_email']);
        }else{
            $purchases = Purchase::
                leftjoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->where('purchases.type', $type_filter)
                ->where('purchases.subscriber_id', $this->subscriber->id)
                ->orderBy('purchases.date', 'desc')
                ->orderBy('purchases.id', 'desc')
                ->select(['purchases.*', 'suppliers.name as supplier', 'suppliers.email as supplier_email']);
        }
        
        return Datatables::of($purchases)
            ->addColumn('action', function ($purchase) {
                    $opt_edit=($purchase->type=='O')?
                    '<a class="dropdown-item" href="'.route('purchases.load', [Crypt::encrypt($purchase->id), $purchase->type]).'">Editar</a>':'';
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                '.$opt_edit.'
                                <a class="dropdown-item" href="'.route('purchases.rpt_purchase', Crypt::encrypt($purchase->id)).'" target="_blank">Imprimir</a>
                                <a class="dropdown-item" href="#" onclick="showModalSend(`'.$purchase->id.'`)">Enviar por correo</a>                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$purchase->id.'`, `'.$purchase->folio.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                })           
            ->editColumn('folio', function ($purchase) {                    
                    return '<a href="'.route('purchases.load', [Crypt::encrypt($purchase->id), $purchase->type]).'"  style="color:inherit" title="Click para editar">'.$purchase->folio.'</a>';
                })
            ->editColumn('supplier', function ($purchase) {                    
                    return $purchase->supplier.'<br><small><i>'.$purchase->supplier_email.'</i></small>';
                })
            ->editColumn('date', function ($purchase) {                    
                    return $purchase->date->format('d/m/Y');
                })
            ->editColumn('due_date', function ($purchase) {                    
                    return ($purchase->due_date)?$purchase->due_date->format('d/m/Y'):'';
                })
            ->editColumn('total', function ($purchase) {                    
                    return session('coin').' '.money_fmt($purchase->total);
                })
            ->addColumn('pdf', function ($purchase) {                    
                    return '<a href="'.route('purchases.rpt_purchase', Crypt::encrypt($purchase->id)).'" target="_blank" title="Descargar"><i class="fa fa-download"></i></a>';
                })
            ->rawColumns(['action', 'folio', 'supplier', 'pdf'])
            ->make(true);
    }
    
    /**
     * Display the specified purchase.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id, $type)
    {
        $suppliers= $this->subscriber->suppliers()->where('active', true)
                    ->orderBy('name')->pluck('name','id');
        
        $products= $this->subscriber->products()->where('active', true)
                    ->orderBy('name')->pluck('name','id');

        $suppliers->prepend('** Nuevo Proveedor **',0); //al principio
        //$suppliers->push('** Nuevo Proveedor **',0); //al final

        $tax=$this->subscriber->iva; //va en configuraciones

        $sub_total=0;
        $total_discount=0;
        $total_tax=0;
        $total=0;

        if(PurchaseSetting::where('subscriber_id', $this->subscriber->id)->exists()){
            $setting=PurchaseSetting::where('subscriber_id', $this->subscriber->id)->first();
        }else{
            $setting=new PurchaseSetting();
            $setting->subscriber_id=$this->subscriber->id;
            $setting->save();
        }
        
        if(Crypt::decrypt($id)==0){
            $array_ids=[];
            $array_types=[];
            $array_descriptions=[];
            $array_quantities=[];
            $array_unit_prices=[];
            $array_sub_totals=[];
            $array_percent_discounts=[];
            $array_discounts=[];
            $array_percent_taxes=[];
            $array_taxes=[];
            $array_totals=[];
            $purchase=new Purchase();
        }else{
            $purchase=Purchase::find(Crypt::decrypt($id));
            $array_ids=$purchase->items()->pluck('item_id');
            $array_types=$purchase->items()->pluck('type');
            for ($i=0; $i < sizeof($array_ids) ; $i++){
                $array_descriptions[]=($array_types[$i]=='P')?Product::find($array_ids[$i])->name:Service::find($array_ids[$i])->name;
            }   
            $array_quantities=$purchase->items()->pluck('quantity');
            $array_unit_prices=$purchase->items()->pluck('unit_price');
            $array_sub_totals=$purchase->items()->pluck('sub_total');
            $array_percent_discounts=$purchase->items()->pluck('percent_discount');
            $array_discounts=$purchase->items()->pluck('discount');
            $array_percent_taxes=$purchase->items()->pluck('percent_tax');
            $array_taxes=$purchase->items()->pluck('tax');
            $array_totals=$purchase->items()->pluck('total');
        }

        for ($i=0; $i < sizeof($array_descriptions) ; $i++) { 
            $sub_total+=$array_sub_totals[$i];
            $total_discount+=$array_discounts[$i];
            $total_tax+=$array_taxes[$i];
            $total+=$array_totals[$i];
        }

        $tot_items=$this->subscriber->products()->count()+$this->subscriber->services()->count();
        $min_input_length=($tot_items>1000)?2:-1;        
        
        return view('purchases.save')->with('today', Carbon::now())
                        ->with('min_input_length', $min_input_length)
                        ->with('setting', $setting)
                        ->with('subscriber_id', $this->subscriber->id)
                        ->with('purchase', $purchase)
                        ->with('type', $type)
                        ->with('suppliers', $suppliers)
                        ->with('products', $products)
                        ->with('tax', $tax)
                        ->with('array_ids', json_encode($array_ids))
                        ->with('array_types', json_encode($array_types))
                        ->with('array_descriptions', json_encode($array_descriptions))
                        ->with('array_quantities', json_encode($array_quantities))
                        ->with('array_unit_prices', json_encode($array_unit_prices))
                        ->with('array_sub_totals', json_encode($array_sub_totals))
                        ->with('array_percent_discounts', json_encode($array_percent_discounts))
                        ->with('array_discounts', json_encode($array_discounts))
                        ->with('array_percent_taxes', json_encode($array_percent_taxes))
                        ->with('array_taxes', json_encode($array_taxes))
                        ->with('array_totals', json_encode($array_totals))
                        ->with('sub_total', $sub_total)
                        ->with('total_discount', $total_discount)
                        ->with('total_tax', $total_tax)
                        ->with('total', $total);
    }

    public function load_items(Request $request)
    {
        $sub_total=0;
        $total_discount=0;
        $total_tax=0;
        $total=0;

        if($request->array_descriptions){
            
            for ($i=0; $i < sizeof($request->array_descriptions) ; $i++) { 
                $sub_total+=$request->array_sub_totals[$i];
                $total_discount+=$request->array_discounts[$i];
                $total_tax+=$request->array_taxes[$i];
                $total+=$request->array_totals[$i];
            }
            
            return view('purchases.items')
                        ->with('array_descriptions', $request->array_descriptions)
                        ->with('array_quantities', $request->array_quantities)
                        ->with('array_unit_prices', $request->array_unit_prices)
                        ->with('array_sub_totals', $request->array_sub_totals)
                        ->with('array_percent_discounts', $request->array_percent_discounts)
                        ->with('array_discounts', $request->array_discounts)
                        ->with('array_percent_taxes', $request->array_percent_taxes)
                        ->with('array_taxes', $request->array_taxes)
                        ->with('array_totals', $request->array_totals)
                        ->with('sub_total', $sub_total)
                        ->with('total_discount', $total_discount)
                        ->with('total_tax', $total_tax)
                        ->with('total', $total);


        }else{
            return "";
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
        $purchase = Purchase::find($id);
        
        if($purchase){
            return response()->json([
                    'success' => true,
                    'purchase' => $purchase,
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }

    /**
     * Store a newly created purchase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $purchase=new Purchase();
            $purchase->user_id=Auth::user()->id;
            if($request->free){
                $purchase->prospect=$request->prospect;
            }else{
                $supplier=Supplier::find($request->supplier_id);
                $purchase->supplier_id=$supplier->id;
            }
            $purchase->subscriber_id=$request->subscriber_id;
            $purchase->type=$request->type; //O=Orden C=Compra
            if($purchase->type=='O'){
                $purchase->custom_order_folio=($request->custom_folio)?1:0;
                if($purchase->custom_order_folio){
                    $purchase->order_folio=$request->folio;
                }else{
                    $purchase->order_number=Purchase::where('subscriber_id', $purchase->subscriber_id)->max('order_number')+1;
                }
            }else{
                $purchase->custom_purchase_folio=($request->custom_folio)?1:0;
                if($purchase->custom_purchase_folio){
                    $purchase->purchase_folio=$request->folio;
                }else{
                    $purchase->purchase_number=Purchase::where('subscriber_id', $purchase->subscriber_id)->max('purchase_number')+1;
                }
            }
            $purchase->contact=$request->contact;
            $purchase->created_by=$request->created_by;
            $purchase->date=Carbon::now();
            ($purchase->type=='O')?$purchase->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date):'';
            $purchase->observations=($request->observations)?$request->observations:null;
            $purchase->conditions=($request->conditions)?$request->conditions:null;
            $purchase->save();
            
            $sub_total=0;
            $total_discount=0;
            $total_tax=0;
            $total=0;

            //$array_descriptions=$request->descriptions;
            $array_ids=$request->ids;
            $array_types=$request->types;
            $array_quantities=$request->quantities;
            $array_unit_prices=$request->unit_prices;
            $array_percent_discounts=$request->percent_discounts;
            $array_percent_taxes=$request->percent_taxes;
            
            for ($i=0; $i < count($array_ids) ; $i++) { 
                $purchase_item=new PurchaseItem();
                $purchase_item->purchase_id=$purchase->id;
                $purchase_item->item_id=$array_ids[$i];
                $purchase_item->type=$array_types[$i];
                $purchase_item->unit_price=$array_unit_prices[$i];
                $purchase_item->quantity=$array_quantities[$i];
                $purchase_item->sub_total=$purchase_item->unit_price*$purchase_item->quantity;
                $purchase_item->percent_discount=$array_percent_discounts[$i];
                $purchase_item->discount=$purchase_item->sub_total*($purchase_item->percent_discount/100);
                $purchase_item->percent_tax=$array_percent_taxes[$i];
                $purchase_item->tax=($purchase_item->sub_total-$purchase_item->discount)*($purchase_item->percent_tax/100);
                $purchase_item->total=$purchase_item->sub_total-$purchase_item->discount+$purchase_item->tax;
                $purchase_item->save();
                $sub_total+=$purchase_item->sub_total;
                $total_discount+=$purchase_item->discount;
                $total_tax+=$purchase_item->tax;
                $total+=$purchase_item->total;
                if($purchase->type=='C'){
                    //2. Se registra en el movimiento del inventario
                    $inventory_movement=new InventoryMovement();
                    $inventory_movement->subscriber_id=$purchase->subscriber_id;
                    $inventory_movement->transaction_id=$purchase_item->id;
                    $inventory_movement->date=$purchase->date->format('Y-m-d');
                    $inventory_movement->type='I';
                    $inventory_movement->input_type_id=1; //1=Compra
                    $inventory_movement->product_id=$purchase_item->item_id;
                    $inventory_movement->quantity=$purchase_item->quantity;
                    $inventory_movement->unit_price=$purchase_item->unit_price;
                    $inventory_movement->save();
                    $product=Product::find($purchase_item->item_id);
                    //3. Actualiza la existencia
                    $product->update_stock();
                }                
            }

            $purchase->sub_total=$sub_total;
            $purchase->total_discount=$total_discount;
            $purchase->total_tax=$total_tax;
            $purchase->total=$total;
            $purchase->save();

            ($request->send_email)?$this->send_email($purchase->id,$request->to):'';
            
            return response()->json([
                'success' => true,
                'message' => (($purchase->type=='O')?'Orden':'Compra').' registrada exitosamente',
                'purchase' => $purchase,
                //'base64'=> $this->base64($purchase->id)
            ]);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
       
   /**
     * Update the specified purchase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $purchase = Purchase::find($id);
            if($request->free){
                $purchase->prospect=$request->prospect;
                $purchase->supplier_id=null;
            }else{
                $supplier=Supplier::find($request->supplier_id);
                $purchase->supplier_id=$request->supplier_id;
                $purchase->prospect=null;
            }
            if($purchase->type=='O'){
                ($purchase->custom_order_folio)?$purchase->order_folio=$request->folio:'';
            }else{
                ($purchase->custom_purchase_folio)?$purchase->purchase_folio=$request->folio:'';
            }
            $purchase->contact=$request->contact;
            $purchase->created_by=$request->created_by;
            $purchase->date=Carbon::now();
            ($purchase->type=='O')?$purchase->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date):'';
            $purchase->observations=($request->observations)?$request->observations:null;
            $purchase->conditions=($request->conditions)?$request->conditions:null;
            $purchase->save();
            $purchase->items()->delete();

            $sub_total=0;
            $total_discount=0;
            $total_tax=0;
            $total=0;

            $array_ids=$request->ids;
            $array_types=$request->types;
            $array_quantities=$request->quantities;
            $array_unit_prices=$request->unit_prices;
            $array_percent_discounts=$request->percent_discounts;
            $array_percent_taxes=$request->percent_taxes;
            
            for ($i=0; $i < count($array_types) ; $i++) { 
                $purchase_item=new PurchaseItem();
                $purchase_item->purchase_id=$purchase->id;
                $purchase_item->item_id=$array_ids[$i];
                $purchase_item->type=$array_types[$i];
                $purchase_item->unit_price=$array_unit_prices[$i];
                $purchase_item->quantity=$array_quantities[$i];
                $purchase_item->sub_total=$purchase_item->unit_price*$purchase_item->quantity;
                $purchase_item->percent_discount=$array_percent_discounts[$i];
                $purchase_item->discount=$purchase_item->sub_total*($purchase_item->percent_discount/100);
                $purchase_item->percent_tax=$array_percent_taxes[$i];
                $purchase_item->tax=($purchase_item->sub_total-$purchase_item->discount)*($purchase_item->percent_tax/100);
                $purchase_item->total=$purchase_item->sub_total-$purchase_item->discount+$purchase_item->tax;
                $purchase_item->save();
                $sub_total+=$purchase_item->sub_total;
                $total_discount+=$purchase_item->discount;
                $total_tax+=$purchase_item->tax;
                $total+=$purchase_item->total;                
            }

            $purchase->sub_total=$sub_total;
            $purchase->total_discount=$total_discount;
            $purchase->total_tax=$total_tax;
            $purchase->total=$total;
            $purchase->save();
            
            return response()->json([
                'success' => true,
                'message' => (($purchase->type=='O')?'Orden':'Compra').' actualizada exitosamente',
                'purchase' => $purchase
            ]);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified purchase from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $purchase = Purchase::find($id);
            if($purchase->type=='C'){
                $transaction_ids=$purchase->products()->pluck('id');
                //elimina los movimientos de inventario de esa compra
                $inventory_movements=InventoryMovement::where('input_type_id', 1)->whereIn('transaction_id', $transaction_ids)->get();
                foreach($inventory_movements as $inventory_movement){
                    $product=Product::findOrfail($inventory_movement->product_id);
                    $inventory_movement->delete();
                    $product->update_stock();
                }
            }
            $purchase->delete();
            
            return response()->json([
                'success' => true,
                'message' => (($purchase->type=='O')?'Orden':'Compra').' eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
      
    public function purchase_pdf($id)
    {        
        $purchase=Purchase::find($id);
        $setting = PurchaseSetting::where('subscriber_id', $purchase->subscriber_id)->first();
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $sello=($this->subscriber->stamp)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->stamp)):'';
        

        $company=$setting->company;
        
        $data=[
            //'purchase_setting' => $purchase_setting,
            //'header' => $header,
            //'footer' => $footer,
            'setting' => $setting,            
            'purchase' => $purchase,
            'sello' => $sello,
            'subscriber' => $purchase->subscriber,            
            'logo' => $logo,
            'root_public' => realpath(public_path()),
            'root_storage' => realpath(storage_path())
        ];
                
        return $pdf = PDF::loadView('reports/rpt_purchase', $data);
    }    

    public function base64($id)
    {        
        $pdf=$this->purchase_pdf($id);
        return base64_encode($pdf->output());        
    }
    
    public function rpt_purchase($id){
        
        $purchase=Purchase::find(Crypt::decrypt($id));
        $pdf=$this->purchase_pdf($purchase->id);
        
        return $pdf->stream((($purchase->type=='O')?'Orden':'Compra').' Folio '.$purchase->folio.'.pdf');
    }
    
    public function download_purchase($id){
        
        $purchase=Purchase::find($id);
        $pdf=$this->purchase_pdf($purchase->id);

        return $pdf->download((($purchase->type=='O')?'Orden':'Compra').' Folio '.$purchase->folio.'.pdf');
    }

    public function load_send_modal($id)
    {
        $purchase = Purchase::find($id);
        
        return view('purchases.send')->with('purchase', $purchase);
    }
    
    public function send_email($id, $to){
                    
        try {
            $purchase=Purchase::find($id);
            $pdf=$this->purchase_pdf($id);
            $file = $pdf->output();

            Mail::to($to)->send(new PurchaseNotification($purchase, $file));
            
            return response()->json([
                'success' => true,
                'message' => (($purchase->type=='O')?'Orden':'Compra').' enviada exitosamente'
            ], 200);
        
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }

    }
        
    public function load_convert_modal($id)
    {
        $purchase = Purchase::find($id);
        
        return view('purchases.convert')->with('purchase', $purchase);
    }
    
    public function convert(Request $request, $id){
                    
        try {
            $purchase=Purchase::find($id);
            $purchase->type='C';
            $purchase->date=Carbon::now();
            $purchase->custom_purchase_folio=$request->custom_folio;
            if($purchase->custom_purchase_folio){
                $purchase->purchase_folio=$request->folio;
            }else{
                $purchase->purchase_number=Purchase::where('subscriber_id', $purchase->subscriber_id)->max('purchase_number')+1;
            }
            $purchase->save();
            //actualizar el inventario
            foreach($purchase->products()->get() as $product){
                //2. Se registra en el movimiento del inventario
                $inventory_movement=new InventoryMovement();
                $inventory_movement->subscriber_id=$purchase->subscriber_id;
                $inventory_movement->transaction_id=$product->id;
                $inventory_movement->date=$purchase->date->format('Y-m-d');
                $inventory_movement->type='I';
                $inventory_movement->input_type_id=1; //1=Compra
                $inventory_movement->product_id=$product->item_id;
                $inventory_movement->quantity=$product->quantity;
                $inventory_movement->unit_price=$product->unit_price;
                $inventory_movement->save();
                $product=Product::find($product->item_id);
                //3. Actualiza la existencia
                $product->update_stock();

            }

            ($request->send_email)?$this->send_email($purchase->id,$request->to):'';
            
            return response()->json([
                'success' => true,
                'message' => 'Orden convertida exitosamente',
                'purchase' => $purchase
            ], 200);
        
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }

    }

    /**
     * Display a listing of the purchase.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {                
        if(PurchaseSetting::where('subscriber_id', $this->subscriber->id)->exists()){
            $setting=PurchaseSetting::where('subscriber_id', $this->subscriber->id)->first();
        }else{
            $setting=new PurchaseSetting();
            $setting->subscriber_id=$this->subscriber->id;
            $setting->save();
        }

        return view('purchases.settings')->with('setting', $setting);
    }

    public function update_settings(Request $request, $id)
    {
        try {
            
            $setting = PurchaseSetting::find($id);        
            $setting->conditions= $request->conditions;
            $setting->save();        

            return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones actualizadas exitosamente',
                ], 200);                    
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }

    }

    public function products($id){
        try {
            $purchase = Purchase::findOrfail($id);        

            return response()->json([
                    'success' => true,
                    //'ids' => $purchase->products()->pluck('id')
                    'products' => $purchase->products()->get(),
                ], 200);                    
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
}
