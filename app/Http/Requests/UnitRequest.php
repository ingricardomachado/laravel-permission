<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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

        $unit_id= $this->request->get('unit_id');
        if($unit_id>0){
            $rules['unit'] = 'max:10|unique:units,unit,'.$unit_id;
        }else{
            $rules['unit'] = 'max:10|unique:units';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'unit.unique' => 'La unidad ya ha sido registrada',
        ];
    }
}
