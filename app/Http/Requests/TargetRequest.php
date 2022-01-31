<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TargetRequest extends FormRequest
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

        $target_id= $this->request->get('target_id');
        if($target_id>0){
            $rules['name'] = 'max:100|unique:targets,name,'.$target_id;
        }else{
            $rules['name'] = 'max:100|unique:targets';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'Ya ha sido registrado un giro con ese nombre',
          'name.max' => 'El giro no debe ser mayor que 100 caracteres',          
        ];
    }
}
