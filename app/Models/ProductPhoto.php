<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    protected $table = 'product_photo';
    
    //*** Relations ***            
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    //*** Accesors ***
}
