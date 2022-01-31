<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Customer;
use Session;

class ContactRequest extends FormRequest
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
        $contact_id=$this->request->get('contact_id');

        if($contact_id>0){
            $rules['full_name'] = 'required|unique:contacts,full_name,'.$contact_id.',id,subscriber_id,'.$subscriber_id;
        }else{
            $rules['full_name'] = 'required|unique:contacts,full_name,NULL,id,subscriber_id,'.$subscriber_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'full_name.unique' => 'El contacto ya ha sido registrado',
        ];
    }
}
