<?php

namespace App\Models;

use App\Models\InputType;
use App\Models\OutputType;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';
    protected $dates = ['date'];
    
    //*** Relations ***            
    public function subscriber(){
   
        return $this->belongsTo('App\Models\Center');
    }

    public function product(){
   
        return $this->belongsTo('App\Models\Product');
    }

    public function input_type(){
   
        return $this->belongsTo('App\Models\InputType');
    }
    
    public function output_type(){
   
        return $this->belongsTo('App\Models\OutputType');
    }

    //*** Accesors ***
    public function getTypeLabelAttribute(){
                
        if($this->type == 'I'){
            return "<span class='label label-primary' style='font-weight:normal'>Entrada</span>";
        }elseif($this->type == 'O'){
            return "<span class='label label-danger' style='font-weight:normal'>Salida</span>";
        }
    }    

    public function getMovementTypeDescriptionAttribute(){
                
        if($this->type== 'I'){
            $input_type=InputType::find($this->input_type_id);
            return $input_type->name;
        }elseif($this->type == 'O'){
            $output_type=OutputType::find($this->output_type_id);
            return $output_type->name;
        }else{
            return $this->movement_type;
        }
    }    

}
