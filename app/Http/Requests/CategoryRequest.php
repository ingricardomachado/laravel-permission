<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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

        $subscriber_id= $this->request->get('subscriber_id');
        $category_id= $this->request->get('category_id');
        if($category_id>0){
            $rules['name'] = 'required|max:100|unique:categories,name,'.$category_id.',id,subscriber_id,'.$subscriber_id;

        }else{
            $rules['name'] = 'required|max:100|unique:categories,name,NULL,id,subscriber_id,'.$subscriber_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'Ya ha sido registrada una categoría con ese nombre',
          'name.max' => 'La categoría no debe ser mayor que 100 caracteres',          
        ];
    }
}
