<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    //*** Relations ***   
    public function products()
    {
        return $this->hasMany('App\Models\Product')->where('type','P');
    }
        
    public function services()
    {
        return $this->hasMany('App\Models\Product')->where('type','S');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    
    public function getTypeDescriptionAttribute(){
        
        if($this->type=='P'){
            return "Producto";
        }elseif($this->type=='S'){
            return "Servicio";
        }else{
            return $this->type;
        }
    }

    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
