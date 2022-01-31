<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $table = 'customer_document';
    
    //*** Relations ***
    public function customer(){
   
        return $this->belongsTo('App\Models\Customer');
    }


    //*** Accesors ***   

}
