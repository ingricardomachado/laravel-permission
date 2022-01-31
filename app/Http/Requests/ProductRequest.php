<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class ProductRequest extends FormRequest
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
        $product_id=$this->request->get('product_id');

        if($product_id>0){
            $rules['name'] = 'required|unique:products,name,'.$product_id.',id,category_id, '.$category_id.',subscriber_id,'.$subscriber_id;
        }else{
            $rules['name'] = 'required|unique:products,name,NULL,id,category_id, '.$category_id.',subscriber_id,'.$subscriber_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El producto ya existe para esa categoría',
        ];
    }
}
