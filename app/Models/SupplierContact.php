<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    protected $table = 'supplier_contact';
    
    //*** Relations ***
    public function supplier(){
   
        return $this->belongsTo('App\Models\Supplier');
    }


    //*** Accesors ***   

}
