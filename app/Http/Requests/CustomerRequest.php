<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Customer;
use Session;


class CustomerRequest extends FormRequest
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

        $customer_id= $this->request->get('customer_id');
        $subscriber_id= $this->request->get('subscriber_id');
        if($customer_id>0){
            $rules['name'] = 'required|unique:customers,name,'.$customer_id.',id,subscriber_id,'.$subscriber_id;            
            if($this->request->get('email')){
                $customer=Customer::find($customer_id);
                ($customer->user_id)?$rules['email'] = 'email|max:50|unique:users,email,'.$customer->user->id:$rules['email'] = 'email|max:50|unique:users';
            }
            if ($this->request->get('change_password')){        
                $rules['password'] = 'nullable|required_with:password_confirmation|string|confirmed';
            }
        }else{
            $rules['name'] = 'required|unique:customers,name,NULL,id,subscriber_id,'.$subscriber_id;
            ($this->request->get('email'))?$rules['email'] = 'email|max:50|unique:users':'';
            $rules['password'] = 'nullable|required_with:password_confirmation|string|confirmed';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El cliente ya ha sido registrado',
          'email.unique' => 'El correo ya ha sido registrado',
        ];
    }
}
