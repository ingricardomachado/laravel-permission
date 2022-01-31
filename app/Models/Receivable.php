<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $table = 'receivables';
    protected $dates = ['date', 'close_date'];
    
    //*** Relations ***   
    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
        
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    //*** Methods ***
    
    //*** Accesors ***    
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

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
