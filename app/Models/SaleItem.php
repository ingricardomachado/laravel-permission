<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $table = 'sale_item';
    
    //*** Relations ***
    public function sale()
    {
        return $this->belongsTo('App\Models\Sale');
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
    
    public function getUnitAttribute(){
        if($this->type=='P'){
            return $this->product->unit->unit;
        }elseif($this->type=='S'){
            return $this->service->unit->unit;
        }else{
            return "";
        }
    }

    public function getCodeAttribute(){
        if($this->type=='P'){
            return $this->product->code;
        }elseif($this->type=='S'){
            return $this->service->code;
        }else{
            return "";
        }
    }
}
