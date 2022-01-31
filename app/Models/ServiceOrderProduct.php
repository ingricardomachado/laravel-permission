<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderProduct extends Model
{
    protected $table = 'service_order_product';
    
    //*** Relations ***
    public function service_order()
    {
        return $this->belongsTo('App\Models\ServiceOrder');
    }
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    //*** Methods ***
    
    //*** Accesors ***
}
