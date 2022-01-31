<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'targets';
    
    //*** Relations ***   
    public function customers()
    {
        return $this->hasMany('App\Models\Customer');
    }

    public function suppliers()
    {
        return $this->hasMany('App\Models\Supplier');
    }

    //*** Methods ***
    
    //*** Accesors ***    
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
