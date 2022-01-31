<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $dates = ['date', 'due_date'];
    
    //*** Relations ***            
    public function customer()
    {        
        return $this->belongsTo('App\Models\Customer');
    }

    public function order()
    {        
        return $this->belongsTo('App\Models\Order');
    }

    public function items()
    {        
        return $this->hasMany('App\Models\SaleItem');
    }

    public function products()
    {        
        return $this->hasMany('App\Models\SaleItem', 'item_id')->where('type', 'P');
    }

    public function services()
    {        
        return $this->hasMany('App\Models\SaleItem', 'item_id')->where('type', 'S');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    
    public function getCustomFolioAttribute(){
        if($this->type=='C'){
            return ($this->custom_sale_folio)?1:0;
        }elseif($this->type=='F'){
            return ($this->custom_sale_folio)?1:0;
        }else{
            return false;
        }
    }


    public function getFolioAttribute(){
        if($this->type=='C'){
            return ($this->custom_budget_folio)?$this->budget_folio:$this->budget_number;
        }elseif($this->type=='F'){
            return ($this->custom_sale_folio)?$this->sale_folio:$this->sale_number;
        }else{
            return "";
        }
    }
    
    public function getWayPayDescriptionAttribute(){
        
        switch ($this->way_pay) {
            case 1:
                return "Efectivo";
                break;
            case 2:
                return "Cheque";
                break;
            case 3:
                return "Tarjeta";
                break;
            case 4:
                return "Transferencia";
                break;            
            default:
                return $this->way_pay;
                break;
        }
    }

    public function getMethodPayDescriptionAttribute(){
        
        switch ($this->method_pay) {
            case 1:
                return "Pago total";
                break;
            case 2:
                return "Pagos parciales";
                break;
            default:
                return $this->method_pay;
                break;
        }
    }

    public function getConditionPayDescriptionAttribute(){
        
        switch ($this->condition_pay) {
            case 1:
                return "Contado";
                break;
            case 2:
                return "Crédito";
                break;
            default:
                return $this->condition_pay;
                break;
        }
    }
}
