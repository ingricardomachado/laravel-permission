<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $dates = ['date', 'due_date'];
    
    //*** Relations ***            
    public function supplier()
    {        
        return $this->belongsTo('App\Models\Supplier');
    }

    public function order()
    {        
        return $this->belongsTo('App\Models\Order');
    }

    public function items()
    {        
        return $this->hasMany('App\Models\PurchaseItem');
    }

    public function products(){

        return $this->items()->where('type','P');
    }

    public function services()
    {        
        return $this->items()->where('type','S');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    
    public function getCustomFolioAttribute(){
        if($this->type=='O'){
            return ($this->custom_purchase_folio)?1:0;
        }elseif($this->type=='C'){
            return ($this->custom_purchase_folio)?1:0;
        }else{
            return false;
        }
    }

    public function getFolioAttribute(){
        if($this->type=='O'){
            return ($this->custom_order_folio)?$this->order_folio:$this->order_number;
        }elseif($this->type=='C'){
            return ($this->custom_purchase_folio)?$this->purchase_folio:$this->purchase_number;
        }else{
            return "";
        }
    }
    
}
