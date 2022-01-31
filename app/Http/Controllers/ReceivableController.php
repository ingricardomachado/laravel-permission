<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ReceivableRequest;
use App\User;
use App\Models\Receivable;
use App\Models\Category;
use App\Models\Unit;
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
use App\Exports\ReceivablesExport;
use Carbon\Carbon;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ReceivableController extends Controller
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
     * Display a listing of the receivable.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        $customers=$this->subscriber->customers()->orderBy('name')->pluck('name','id');
        
        return view('receivables.index')->with('customers', $customers);
    }

    public function datatable(Request $request)
    {        
        $customer_filter=$request->customer_filter;

        if($customer_filter!=''){
            $receivables = $this->subscriber->receivables()
                        ->join('customers', 'receivables.customer_id', '=', 'customers.id')
                        ->where('receivables.customer_id', $customer_filter)
                        ->select(['receivables.*', 'customers.name as customer']);
        }else{
            $receivables = $this->subscriber->receivables()
                        ->join('customers', 'receivables.customer_id', '=', 'customers.id')
                        ->select(['receivables.*', 'customers.name as customer']);
        }

                            ;                
        return Datatables::of($receivables)
            ->addColumn('action', function ($receivable) {
                    return '
                        <div class="input-group-prepend">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalReceivable('.$receivable->id.')">Editar</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$receivable->id.'`, `'.$receivable->name.'`, `'.$receivable->credit_points.'`, `'.$receivable->debit_points.'`)">Eliminiar</a>                                
                            </div>
                        </div>';
            })           
            ->editColumn('number', function ($receivable) {                    
                    return '<b>'.$receivable->number.'</b>';
                })
            ->editColumn('date', function ($receivable) {                    
                    return $receivable->date->format('d/m/Y');
                })
            ->editColumn('close_date', function ($receivable) {                    
                    return ($receivable->close_date)?$receivable->close_date->format('d/m/Y'):'';
                })
            ->editColumn('amount', function ($receivable) {                    
                    return money_fmt($receivable->amount);
                })
            ->editColumn('balance', function ($receivable) {                    
                    return money_fmt($receivable->balance);
                })
            ->editColumn('way_pay', function ($receivable) {                    
                    return $receivable->way_pay_description;
                })
            ->editColumn('method_pay', function ($receivable) {                    
                    return $receivable->method_pay_description;
                })
            ->editColumn('condition_pay', function ($receivable) {                    
                    return $receivable->condition_pay_description;
                })
            ->rawColumns(['action', 'number', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified receivable.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $customers= $this->subscriber->customers()
                                ->where('active',true)
                                ->orderBy('name')->pluck('name','id');

        if($id==0){
            $receivable = new Receivable();
        }else{
            $receivable = Receivable::find($id);
        }
        
        return view('receivables.save')->with('receivable', $receivable)
                        ->with('customers', $customers);
    }

    /**
     * Store a newly created receivable in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceivableRequest $request)
    {
        try {
            $customer=Customer::findOrFail($request->customer);
            $receivable = new Receivable();
            $receivable->number=Receivable::where('subscriber_id', $customer->subscriber_id)->max('number')+1;
            $receivable->subscriber_id=$customer->subscriber_id;
            $receivable->customer_id=$customer->id;
            $receivable->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $receivable->folio=$request->folio;
            $receivable->amount=$request->amount;
            $receivable->balance=$request->balance;
            $receivable->way_pay=$request->way_pay;
            $receivable->method_pay=$request->method_pay;
            $receivable->condition_pay=$request->condition_pay;
            $receivable->days=$request->days;
            $receivable->description=$request->description;
            $receivable->close_date=Carbon::createFromFormat('d/m/Y', $request->close_date);
            $receivable->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Cuenta por Cobrar registrada exitosamente',
                    'receivable' => $receivable
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
        $receivable = Receivable::find($id);
        
        if($receivable){
            return response()->json([
                    'success' => true,
                    'receivable' => $receivable
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified receivable in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReceivableRequest $request, $id)
    {
        try {
            $receivable = Receivable::findOrfail($id);
            $receivable->customer_id=$request->customer;
            $receivable->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $receivable->folio=$request->folio;
            $receivable->amount=$request->amount;
            $receivable->balance=$request->balance;
            $receivable->way_pay=$request->way_pay;
            $receivable->method_pay=$request->method_pay;
            $receivable->condition_pay=$request->condition_pay;
            $receivable->days=$request->days;
            $receivable->description=$request->description;
            $receivable->close_date=Carbon::createFromFormat('d/m/Y', $request->close_date);
            $receivable->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Cuenta por Cobrar actualizada exitosamente',
                    'receivable' => $receivable
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified receivable from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $receivable = Receivable::findOrFail($id);
            $receivable->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cuenta por Cobrar eliminada exitosamente'
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
            $receivable = Receivable::find($id);
            $receivable->active=($receivable->active)?false:true;
            $receivable->save();

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
    
    public function rpt_receivables()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $receivables=$this->subscriber->receivables()->orderBy('date', 'desc')->get();

        $data=[
            'company' => $company,
            'receivables' => $receivables,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_receivables', $data);
        
        return $pdf->stream('Cuentas por Cobrar.pdf');
    }

    public function xls_receivables(Request $request)
    {        
        return Excel::download(new ReceivablesExport($this->subscriber), 'Cuentas por Cobrar.xlsx');        
    }

    public function receivables(){
        return Receivable::orderBy('name')->get();            
    }
}
