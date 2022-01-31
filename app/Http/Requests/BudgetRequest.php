<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Customer;
use Session;


class SupplierRequest extends FormRequest
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

        $subscriber_id=$this->request->get('subscriber_id');
        $supplier_id=$this->request->get('supplier_id');

        if($supplier_id>0){
            $rules['name'] = 'required|unique:suppliers,name,'.$supplier_id.',id,subscriber_id,'.$subscriber_id;
        }else{
            $rules['name'] = 'required|unique:suppliers,name,NULL,id,subscriber_id,'.$subscriber_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El proveedor ya ha sido registrado',
        ];
    }
}
