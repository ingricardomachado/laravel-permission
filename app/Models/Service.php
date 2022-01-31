<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    
    //*** Relations ***   
    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
        
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    
    public function getNumberMaskAttribute(){
        return 'S'.$this->number;        
    }
    
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
