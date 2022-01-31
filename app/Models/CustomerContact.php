<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $table = 'customer_contact';
    
    //*** Relations ***
    public function customer(){
   
        return $this->belongsTo('App\Models\Customer');
    }


    //*** Accesors ***   

}
