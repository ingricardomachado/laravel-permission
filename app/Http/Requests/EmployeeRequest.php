<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Employee;
use Session;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */    
    public function rules()
    {
        $rules = [];

        $employee_id= $this->request->get('employee_id');
        if($employee_id>0){
            $employee=Employee::find($employee_id);
            $rules['email'] = 'email|max:50|unique:users,email,'.$employee->user->id;
            if ($this->request->get('change_password')){        
                $rules['password'] = 'nullable|required_with:password_confirmation|string|confirmed';
            }
        }else{
            $rules['email'] = 'email|max:50|unique:users';
            $rules['password'] = 'nullable|required_with:password_confirmation|string|confirmed';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'email.unique' => 'El correo ya ha sido registrado',
        ];
    }
}
