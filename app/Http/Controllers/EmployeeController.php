<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EmployeeRequest;
use App\User;
use App\Models\Employee;
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
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class EmployeeController extends Controller
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
     * Display a listing of the employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('employees.index');
    }

    public function datatable()
    {        

        $employees = $this->subscriber->employees();        
        
        return Datatables::of($employees)
            ->addColumn('action', function ($employee) {
                if(session()->get('role')=='ADM'){
                    if($employee->active){
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_cancel" onclick="showModalEmployee('.$employee->id.')">Editar</a>
                                    <a class="dropdown-item" href="#" name="href_status" onclick="change_status('.$employee->id.')">Desactivar</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showModalDelete(`'.$employee->id.'`, `'.$employee->name.'`, `'.$employee->credit_points.'`, `'.$employee->debit_points.'`)">Eliminiar</a>                                
                                </div>
                            </div>';
                    }else{
                        return '
                            <div class="input-group-prepend">
                                <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" name="href_status" class="modal-class" onclick="change_status('.$employee->id.')"> Activar</a>
                                </div>
                            </div>';
                    }
                }else{
                    return "";
                }
                })           
            ->editColumn('number', function ($employee) {                    
                    return '<b>'.$employee->number_mask.'</b>';
                })
            ->editColumn('full_name', function ($employee) {                    
                    return '<a href="#"  onclick="showModalEmployee('.$employee->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$employee->full_name.'<br><small>'.$employee->email.'</small></a>';
                })
            ->editColumn('status', function ($employee) {                    
                    return $employee->status_label;
                })
            ->editColumn('role', function ($employee) {                    
                    return $employee->user->role_description;
                })
            ->rawColumns(['action', 'number', 'full_name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries= Country::orderBy('name')->pluck('name','id');
        $roles= Role::where('subscriber_id', $this->subscriber->id)
                    ->where('aliase', '!=' ,'CLI')
                    ->orderBy('description')->pluck('description','name');

        if($id==0){
            $employee = new Employee();
            $states= State::where('country_id', 1)->orderBy('name')->pluck('name','id');

        }else{
            $employee = Employee::find($id);
            $states= State::where('country_id', $employee->country_id)->orderBy('name')->pluck('name','id');
        }
        
        return view('employees.save')->with('subscriber', $this->subscriber)
                                ->with('roles', $roles)
                                ->with('employee', $employee)
                                ->with('countries', $countries)
                                ->with('states', $states);
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        try {
            if(true){
                $user= new User();
                $user->PIN=rand(1000,9999);
                $user->subscriber_id=$request->subscriber_id;
                $user->name= $request->first_name.' '.$request->last_name;
                $user->email= $request->email;
                $user->password= password_hash($request->password, PASSWORD_DEFAULT);
                $user->email_notification=0;
                $user->save();
            }            
            $user->assignRole($request->role);
            $employee = new Employee();
            $employee->number=Employee::where('subscriber_id', $request->subscriber_id)->max('number')+1;
            $employee->subscriber_id=$request->subscriber_id;
            $employee->user_id=$user->id;
            $employee->pin=($request->pin)?$request->pin:null;
            $employee->first_name=$request->first_name;
            $employee->last_name=$request->last_name;
            $employee->full_name=$employee->first_name.' '.$employee->last_name;
            $employee->country_id=$request->country;
            $employee->state_id=$request->state;
            $employee->city=$request->city;
            $employee->address=$request->address;
            $employee->email=($request->email)?$request->email:null;
            $employee->cell=($request->cell)?$request->cell:null;
            $employee->phone=($request->phone)?$request->phone:null;
            $employee->field1=($request->field1)?$request->field1:null;
            $employee->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Empleado registrado exitosamente',
                    'employee' => $employee
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
        $employee = Employee::find($id);
        
        if($employee){
            return response()->json([
                    'success' => true,
                    'employee' => $employee
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::find($id);           
            $employee->pin=($request->pin)?$request->pin:null;
            $employee->first_name=$request->first_name;
            $employee->last_name=$request->last_name;
            $employee->full_name=$employee->first_name.' '.$employee->last_name;
            $employee->country_id=$request->country;
            $employee->state_id=$request->state;
            $employee->city=$request->city;
            $employee->address=$request->address;
            $employee->email=($request->email)?$request->email:null;
            $employee->cell=($request->cell)?$request->cell:null;
            $employee->phone=($request->phone)?$request->phone:null;
            $employee->field1=($request->field1)?$request->field1:null;
            if($employee->user_id){
                $employee->user->name=$employee->full_name;
                $employee->user->email=$employee->email;
                $employee->user->syncRoles([$request->role]);
                if($request->change_password){
                    $employee->user->password=password_hash($request->password, PASSWORD_DEFAULT);
                }
                $employee->user->save();
            }
            $employee->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Empleado actualizado exitosamente',
                    'employee' => $employee
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);
            if($employee->user_id){
                $user=User::find($employee->user_id);
                $user->roles()->detach();
                $user-> forgetCachedPermissions();                
                $user->delete();                
            }
            $employee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente'
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
            $employee = Employee::find($id);
            if($employee->active){
                $employee->active=false;
                $employee->user->active=false;    
            }else{
                $employee->active=true;
                $employee->user->active=true;
            }
            $employee->user->save();
            $employee->save();

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
    
    public function rpt_employees()
    {        
        $logo=($this->subscriber->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->subscriber->id.'/'.$this->subscriber->logo)):'';
        $company=$this->subscriber->bussines_name;
        
        $employees=$this->subscriber->employees()->orderBy('full_name')->get();

        $data=[
            'company' => $company,
            'employees' => $employees,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_employees', $data);
        
        return $pdf->stream('Empleados.pdf');

    }

    public function xls_employees(Request $request)
    {        
        return Excel::download(new EmployeesExport($this->subscriber), 'Empleados.xlsx');        
    }
    
    public function employees(){
        return Employee::orderBy('full_name')->get();            
    }
}
