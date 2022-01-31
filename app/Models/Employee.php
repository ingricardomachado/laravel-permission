<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    
    //*** Relations ***   
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
        
    //*** Methods ***
    
    //*** Accesors ***    
    public function getNumberMaskAttribute(){
        return 'E'.$this->number;        
    }
    
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
