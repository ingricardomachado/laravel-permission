<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierDocument extends Model
{
    protected $table = 'supplier_document';
    
    //*** Relations ***
    public function supplier(){
   
        return $this->belongsTo('App\Models\Supplier');
    }


    //*** Accesors ***   

}
