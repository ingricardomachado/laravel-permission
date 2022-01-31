<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleSetting;
use App\Models\Product;
use App\Models\Service;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Carbon\Carbon;
use Mail;
use App\Mail\SaleNotification;
use Storage;


class SaleController extends Controller
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
        return view('sales.index')->with('type', 'F');
    }

    public function budgets()
    {                        
        return view('sales.index')->with('type', 'C');
    }

    public function datatable(Request $request)
    {        
        $type_filter=$request->type_filter;
        $customer_filter=$request->customer_filter;

        if($customer_filter!=''){
            $sales = Sale::
                join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sales.type', $type_filter)
                ->where('sales.subscriber_id', $this->subscriber->id)
                ->where('sales.customer_id', $customer_filter)
                ->orderBy('sales.date', 'desc')
                ->orderBy('sales.id', 'desc')
                ->select(['sales.*', 'customers.name as customer', 'customers.email as customer_email']);
        }else{
            $sales = Sale::
                leftjoin('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sales.type', $type_filter)
                ->where('sales.subscriber_id', $this->subscriber->id)
                ->orderBy('sales.date', 'desc')
                ->orderBy('sales.id', 'desc')
                ->select(['sales.*', 'customers.name as customer', 'customers.email as customer_email']);
        }
        
        return Datatables::of($sales)
            ->addColumn('action', function ($sale) {
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="'.route('sales.load', [Crypt::encrypt($sale->id), $sale->type]).'">Editar</a>
                                <a class="dropdown-item" href="'.route('sales.rpt_sale', Crypt::encrypt($sale->id)).'" target="_blank">Imprimir</a>
                                <a class="dropdown-item" href="#" onclick="showModalSend(`'.$sale->id.'`)">Enviar por correo</a>                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$sale->id.'`, `'.$sale->folio.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
                })           
            ->editColumn('folio', function ($sale) {                    
                    return '<a href="'.route('sales.load', [Crypt::encrypt($sale->id), $sale->type]).'"  style="color:inherit" title="Click para editar"><b>'.$sale->folio.'</b></a>';
                })
            ->editColumn('customer', function ($sale) {                    
                    return $sale->customer.'<br><small><i>'.$sale->customer_email.'</i></small>';
                })
            ->editColumn('date', function ($sale) {                    
                    return $sale->date->format('d/m/Y');
                })
            ->editColumn('due_date', function ($sale) {                    
                    return ($sale->due_date)?$sale->due_date->format('d/m/Y'):'';
                })
            ->editColumn('total', function ($sale) {                    
                    return session('coin').' '.money_fmt($sale->total);
                })
            ->addColumn('pdf', function ($sale) {                    
                    return '<a href="'.route('sales.rpt_sale', Crypt::encrypt($sale->id)).'" target="_blank" title="Descargar"><i class="fa fa-download"></i></a>';
                })
            ->rawColumns(['action', 'folio', 'customer', 'pdf'])
            ->make(true);
    }
    
    /**
     * Display the specified sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id, $type)
    {
        $customers= $this->subscriber->customers()->where('active', true)
                    ->orderBy('name')->pluck('name','id');
        
        $customers->prepend('** Nuevo Cliente **',0); //al principio
        //$customers->push('** Nuevo Cliente **',0); //al final

        $tax=$this->subscriber->iva; //va en configuraciones

        $sub_total=0;
        $total_discount=0;
        $total_tax=0;
        $total=0;
        $due_date=Carbon::now()->addDay();

        if(SaleSetting::where('subscriber_id', $this->subscriber->id)->exists()){
            $setting=SaleSetting::where('subscriber_id', $this->subscriber->id)->first();
        }else{
            $setting=new SaleSetting();
            $setting->subscriber_id=$this->subscriber->id;
            $setting->save();
        }
        
        if(Crypt::decrypt($id)==0){
            $array_ids=[];
            $array_types=[];
            $array_codes=[];
            $array_descriptions=[];
            $array_quantities=[];
            $array_unit_prices=[];
            $array_sub_totals=[];
            $array_percent_discounts=[];
            $array_discounts=[];
            $array_percent_taxes=[];
            $array_taxes=[];
            $array_totals=[];
            $sale=new Sale();
        }else{
            $sale=Sale::find(Crypt::decrypt($id));
            ($sale->due_date->greaterThan($due_date))?$due_date=$sale->due_date:'';
            $array_ids=$sale->items()->pluck('item_id');
            $array_types=$sale->items()->pluck('type');
            for ($i=0; $i < sizeof($array_ids) ; $i++){
                if($array_types[$i]=='P'){
                    $product=Product::find($array_ids[$i]);
                    $array_codes[]=$product->code;
                    $array_descriptions[]=$product->name;
                }else{
                    $service=Service::find($array_ids[$i]);
                    $array_codes[]=$service->code;
                    $array_descriptions[]=$service->name;
                }
                
            }   
            $array_quantities=$sale->items()->pluck('quantity');
            $array_unit_prices=$sale->items()->pluck('unit_price');
            $array_sub_totals=$sale->items()->pluck('sub_total');
            $array_percent_discounts=$sale->items()->pluck('percent_discount');
            $array_discounts=$sale->items()->pluck('discount');
            $array_percent_taxes=$sale->items()->pluck('percent_tax');
            $array_taxes=$sale->items()->pluck('tax');
            $array_totals=$sale->items()->pluck('total');
        }

        for ($i=0; $i < sizeof($array_descriptions) ; $i++) { 
            $sub_total+=$array_sub_totals[$i];
            $total_discount+=$array_discounts[$i];
            $total_tax+=$array_taxes[$i];
            $total+=$array_totals[$i];
        }

        $tot_items=$this->subscriber->products()->count()+$this->subscriber->services()->count();
        $min_input_length=($tot_items>1000)?2:-1;

        return view('sales.save')
                        ->with('today', Carbon::now())
                        ->with('due_date', $due_date)
                        ->with('min_input_length', $min_input_length)
                        ->with('setting', $setting)
                        ->with('subscriber_id', $this->subscriber->id)
                        ->with('sale', $sale)
                        ->with('type', $type)
                        ->with('customers', $customers)
                        ->with('tax', $tax)
                        ->with('array_ids', json_encode($array_ids))
                        ->with('array_types', json_encode($array_types))
                        ->with('array_codes', json_encode($array_codes))
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
            
            return view('sales.items')
                        ->with('array_codes', $request->array_codes)
                        ->with('array_descriptions', $request->array_descriptions)
                        ->with('array_quantities', $request->array_quantities)
                        ->with('array_unit_prices', $request->array_unit_prices)
                        ->with('array_sub_totals', $request->array_sub_totals)
                        ->with('array_percent_discounts', $request->array_percent_discounts)
                        ->with('array_discounts', $request->array_discounts)
                        ->with('array_percent_taxes', $request->array_percent_taxes)
                        ->with('array_taxes', $request->array_taxes)
                        ->with('array_totals', $request->array_totals)
                        ->with('sub_total', $sub_total-$total_discount)
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
        $sale = Sale::find($id);
        $sale['items']=$sale->items()->get();
        
        if($sale){
            return response()->json([
                    'success' => true,
                    'sale' => $sale,
                    'base64'=> $this->base64($sale->id)
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }

    /**
     * Store a newly created sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $sale=new Sale();
            $sale->user_id=Auth::user()->id;
            if($request->free){
                $sale->prospect=$request->prospect;
            }else{
                $customer=Customer::find($request->customer_id);
                $sale->customer_id=$customer->id;
            }
            $sale->subscriber_id=$request->subscriber_id;
            $sale->type=$request->type; //C=Cotizacion F=Factura
            if($sale->type=='C'){
                $sale->custom_budget_folio=($request->custom_folio)?1:0;
                if($sale->custom_budget_folio){
                    $sale->budget_folio=$request->folio;
                }else{
                    $sale->budget_number=Sale::where('subscriber_id', $sale->subscriber_id)->max('budget_number')+1;
                }
            }else{
                $sale->custom_sale_folio=($request->custom_folio)?1:0;
                if($sale->custom_sale_folio){
                    $sale->sale_folio=$request->folio;
                }else{
                    $sale->sale_number=Sale::where('subscriber_id', $sale->subscriber_id)->max('sale_number')+1;
                }
            }
            $sale->contact=$request->contact;
            $sale->created_by=Auth::user()->name;
            $sale->date=Carbon::now();
            ($sale->type=='C')?$sale->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date):'';
            $sale->way_pay=($request->way_pay)?$request->way_pay:null;
            $sale->method_pay=($request->method_pay)?$request->method_pay:null;
            $sale->condition_pay=($request->condition_pay)?$request->condition_pay:null;
            $sale->observations=($request->observations)?$request->observations:null;
            $sale->conditions=($request->conditions)?$request->conditions:null;
            $sale->save();
            
            $sub_total=0;
            $total_discount=0;
            $total_tax=0;
            $total=0;
            if($request->products){
                //mobile
                $array_products=(gettype($request->products)=="string")?json_decode($request->products, true):$request->products;
                for ($i=0; $i < count($array_products) ; $i++) { 
                    $array_ids[]=$array_products[$i]['id'];
                    $array_types[]=$array_products[$i]['type'];
                    $array_quantities[]=$array_products[$i]['quantity'];
                    $array_unit_prices[]=$array_products[$i]['unit_price'];
                    $array_percent_discounts[]=$array_products[$i]['percent_discount'];
                    $array_percent_taxes[]=$array_products[$i]['percent_tax'];
                }
            }else{
                //web
                $array_ids=$request->ids;
                $array_types=$request->types;
                $array_quantities=$request->quantities;
                $array_unit_prices=$request->unit_prices;
                $array_percent_discounts=$request->percent_discounts;
                $array_percent_taxes=$request->percent_taxes;                
            }
            
            for ($i=0; $i < count($array_ids) ; $i++) { 
                $sale_item=new SaleItem();
                $sale_item->sale_id=$sale->id;
                $sale_item->item_id=$array_ids[$i];
                $sale_item->type=$array_types[$i];
                $sale_item->unit_price=$array_unit_prices[$i];
                $sale_item->quantity=$array_quantities[$i];
                $sale_item->sub_total=$sale_item->unit_price*$sale_item->quantity;
                $sale_item->percent_discount=$array_percent_discounts[$i];
                $sale_item->discount=$sale_item->sub_total*($sale_item->percent_discount/100);
                $sale_item->percent_tax=$array_percent_taxes[$i];
                $sale_item->tax=($sale_item->sub_total-$sale_item->discount)*($sale_item->percent_tax/100);
                $sale_item->total=$sale_item->sub_total-$sale_item->discount+$sale_item->tax;
                $sale_item->save();
                $sub_total+=$sale_item->sub_total;
                $total_discount+=$sale_item->discount;
                $total_tax+=$sale_item->tax;
                $total+=$sale_item->total;                
            }

            $sale->sub_total=$sub_total;
            $sale->total_discount=$total_discount;
            $sale->total_tax=$total_tax;
            $sale->total=$total;
            $sale->save();
            $sale['items']=$sale->items()->get();

            if($request->send_email){
                if($request->to){
                    $array_emails=(gettype($request->to)=="string")?json_decode($request->to, true):$request->to;
                    for ($i=0; $i < count($array_emails); $i++) { 
                        $email=$array_emails[$i];
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                            $this->send_email($sale->id,$email);
                            //$this->send_email($sale->id,'ing.ricardo.machado@gmail.com');
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => (($sale->type=='C')?'Cotización':'Factura').' registrada exitosamente',
                'sale' => $sale,
                'base64'=> $this->base64($sale->id)
            ]);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
       
   /**
     * Update the specified sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $sale = Sale::find($id);
            if($request->free){
                $sale->prospect=$request->prospect;
                $sale->customer_id=null;
            }else{
                $customer=Customer::find($request->customer_id);
                $sale->customer_id=$request->customer_id;
                $sale->prospect=null;
            }
            if($sale->type=='C'){
                ($sale->custom_budget_folio)?$sale->budget_folio=$request->folio:'';
            }else{
                ($sale->custom_sale_folio)?$sale->sale_folio=$request->folio:'';
            }
            $sale->contact=$request->contact;
            $sale->created_by=Auth::user()->name;
            $sale->date=Carbon::now();
            ($sale->type=='C')?$sale->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date):'';
            $sale->way_pay=($request->way_pay)?$request->way_pay:null;
            $sale->method_pay=($request->method_pay)?$request->method_pay:null;
            $sale->condition_pay=($request->condition_pay)?$request->condition_pay:null;
            $sale->observations=($request->observations)?$request->observations:null;
            $sale->conditions=($request->conditions)?$request->conditions:null;
            $sale->save();
            $sale->items()->delete();

            $sub_total=0;
            $total_discount=0;
            $total_tax=0;
            $total=0;

            //$array_descriptions=$request->descriptions;
            $array_ids=$request->ids;
            $array_types=$request->types;
            $array_quantities=$request->quantities;
            $array_unit_prices=$request->unit_prices;
            $array_sub_totals=$request->sub_totals;
            $array_percent_discounts=$request->percent_discounts;
            $array_discounts=$request->discounts;
            $array_percent_taxes=$request->percent_taxes;
            $array_taxes=$request->taxes;
            $array_totals=$request->totals;
            
            for ($i=0; $i < count($array_types) ; $i++) { 
                $sale_item=new SaleItem();
                $sale_item->sale_id=$sale->id;
                $sale_item->item_id=$array_ids[$i];
                $sale_item->type=$array_types[$i];
                $sale_item->unit_price=$array_unit_prices[$i];
                $sale_item->quantity=$array_quantities[$i];
                $sale_item->sub_total=$sale_item->unit_price*$sale_item->quantity;
                $sale_item->percent_discount=$array_percent_discounts[$i];
                $sale_item->discount=$sale_item->sub_total*($sale_item->percent_discount/100);
                $sale_item->percent_tax=$array_percent_taxes[$i];
                $sale_item->tax=($sale_item->sub_total-$sale_item->discount)*($sale_item->percent_tax/100);
                $sale_item->total=$sale_item->sub_total-$sale_item->discount+$sale_item->tax;
                $sale_item->save();
                $sub_total+=$sale_item->sub_total;
                $total_discount+=$sale_item->discount;
                $total_tax+=$sale_item->tax;
                $total+=$sale_item->total;                
            }

            $sale->sub_total=$sub_total;
            $sale->total_discount=$total_discount;
            $sale->total_tax=$total_tax;
            $sale->total=$total;
            $sale->save();
            $sale['items']=$sale->items()->get();
            
            if($request->send_email){
                $array_emails=$request->to;
                for ($i=0; $i < count($array_emails); $i++) { 
                    $email=$array_emails[$i];
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $this->send_email($sale->id,$email);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => (($sale->type=='C')?'Cotización':'Factura').' actualizada exitosamente',
                'sale' => $sale,
                'base64'=> $this->base64($sale->id)                
            ]);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified sale from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $sale = Sale::find($id);
            $sale->delete();
            
            return response()->json([
                'success' => true,
                'message' => (($sale->type=='C')?'Cotización':'Factura').' eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
      
    public function sale_pdf($id)
    {        
        $sale=Sale::find($id);
        $setting = SaleSetting::where('subscriber_id', $sale->subscriber_id)->first();
        //$logo=($sale->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($sale->subscriber_id.'/'.$sale->subscriber->logo)):'';
        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';

        $sello=($sale->subscriber->stamp)?'data:image/png;base64, '.base64_encode(Storage::get($sale->subscriber_id.'/'.$sale->subscriber->stamp)):'';
        
        $company=$setting->company;
        
        $data=[
            //'sale_setting' => $sale_setting,
            //'header' => $header,
            //'footer' => $footer,
            'setting' => $setting,
            'sale' => $sale,
            'sello' => $sello,
            'subscriber' => $sale->subscriber,            
            'logo' => $logo,
            'root_public' => realpath(public_path()),
            'root_storage' => realpath(storage_path())
        ];
                
        return $pdf = PDF::loadView('reports/rpt_sale', $data);
    }    

    public function base64($id)
    {        
        $pdf=$this->sale_pdf($id);
        return base64_encode($pdf->output());        
    }
    
    public function rpt_sale($id){
        
        $sale=Sale::find(Crypt::decrypt($id));
        $pdf=$this->sale_pdf($sale->id);
        
        return $pdf->stream((($sale->type=='C')?'Cotización':'Factura').' Folio '.$sale->folio.'.pdf');
    }
    
    public function download_sale($id){
        
        $sale=Sale::find($id);
        $pdf=$this->sale_pdf($sale->id);

        return $pdf->download((($sale->type=='C')?'Cotización':'Factura').' Folio '.$sale->folio.'.pdf');
    }

    public function load_send_modal($id)
    {
        $sale = Sale::find($id);
        
        return view('sales.send')->with('sale', $sale);
    }
    
    public function send_email($id, $to){
                    
        try {
            $sale=Sale::find($id);
            $pdf=$this->sale_pdf($id);
            $file = $pdf->output();

            Mail::to($to)->send(new SaleNotification($sale, $file));
            
            return response()->json([
                'success' => true,
                'message' => (($sale->type=='C')?'Cotización':'Factura').' enviada exitosamente'
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
        $sale = Sale::find($id);
        
        return view('sales.convert')->with('sale', $sale);
    }
    
    public function convert(Request $request, $id){
                    
        try {
            $sale=Sale::find($id);
            $sale->type='F';
            $sale->date=Carbon::now();
            $sale->custom_sale_folio=$request->custom_folio;
            if($sale->custom_sale_folio){
                $sale->sale_folio=$request->folio;
            }else{
                $sale->sale_number=Sale::where('subscriber_id', $sale->subscriber_id)->max('sale_number')+1;
            }
            $sale->way_pay=$request->way_pay;
            $sale->method_pay=$request->method_pay;
            $sale->condition_pay=$request->condition_pay;
            $sale->save();
            ($request->send_email)?$this->send_email($sale->id,$request->to):'';
            
            return response()->json([
                'success' => true,
                'message' => 'Cotización convertida exitosamente',
                'sale' => $sale
            ], 200);
        
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }

    }

    public function rpt_sales($type)
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $sales=$this->subscriber->sales()->where('type', $type)
                                ->orderBy('date', 'desc')
                                ->orderBy('id', 'desc')->get();

        $data=[
            'company' => $company,
            'sales' => $sales,
            'type' => $type,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_sales', $data);
        
        return $pdf->stream(($type=='F')?'Facturas.pdf':'Cotizaciones.pdf');
    }

    public function xls_sales(Request $request, $type)
    {        
        return Excel::download(new SalesExport($this->subscriber, $type), ($type=='F')?'Facturas.xlsx':'Cotizaciones.xlsx');        
    }
    
    /**
     * Display a listing of the sale.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {                
        if(SaleSetting::where('subscriber_id', $this->subscriber->id)->exists()){
            $setting=SaleSetting::where('subscriber_id', $this->subscriber->id)->first();
        }else{
            $setting=new SaleSetting();
            $setting->subscriber_id=$this->subscriber->id;
            $setting->save();
        }

        return view('sales.settings')->with('setting', $setting);
    }

    public function update_settings(Request $request, $id)
    {
        try {
            
            $setting = SaleSetting::find($id);        
            $setting->show_coin_name= $request->show_coin_name;
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

}
