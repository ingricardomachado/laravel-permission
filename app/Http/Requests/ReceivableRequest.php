<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class ReceivableRequest extends FormRequest
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
        $rules = [
            'balance' => 'lte:amount',
            'close_date' => 'gte:date'
        ];         
        
        return $rules;
    }

    public function messages()
    {
        return [
            'balance.lte' => 'El balance debe ser menor o igual al monto',
        ];
    }
}
    