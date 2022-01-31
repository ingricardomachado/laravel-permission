<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class ServiceRequest extends FormRequest
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
        $category_id=$this->request->get('category');
        $service_id=$this->request->get('service_id');

        if($service_id>0){
            $rules['name'] = 'required|unique:services,name,'.$service_id.',id,category_id, '.$category_id.',subscriber_id,'.$subscriber_id;
        }else{
            $rules['name'] = 'required|unique:services,name,NULL,id,category_id, '.$category_id.',subscriber_id,'.$subscriber_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El servicio ya ha sido registrado para esa categoría',
        ];
    }
}
