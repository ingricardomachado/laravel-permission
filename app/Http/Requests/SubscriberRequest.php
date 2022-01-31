<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Session;
use App\Models\Subscriber;

class SubscriberRequest extends FormRequest
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
        $subscriber=Subscriber::find($subscriber_id);
        if($subscriber_id>0){
            $rules['email'] = 'email|max:50|unique:users,email,'.$subscriber->user_id;
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
          'password.confirmed'  => 'La confirmación de la contraseña no coincide.'
        ];
    }
}
