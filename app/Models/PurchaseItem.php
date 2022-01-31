<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table = 'purchase_item';
    
    //*** Relations ***
    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'item_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'item_id');
    }

    //*** Methods ***
    
    //*** Accesors ***    
    public function getDescriptionAttribute(){
        if($this->type=='P'){
            return $this->product->name;
        }elseif($this->type=='S'){
            return $this->service->name;
        }else{
            return "";
        }
    }

}
